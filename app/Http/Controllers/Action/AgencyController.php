<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\CRM_REQUEST;
use App\Models\CRM_REQUEST2;
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

class AgencyController extends Controller
{
    use ActiveUsers;
    use KycVerify;

    protected $loginUserId;

    // Constructor to initialize the property
    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    //Show CRM
    public function showCRM(Request $request)
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

            //Notification Data
            $pending = CRM_REQUEST::all()
                ->where('user_id', $this->loginUserId)
                ->where('status', 'pending')
                ->count();

            $resolved = CRM_REQUEST::all()
                ->where('user_id', $this->loginUserId)
                ->where('status', 'resolved')
                ->count();

            $rejected = CRM_REQUEST::all()
                ->where('user_id', $this->loginUserId)
                ->where('status', 'rejected')
                ->count();

            $total_request = CRM_REQUEST::all()
                ->where('user_id', $this->loginUserId)
                ->count();

            $crm = CRM_REQUEST::where('user_id', $this->loginUserId)
                ->orderBy('id', 'desc')
                ->paginate(5);

            return view('crm')
                ->with(compact('notifications'))
                ->with(compact('pending'))
                ->with(compact('resolved'))
                ->with(compact('rejected'))
                ->with(compact('total_request'))
                ->with(compact('crm'))
                ->with(compact('notifycount'));
        }
    }

    //Submit Request
    public function crmRequest(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|numeric|digits:20',
            'bms_id' => 'required|numeric|digits:8',
        ]);

        //Check if ticket id existed
        // $exist = CRM_REQUEST::where('ticket_no', $request->ticket_id)
        // ->where('bms_ticket_no', $request->bms_id)
        // ->exists();
        $ticketExists = CRM_REQUEST::where('ticket_no', $request->ticket_id)->exists();
        $bmsExists = CRM_REQUEST::where('bms_ticket_no', $request->bms_id)->exists();

        if ($ticketExists || $bmsExists) {
            return redirect()->back()->with('error', 'Sorry  Ticket ID Or BMS ID No already existed!');
        }

        $count = CRM_REQUEST::all()
            ->where('user_id', $this->loginUserId)
            ->where('status', 'pending')
            ->count();

        if ($count == 10) {
            return redirect()->back()->with('error', 'Note: You have reached the maximum limit of '.$count.' Pending requests. Please wait until one of your requests is processed before submitting additional requests. Once a request is completed, you will be able to add more.');
        }

        $ticket_id = $request->ticket_id;
        $bms_id = $request->bms_id;

        // Services Fee
        $ServiceFee = 0;
        $ServiceFee = Services::where('service_code', '110')->first();
        $ServiceFee = $ServiceFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $ServiceFee) {
            return redirect()->back()->with('error', 'Sorry Wallet Not Sufficient for Transaction !');
        } else {

            $balance = $wallet->balance - $ServiceFee;

            $affected = Wallet::where('user_id', $this->loginUserId)
                ->update(['balance' => $balance]);

            $referenceno = '';
            srand((float) microtime() * 1000000);
            $gen = '123456123456789071234567890890';
            $gen .= 'aBCdefghijklmn123opq45rs67tuv89wxyz'; // if you need alphabatic also
            $ddesc = '';
            for ($i = 0; $i < 12; $i++) {
                $referenceno .= substr($gen, (rand() % (strlen($gen))), 1);
            }

            $payer_name = auth()->user()->first_name.' '.Auth::user()->last_name;
            $payer_email = auth()->user()->email;
            $payer_phone = auth()->user()->phone_number;

            $trx_id = Transaction::create([
                'user_id' => $this->loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'CRM Request',
                'service_description' => 'Wallet debitted with a Request fee of '.number_format($ServiceFee, 2),
                'amount' => $ServiceFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            $trx_id = $trx_id->id;

            CRM_REQUEST::create([
                'user_id' => $this->loginUserId,
                'tnx_id' => $trx_id,
                'refno' => $referenceno,
                'bms_ticket_no' => $bms_id,
                'ticket_no' => $ticket_id,
            ]);

            //Notifocation
            //In App Notification
            Notification::create([
                'user_id' => $this->loginUserId,
                'message_title' => 'CRM Request',
                'messages' => 'Wallet debitted with a Request fee of '.number_format($ServiceFee, 2),
            ]);

            $successMessage = 'CRM Request was successfully';

            return redirect()->back()->with('success', $successMessage);

        }

    }

    public function showCRM2(Request $request)
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

            //Notification Data
            $pending = CRM_REQUEST2::all()
                ->where('user_id', $this->loginUserId)
                ->where('status', 'pending')
                ->count();

            $resolved = CRM_REQUEST2::all()
                ->where('user_id', $this->loginUserId)
                ->where('status', 'resolved')
                ->count();

            $rejected = CRM_REQUEST2::all()
                ->where('user_id', $this->loginUserId)
                ->where('status', 'rejected')
                ->count();

            $total_request = CRM_REQUEST2::all()
                ->where('user_id', $this->loginUserId)
                ->count();

            $crm = CRM_REQUEST2::where('user_id', $this->loginUserId)
                ->orderBy('id', 'desc')
                ->paginate(5);

            return view('crm2')
                ->with(compact('notifications'))
                ->with(compact('pending'))
                ->with(compact('resolved'))
                ->with(compact('rejected'))
                ->with(compact('total_request'))
                ->with(compact('crm'))
                ->with(compact('notifycount'));
        }
    }

    //Submit Request
    public function crmRequest2(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|numeric|digits:11',
            // 'dob' =>  'required|numeric|digits:8',
        ]);

        //Check if ticket id existed
        $exist = CRM_REQUEST2::where('phoneno', $request->phone_number)
            ->where('dob', $request->dob)
            ->where('status', 'pending')
            ->exists();
        if ($exist) {
            return redirect()->back()->with('error', 'Sorry Request Already exist!');
        }

        $count = CRM_REQUEST2::all()
            ->where('user_id', $this->loginUserId)
            ->where('status', 'pending')
            ->count();

        if ($count == 10) {
            return redirect()->back()->with('error', 'Note: You have reached the maximum limit of '.$count.' Pending requests. Please wait until one of your requests is processed before submitting additional requests. Once a request is completed, you will be able to add more.');
        }

        $phoneno = $request->phone_number;
        $dob = $request->dob;

        // Services Fee
        $ServiceFee = 0;
        $ServiceFee = Services::where('service_code', '111')->first();
        $ServiceFee = $ServiceFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $ServiceFee) {
            return redirect()->back()->with('error', 'Sorry Wallet Not Sufficient for Transaction !');
        } else {

            $balance = $wallet->balance - $ServiceFee;

            $affected = Wallet::where('user_id', $this->loginUserId)
                ->update(['balance' => $balance]);

            $referenceno = '';
            srand((float) microtime() * 1000000);
            $gen = '123456123456789071234567890890';
            $gen .= 'aBCdefghijklmn123opq45rs67tuv89wxyz'; // if you need alphabatic also
            $ddesc = '';
            for ($i = 0; $i < 12; $i++) {
                $referenceno .= substr($gen, (rand() % (strlen($gen))), 1);
            }

            $payer_name = auth()->user()->first_name.' '.Auth::user()->last_name;
            $payer_email = auth()->user()->email;
            $payer_phone = auth()->user()->phone_number;

            $trx_id = Transaction::create([
                'user_id' => $this->loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'CRM Request WPD',
                'service_description' => 'Wallet debitted with a Request fee of '.number_format($ServiceFee, 2),
                'amount' => $ServiceFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            $trx_id = $trx_id->id;

            CRM_REQUEST2::create([
                'user_id' => $this->loginUserId,
                'tnx_id' => $trx_id,
                'refno' => $referenceno,
                'phoneno' => $phoneno,
                'dob' => $dob,
            ]);

            //Notifocation
            //In App Notification
            Notification::create([
                'user_id' => $this->loginUserId,
                'message_title' => 'CRM Request WPD',
                'messages' => 'Wallet debitted with a Request fee of '.number_format($ServiceFee, 2),
            ]);

            $successMessage = 'CRM Request WPD was successfully';

            return redirect()->back()->with('success', $successMessage);

        }

    }

    public function showBVN(Request $request)
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

            //Notification Data
            $pending = DB::table('bvn_modifications')
                ->where('user_id', $this->loginUserId)
                ->where('status', 'pending')
                ->count();

            $resolved = DB::table('bvn_modifications')
                ->where('user_id', $this->loginUserId)
                ->where('status', 'resolved')
                ->count();

            $rejected = DB::table('bvn_modifications')
                ->where('user_id', $this->loginUserId)
                ->where('status', 'rejected')
                ->count();

            $total_request = DB::table('bvn_modifications')
                ->where('user_id', $this->loginUserId)
                ->count();

            $mod = DB::table('bvn_modifications')->where('user_id', $this->loginUserId)
                ->orderBy('id', 'desc')
                ->paginate(5);

            return view('bvn-mod')
                ->with(compact('notifications'))
                ->with(compact('pending'))
                ->with(compact('resolved'))
                ->with(compact('rejected'))
                ->with(compact('total_request'))
                ->with(compact('mod'))
                ->with(compact('notifycount'));
        }
    }

    public function bvnModRequest(Request $request)
    {

        $request->validate([
            'bvn_number' => 'required|numeric|digits:11',
            'enrollment_center' => ['required', 'string'],
            'field_to_modify' => ['required', 'string'],
            'data_to_modify' => ['required', 'string'],
            'documents' => 'required|file|mimes:pdf|max:10240', // Max size 10MB
        ]);

        // Retrieve the validated file
        $file = $request->file('documents');

        // Generate a unique file name or use the original name
        $fileName = time().'_'.$file->getClientOriginalName();

        // Move the file to the storage path
        $filePath = $file->storeAs('Documents', $fileName, 'public');

        //Check if ticket id existed
        $exist = DB::table('bvn_modifications')
            ->where('type', $request->field_to_modify)
            ->where('status', 'pending')
            ->exists();
        if ($exist) {
            return redirect()->back()->with('error', 'Your request is being processed. We appreciate your patience and will respond as soon as possible.');
        }

        $count = DB::table('bvn_modifications')
            ->where('user_id', $this->loginUserId)
            ->where('status', 'pending')
            ->count();

        if ($count == 10) {
            return redirect()->back()->with('error', 'Note: You have reached the maximum limit of '.$count.' Pending requests. Please wait until one of your requests is processed before submitting additional requests. Once a request is completed, you will be able to add more.');
        }

        // Services Fee
        $ServiceFee = 0;
        $ServiceFee = Services::where('service_code', '112')->first();
        $ServiceFee = $ServiceFee->amount;

        //Check if wallet is funded
        $wallet = Wallet::where('user_id', $this->loginUserId)->first();
        $wallet_balance = $wallet->balance;
        $balance = 0;

        if ($wallet_balance < $ServiceFee) {
            return redirect()->back()->with('error', 'Sorry Wallet Not Sufficient for Transaction !');
        } else {

            $balance = $wallet->balance - $ServiceFee;

            $affected = Wallet::where('user_id', $this->loginUserId)
                ->update(['balance' => $balance]);

            $referenceno = '';
            srand((float) microtime() * 1000000);
            $gen = '123456123456789071234567890890';
            $gen .= 'aBCdefghijklmn123opq45rs67tuv89wxyz'; // if you need alphabatic also
            $ddesc = '';
            for ($i = 0; $i < 12; $i++) {
                $referenceno .= substr($gen, (rand() % (strlen($gen))), 1);
            }

            $payer_name = auth()->user()->first_name.' '.Auth::user()->last_name;
            $payer_email = auth()->user()->email;
            $payer_phone = auth()->user()->phone_number;

            $trx_id = Transaction::create([
                'user_id' => $this->loginUserId,
                'payer_name' => $payer_name,
                'payer_email' => $payer_email,
                'payer_phone' => $payer_phone,
                'referenceId' => $referenceno,
                'service_type' => 'BVN Modification Request',
                'service_description' => 'Wallet debitted with a request fee of '.number_format($ServiceFee, 2),
                'amount' => $ServiceFee,
                'gateway' => 'Wallet',
                'status' => 'Approved',
            ]);

            $trx_id = $trx_id->id;

            DB::table('bvn_modifications')->insert([
                'user_id' => $this->loginUserId,
                'tnx_id' => $trx_id,
                'refno' => $referenceno,
                'enrollment_center' => $request->enrollment_center,
                'bvn_no' => $request->bvn_number,
                'type' => $request->field_to_modify,
                'data_to_modify' => $request->data_to_modify,
                'docs' => $filePath,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            //Notifocation
            //In App Notification
            Notification::create([
                'user_id' => $this->loginUserId,
                'message_title' => 'BVN Modification Request',
                'messages' => 'Wallet debitted with a Request fee of '.number_format($ServiceFee, 2),
            ]);

            $successMessage = 'BVN Modification Request was successfully';

            return redirect()->back()->with('success', $successMessage);

        }

    }
}
