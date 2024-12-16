<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Dashboard;
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
    public function show(Request $request)
    {

        $loginUserId = Auth::id();

        $notifications = Notification::where('user_id', $loginUserId)
            ->where('status', 'unread')
            ->latest()
            ->take(3)
            ->get();


        $notifyCount = $notifications->count();

        $this->createAccounts($loginUserId);

        $walletBalance = Wallet::getTotalWalletBalance();

        $bonusBalance = Bonus::getTotalBonusBalance();

        $transactionCount = Transaction::count();

        $userCount = User::count();

        $adminCount = User::where('role', 'admin')->count();

        $agentCount = User::where('role', 'agent')->count();

        $generalUserCount = User::where('role', 'user')->count();

        //Get Virtual Accounts
        $virtualAccountCount = Virtual_Accounts::count();

        //Get Virtual Accounts
        $servicesCount = Services::count();

        //Utility count
         $dashboard = new Dashboard();

         $totalAgencyCounts = $dashboard->getAgencyCounts();

         $totalIdentityCounts = $dashboard->getIdentityCounts();

        // Fetch News
        $newsItems = News::all();

        // Check if the user has notifications enabled
        $notificationsEnabled = Auth::user()->notification;

        // Return View with Data
        return view('dashboard', compact(
            'newsItems',
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
            'notificationsEnabled',
            'totalAgencyCounts',
            'totalIdentityCounts'
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
