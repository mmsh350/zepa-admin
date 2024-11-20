<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Mail\kyc_notify_mail;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ActiveUsers;
use App\Traits\KycVerify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class kycController extends Controller
{
    use ActiveUsers;
    use KycVerify;

    protected $loginUserId;

    // Constructor to initialize the property
    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    public function index()
    {
        //Check if user is Disabled
        if ($this->is_active() != 1) {
            Auth::logout();

            return view('error');
        }

        //Notification Data
        $notifications = Notification::all()->where('user_id', $this->loginUserId)
            ->sortByDesc('id')
            ->where('status', 'unread')
            ->take(3);

        //Notification Count
        $notifycount = 0;
        $notifycount = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->count();

        $pending = User::where('kyc_status', 'Submitted')->count();
        $verified = User::where('kyc_status', 'Verified')->count();
        $rejected = User::where('kyc_status', 'Rejected')->count();

        $users = User::where('kyc_status', 'Submitted')->paginate(1, ['*'], 'table1_page');
        $verifiedUsers = User::where('kyc_status', 'Verified')->paginate(1, ['*'], 'table2_page');
        $rejectedUsers = User::where('kyc_status', 'Rejected')->paginate(1, ['*'], 'table3_page');

        return view('kyc')
            ->with(compact('users'))
            ->with(compact('notifications'))
            ->with(compact('verified'))
            ->with(compact('pending'))
            ->with(compact('rejected'))
            ->with(compact('verifiedUsers'))
            ->with(compact('rejectedUsers'))
            ->with(compact('notifycount'));
    }

    public function kycedUsers(Request $request)
    {

        $id = $request->input('id');

        //Get application details
        $userDetails = User::all()->where('id', $id)->first();

        $created_at = date('M j, Y', strtotime($userDetails->created_at));
        $updated_at = date('M j, Y', strtotime($userDetails->updated_at));

        $data = ['created_at' => $created_at, 'updated_at' => $updated_at];

        $array = array_merge($userDetails->toArray(), $data);

        return response()->json($array);
    }

    public function approveKYC(Request $request)
    {

        $requestID = $request->userid;
        $name = User::where('id', $requestID)->select('first_name')->first();
        $affected = User::where('id', $requestID)
            ->update([
                'kyc_status' => 'Verified',
            ]);

        if ($affected) {
            //Get User Email Id
            $email = $request->email;

            //Send Mail Notification to admin and user
            $mail_data = [
                'type' => 'Verified',
                'name' => $name,
            ];
            try {
                //Send Mail to User and Admin
                $send = Mail::to($email)->send(new kyc_notify_mail($mail_data));
            } catch (TransportExceptionInterface $e) {
            }
        }

        return response()->json(['status' => 200]);
    }

    public function rejectKYC(Request $request)
    {

        $requestID = $request->userid;
        $affected = User::where('id', $requestID)
            ->update([
                'kyc_status' => 'Rejected',
            ]);

        if ($affected) {
            //Get User Email Id
            $email = $request->email;

            //Send Mail Notification to admin and user
            $mail_data = [
                'type' => 'Rejected',
            ];
            try {
                //Send Mail to User and Admin
                $send = Mail::to($email)->send(new kyc_notify_mail($mail_data));
            } catch (TransportExceptionInterface $e) {
            }
        }

        return response()->json(['status' => 200]);
    }
}
