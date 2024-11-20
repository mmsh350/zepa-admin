<?php

namespace App\Http\Controllers\Action;

use App\Helpers\RequestIdHelper;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Traits\ActiveUsers;
use App\Traits\KycVerify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UtilityController extends Controller
{
    use ActiveUsers;
    use KycVerify;

    protected $loginUserId;

    // Constructor to initialize the property
    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    //Show Airtime Page
    public function airtime(Request $request)
    {

        //Check if user is Disabled
        if ($this->is_active() != 1) {
            Auth::logout();

            return view('error');
        }

        //Check if user is Pending, Rejected, or Verified KYC
        $status = $this->is_verified();

        if ($status == 'Pending') {
            return redirect()->route('verification.kyc');

        } elseif ($status == 'Submitted') {
            return view('kyc-status')->with(compact('status'));

        } elseif ($status == 'Rejected') {
            return view('kyc-status')->with(compact('status'));
        } else {

            //Notification Data
            $notifications = Notification::all()->where('user_id', $this->loginUserId)
                ->sortByDesc('id')
                ->where('status', 'unread')
                ->take(3);

            //Notification Count
            $notifycount = 0;
            $notifycount = Notification::all()
                ->where('user_id', $this->loginUserId)
                ->where('status', 'unread')
                ->count();

            $priceList = Services::where('category', 'Airtime')->paginate(8);

            return view('buy-airtime')
                ->with(compact('notifications'))
                ->with(compact('priceList'))
                ->with(compact('notifycount'));
        }
    }

    public function buyAirtime(Request $request)
    {

        $request->validate([
            'network' => ['required', 'string', 'in:mtn,airtel,glo,etisalat'],
            'mobileno' => 'required|numeric|digits:11',
            'amount' => 'required|numeric|min:50|max:5000',
        ]);

        $network = $request->network;
        $amount = $request->amount;
        $mobile = $request->mobileno;
        $requestId = RequestIdHelper::generateRequestId();

        //Minimum Purchase Airtime
        $service_code = '';

        // Use a switch-case to set the service code based on the network
        switch (strtolower($network)) {
            case 'mtn':
                $service_code = '106';
                break;
            case 'airtel':
                $service_code = '107';
                break;
            case 'glo':
                $service_code = '108';
                break;
            case 'etisalat':
                $service_code = '109';
                break;
            default:
                //Do nothing
                break;
        }

        // Services Fee
        $ServiceFee = 0;
        $ServiceFee = Services::where('service_code', $service_code)->first();
        $ServiceFee = $ServiceFee->amount;

        if ($ServiceFee > $amount) {
            return redirect()->back()->with('error', 'Please note that the minimum amount for airtime purchase on the '.$network.' network is ₦'.$amount);
        }

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $amount) {
            return redirect()->back()->with('error', 'Sorry Wallet Not Sufficient for Transaction !');
        } else {
            $response = Http::withHeaders([
                'api-key' => env('API_KEY'),
                'secret-key' => env('SECRET_KEY'),
            ])->post(env('MAKE_PAYMENT'), [
                'request_id' => $requestId,
                'serviceID' => $network,
                'amount' => $amount,
                'phone' => $mobile,
            ]);

            if ($response->successful()) {

                $data = $response->json();

                if ($data['code'] == 000) {
                    // Airtime purchase was successful
                    $balance = $wallet->balance - $amount;

                    $affected = Wallet::where('user_id', $this->loginUserId)
                        ->update(['balance' => $balance]);

                    $payer_name = auth()->user()->first_name.' '.Auth::user()->last_name;
                    $payer_email = auth()->user()->email;
                    $payer_phone = auth()->user()->phone_number;

                    Transaction::create([
                        'user_id' => $this->loginUserId,
                        'payer_name' => $payer_name,
                        'payer_email' => $payer_email,
                        'payer_phone' => $payer_phone,
                        'referenceId' => $requestId,
                        'service_type' => 'Airtime Purchase',
                        'service_description' => strtoupper($network).''.' Airtime purchase of '.number_format($request->amount, 2).' successfully on '.$mobile,
                        'amount' => $request->amount,
                        'gateway' => 'Wallet',
                        'status' => 'Approved',
                    ]);

                    //Notifocation
                    //In App Notification
                    Notification::create([
                        'user_id' => $this->loginUserId,
                        'message_title' => 'Airtime Purchase',
                        'messages' => 'Airtime of ₦'.number_format($request->amount, 2).' was successful',
                    ]);

                    $successMessage = strtoupper($network).''.' Airtime purchase successfully on '.$mobile;

                    return redirect()->back()->with('success', $successMessage);

                } else {

                    return redirect()->back()->with('error', 'Airtime Purchase Failed');
                }
            } else {

                return redirect()->back()->with('error', 'Failed to purchase airtime. Please try again later.');
            }
        }

    }

    //Show Airtime Page
    public function data(Request $request)
    {

        //Check if user is Disabled
        if ($this->is_active() != 1) {
            Auth::logout();

            return view('error');
        }

        //Check if user is Pending, Rejected, or Verified KYC
        $status = $this->is_verified();

        if ($status == 'Pending') {
            return redirect()->route('verification.kyc');

        } elseif ($status == 'Submitted') {
            return view('kyc-status')->with(compact('status'));

        } elseif ($status == 'Rejected') {
            return view('kyc-status')->with(compact('status'));
        } else {

            //Notification Data
            $notifications = Notification::all()->where('user_id', $this->loginUserId)
                ->sortByDesc('id')
                ->where('status', 'unread')
                ->take(3);

            //Notification Count
            $notifycount = 0;
            $notifycount = Notification::all()
                ->where('user_id', $this->loginUserId)
                ->where('status', 'unread')
                ->count();

            //Get the serve name
            $servicename = DB::table('data_variations')->distinct()->limit(6)
                ->get(['service_id', 'service_name']);

            //Price List
            $priceList1 = DB::table('data_variations')->where('service_id', 'mtn-data')->paginate(10, ['*'], 'table1_page');
            $priceList2 = DB::table('data_variations')->where('service_id', 'airtel-data')->paginate(10, ['*'], 'table2_page');
            $priceList3 = DB::table('data_variations')->where('service_id', 'glo-data')->paginate(10, ['*'], 'table3_page');
            $priceList4 = DB::table('data_variations')->where('service_id', 'etisalat-data')->paginate(10, ['*'], 'table4_page');
            $priceList5 = DB::table('data_variations')->where('service_id', 'smile-direct')->paginate(10, ['*'], 'table5_page');
            $priceList6 = DB::table('data_variations')->where('service_id', 'spectranet')->paginate(10, ['*'], 'table6_page');

            return view('buy-data')
                ->with(compact(
                    'notifications',
                    'servicename',
                    'priceList1',
                    'priceList2',
                    'priceList3',
                    'priceList4',
                    'priceList5',
                    'priceList6',
                    'notifycount'
                ));
        }
    }

    public function getVariation(Request $request)
    {

        $response = Http::get(env('VARIATION_URL').$request->type);

        if ($response->successful()) {

            $data = $response->json();
            $service_name = $data['content']['ServiceName'];
            $service_id = $data['content']['serviceID'];
            $convinience_fee = $data['content']['convinience_fee'];

            foreach ($data['content']['varations'] as $variation) {
                DB::table('data_variations')->updateOrInsert(
                    ['variation_code' => $variation['variation_code']],  // Condition to check if record exists
                    ['service_name' => $service_name,
                        'service_id' => $service_id,
                        'convinience_fee' => $convinience_fee,
                        'name' => $variation['name'],
                        'variation_amount' => $variation['variation_amount'],
                        'fixedPrice' => $variation['fixedPrice'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }
        }
    }

    public function buydata(Request $request)
    {

        $request->validate([
            'network' => ['required', 'string', 'in:airtel-data,mtn-data,glo-data,etisalat-data,spectranet,smile-direct'],
            'mobileno' => 'required|numeric|digits:11',
        ]);

        $requestId = RequestIdHelper::generateRequestId();

        //Get service fee
        $fee = DB::table('data_variations')
            ->where('variation_code', $request->bundle)->value('variation_amount');

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $fee) {
            return redirect()->back()->with('error', 'Sorry Wallet Not Sufficient for Transaction !');
        } else {

            $response = Http::withHeaders([
                'api-key' => env('API_KEY'),
                'secret-key' => env('SECRET_KEY'),
            ])->post(env('MAKE_PAYMENT'), [
                'request_id' => $requestId,
                'serviceID' => $request->network,
                'billersCode' => env('BIILER_CODE'),
                'variation_code' => $request->bundle,
                'phone' => $request->mobileno,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['code'] == 000) {
                    // Airtime purchase was successful
                    $balance = $wallet->balance - $fee;

                    $affected = Wallet::where('user_id', $this->loginUserId)
                        ->update(['balance' => $balance]);

                    $payer_name = auth()->user()->first_name.' '.Auth::user()->last_name;
                    $payer_email = auth()->user()->email;
                    $payer_phone = auth()->user()->phone_number;

                    Transaction::create([
                        'user_id' => $this->loginUserId,
                        'payer_name' => $payer_name,
                        'payer_email' => $payer_email,
                        'payer_phone' => $payer_phone,
                        'referenceId' => $requestId,
                        'service_type' => 'Data Purchase',
                        'service_description' => ' Data purchase of '.number_format($fee, 2).' successfully on '.$request->mobileno,
                        'amount' => $fee,
                        'gateway' => 'Wallet',
                        'status' => 'Approved',
                    ]);

                    //In App Notification
                    Notification::create([
                        'user_id' => $this->loginUserId,
                        'message_title' => 'Data Purchase',
                        'messages' => 'Data of ₦'.number_format($fee, 2).' was successful',
                    ]);

                    $successMessage = 'Data purchase successfully on '.$request->mobileno;

                    return redirect()->back()->with('success', $successMessage);

                } else {
                    return redirect()->back()->with('error', 'Data Purchase Failed. Please try again later.');
                }

            } else {

                return redirect()->back()->with('error', 'Failed to purchase data bundle. Please try again later.');
            }
        }

    }

    public function fetchBundles(Request $request)
    {

        $bundles = DB::table('data_variations')
            ->select(['name', 'variation_code'])
            ->where('service_id', $request->id)
            ->get();

        return response()->json($bundles);
    }

    public function fetchBundlePrice(Request $request)
    {

        $priceCollection = DB::table('data_variations')
            ->select('variation_amount')
            ->where('variation_code', $request->id)
            ->get();

        $price = $priceCollection->first()->variation_amount;
        $formattedPrice = number_format((float) $price, 2);

        return response()->json($formattedPrice);
    }
}
