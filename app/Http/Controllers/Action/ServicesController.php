<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\ActiveUsers;
use App\Traits\KycVerify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicesController extends Controller
{
    use ActiveUsers;
    use KycVerify;

    //Show Dashboard
    public function show(Request $request)
    {
        //Login User Id
        $loginUserId = Auth::id();

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
            $notifications = Notification::all()->where('user_id', $loginUserId)
                ->sortByDesc('id')
                ->where('status', 'unread')
                ->take(3);

            //Notification Count
            $notifycount = 0;
            $notifycount = Notification::all()
                ->where('user_id', $loginUserId)
                ->where('status', 'unread')
                ->count();

            $type = $request->name;

            return view('more-services')
                ->with(compact('type'))
                ->with(compact('notifications'))
                ->with(compact('notifycount'));
        }
    }
}
