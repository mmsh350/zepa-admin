<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\VirtualAccountRepository;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Show Dashboard
    public function show(Request $request)
    {
        // Login User Id
        $loginUserId = Auth::id();

        // Fetch Notifications
        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->latest()
            ->take(3)
            ->get();

        // Notification Count
        $notifyCount = $notifications->count();

        // Create Virtual Account and Wallet Account
        $this->createAccounts($loginUserId);

        // Return Wallet Balance
        $walletBalance = Wallet::getTotalWalletBalance();

        // Get Transactions
        $transactions = Transaction::latest()->paginate(10);

        $transactionCount = Transaction::count();

        // Get Total User Count
        $userCount = User::count();

        // Fetch News
        $newsItems = News::all();

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return View with Data
        return view('dashboard', compact(
            'newsItems',
            'transactions',
            'transactionCount',
            'userCount',
            'walletBalance',
            'notifications',
            'notifyCount',
            'notificationsEnabled'
        ));
    }

    // Creating Virtual Accounts
    private function createAccounts($userId)
    {
        $repObj = new VirtualAccountRepository;
        $repObj->createVirtualAccount($userId);

        $repObj2 = new WalletRepository;
        $repObj2->createWalletAccount($userId);
    }
}
