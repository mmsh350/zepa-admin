<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Mail\kyc_notify_mail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class kycController extends Controller
{
    protected $loginUserId;

    public function __construct()
    {
        $this->loginUserId = Auth::id();
    }

    public function index(Request $request)
    {

        // Get the search terms from the request
        $searchTerm = $request->input('search');

        // Fetch Notification Data
        $notifications = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // Count unread notifications
        $notifyCount = Notification::where('user_id', $this->loginUserId)
            ->where('status', 'unread')
            ->count();

        // KYC Status Counts
        $pending = User::where('kyc_status', 'Submitted')->count();
        $verified = User::where('kyc_status', 'Verified')->count();
        $rejected = User::where('kyc_status', 'Rejected')->count();

        $users = User::where('kyc_status', 'Submitted')
            // Filter by email, name, or phone number if searchTerm is provided
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where(function ($query) use ($searchTerm) {
                    $query->where('email', 'like', '%'.$searchTerm.'%')
                        // Filter by name (first, middle, or last name)
                        ->orWhere('first_name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('middle_name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('last_name', 'like', '%'.$searchTerm.'%')
                        // Filter by phone number
                        ->orWhere('phone_number', 'like', '%'.$searchTerm.'%');
                });
            })->excludeAdmin()
            ->paginate(10, ['*'], 'table1_page');

        // Paginate verified and rejected users
        $verifiedUsers = User::where('kyc_status', 'Verified')->paginate(10, ['*'], 'table2_page');
        $rejectedUsers = User::where('kyc_status', 'Rejected')->paginate(10, ['*'], 'table3_page');

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return view with compacted data
        return view('kyc', compact(
            'users',
            'notifications',
            'verified',
            'pending',
            'rejected',
            'notificationsEnabled',
            'verifiedUsers',
            'rejectedUsers',
            'notifyCount'
        ));
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
        $affected = User::where('id', $requestID)
            ->update([
                'kyc_status' => 'Verified',
            ]);

        if ($affected) {
            //Get User Email Id
            $email = $request->email;
            $kycname = User::where('id', $requestID)->value('first_name');
            //Send Mail Notification to admin and user
            $mail_data = [
                'type' => 'Verified',
                'name' => ucwords(strtolower($kycname)),
            ];
            try {
                //Send Mail in response to kyc submitted
                $send = Mail::to($email)->queue(new kyc_notify_mail($mail_data));
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
            // Get User Email
            $email = $request->email;

            // Get User's First Name
            $kycname = User::where('id', $requestID)->value('first_name');

            // Prepare Mail Data
            $mail_data = [
                'type' => 'Rejected',
                'name' => ucwords(strtolower($kycname)),
            ];

            try {
                //Send Mail in response to kyc submitted
                Mail::to($email)->queue(new kyc_notify_mail($mail_data));
            } catch (TransportExceptionInterface $e) {
                // Log the error for debugging
                Log::error('Mail sending failed: '.$e->getMessage());
            }
        }

        return response()->json(['status' => 200]);
    }
}
