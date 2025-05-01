<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $query = User::query()->ExcludeAdmin();

        $loginUserId = Auth::id();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%$search%")
                    ->orWhere('idNumber', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('middle_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('dob', 'like', "%$search%")
                    ->orWhere('gender', 'like', "%$search%")
                    ->orWhere('role', 'like', "%$search%")
                    ->orWhere('referral_code', 'like', "%$search%")
                    ->orWhere('kyc_status', 'like', "%$search%")
                    ->orWhere('daily_limit', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $notifyCount = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->count();

        $notificationsEnabled = Auth::user()->notification;

        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage)->withQueryString();

        return view('users.index', compact('users', 'notifications', 'notifyCount', 'notificationsEnabled'));
    }

    public function show(User $user)
    {

        $loginUserId = Auth::id();

        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $notifyCount = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->count();

        $notificationsEnabled = Auth::user()->notification;

        $transactions = Transaction::where('user_id', $user->id)->latest()->limit(10)->get();

        return view('users.show', compact('user', 'notifications', 'notifyCount', 'notificationsEnabled','transactions'));
    }

    public function edit(User $user)
    {
        $loginUserId = Auth::id();

        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $notifyCount = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->count();

        $notificationsEnabled = Auth::user()->notification;

        return view('users.edit', compact('user', 'notifications', 'notifyCount', 'notificationsEnabled'));
    }

    public function activate(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'User activation status updated.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'role' => 'nullable|in:user,admin,agent',
            'daily_limit' => 'nullable|numeric',
            'referral_code' => 'nullable|string',
            'referral_bonus' => 'nullable|numeric',
            'wallet_balance' => 'nullable|numeric',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!is_null($request->wallet_balance)) {
                $request->validate([
                    'topup_type' => 'required|numeric|in:1,2',
                ]);
         }

        $user->fill($request->only([
            'first_name',
            'last_name',
            'phone_number',
            'email',
            'dob',
            'gender',
            'role',
            'daily_limit',
            'referral_code',
            'referral_bonus'
        ]));

        // Convert image to base64 if uploaded
        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $base64Image = base64_encode(file_get_contents($image->getRealPath()));
            $user->profile_pic = $base64Image;
        }

        // Wallet balance update
        if ($request->wallet_balance) {
            $amount = $request->wallet_balance;

            if ($user->wallet) {


                 if ($request->topup_type == 1){
                        $user->wallet->balance += $amount;
                        $user->wallet->deposit += $amount;
                        $topuptype ='credited';
                 }else{
                        $user->wallet->balance -= $amount;
                        $user->wallet->deposit -= $amount;
                         $topuptype ='debited';
                 }

                $user->wallet->save();

                // Create transaction
                $this->transactionService->createTransaction([
                    'user_id' => $user->id,
                    'payer_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'payer_email' => auth()->user()->email,
                    'payer_phone' => auth()->user()->phone_number,
                    'service_type' => 'Wallet Topup',
                    'service_description' => 'Your wallet has been '.$topuptype.' with ' . '₦' . number_format($amount, 2),
                    'amount' => $amount,
                    'gateWay' => 'Internal',
                    'status' => 'Approved',
                ]);

                // Notify user
                $this->transactionService->createNotification(
                    $user->id,
                    'Wallet Credited',
                    '₦' . number_format($amount, 2) . ' has been credited to your wallet.'
                );
            }
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
}
