<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\News;
use App\Models\Notification;
use App\Models\Services;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Virtual_Accounts;
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

        // Return Wallet Balance
        $bonusBalance = Bonus::getTotalBonusBalance();

        // Get Transactions
        $transactions = Transaction::latest()->paginate(10);

        $transactionCount = Transaction::count();

        // Get Total User Count
        $userCount = User::count();

        // Get Admin Count
        $adminCount = User::where('role', 'admin')->count();

        // Get Agent Count
        $agentCount = User::where('role', 'agent')->count();

        // Get General User Count
        $generalUserCount = User::where('role', 'user')->count();

        //Get Virtual Accounts
        $virtualAccountCount = Virtual_Accounts::count();

        //Get Virtual Accounts
        $servicesCount = Services::count();

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
            'bonusBalance',
            'notifications',
            'notifyCount',
            'adminCount',
            'agentCount',
            'generalUserCount',
            'virtualAccountCount',
            'servicesCount',
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
