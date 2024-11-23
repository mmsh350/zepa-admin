<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'max:6', 'regex:/^[\pL\pN\s\-]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
        ]);

        //Check the referral Code is entered
        $referral_id = null;
        $referral_bonus = 0;
        if (request()->filled('referral_code')) {
            // User entered a non-empty referral code

            if (User::where('referral_code', $request->referral_code)->exists()) {
                //get the referral Id
                $query = User::where('referral_code', $request->referral_code)->first();
                $referral_id = $query->id;
                $referral_bonus = $query->referral_bonus;

                if ($referral_bonus == 0) {
                    //General Bonus
                    $ref_bonus = DB::table('referral_bonus')->select('bonus')->first();
                    if ($ref_bonus) {
                        $referral_bonus = $ref_bonus->bonus;
                    }
                }
            } else {
                // The refcode does not exist in the database
                return response()->json([
                    'message' => 'Invalid Referral Code',
                    'errors' => ['Invalid Code' => 'Invalid Refferal Code, Enter a Valid One to proceed'],
                ], 422);
            }
        }

        //Create my own referral Code
        do {
            $random = md5(uniqid($request->email, true));
            $myOwncode = substr($random, 0, 6);
        } while (User::where('referral_code', $myOwncode)->exists());

        //Create User
        $user = User::create([

            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'referral_code' => ucwords($myOwncode),
            'refferral_id' => $referral_id,
        ]);

        $lastInsertedId = $user->id;

        //Add bonus to account
        $bonus_balance = 0;
        $deposit_balance = 0;
        $wallet_balance = 0;
        $exist = User::where('id', $referral_id)
            ->where('wallet_is_created', 1)
            ->exists();
        if ($exist) {
            $bonus = Bonus::where('user_id', $referral_id)->first();
            $bonus_balance = $bonus->balance + $referral_bonus;
            $deposit_balance = $bonus->deposit + $referral_bonus;

            Bonus::where('user_id', $referral_id)
                ->update(
                    [
                        'balance' => $bonus_balance,
                        'deposit' => $deposit_balance,
                    ]
                );
        }

        //Update notification history
        Notification::create([
            'user_id' => $lastInsertedId,
            'message_title' => 'Welcome Message',
            'messages' => 'Welcome to Zepa Solutions, Your trusted partner for convenient verification,airtime, data, cable, product and services, and ID solutions. etc. ',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return response()->json(['status' => 200, 'redirect_url' => url('kyc')]);
    }
}
