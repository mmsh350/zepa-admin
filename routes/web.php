<?php

use App\Http\Controllers\Action\AgencyController;
use App\Http\Controllers\Action\BankController;
use App\Http\Controllers\Action\BVNController;
use App\Http\Controllers\Action\DashboardController;
use App\Http\Controllers\Action\kycController;
use App\Http\Controllers\Action\NotificationController;
use App\Http\Controllers\Action\ServicesController;
use App\Http\Controllers\Action\TransactionController;
use App\Http\Controllers\Action\UtilityController;
use App\Http\Controllers\Action\WalletController;
use App\Http\Controllers\NIN\NINController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth', 'verified', 'check.admin')->group(function () {

    //General
    Route::post('/read', [NotificationController::class, 'read'])->name('read');

    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');

    //BVN Verify
    Route::get('/bvn', [BVNController::class, 'show'])->name('bvn');
    Route::post('/retrieveBVN', [BVNController::class, 'retrieveBVN'])->name('retrieve-bvn');

    Route::get('/bvn2', [BVNController::class, 'show'])->name('bvn2');
    Route::post('/bvnv2-retrieve', [BVNController::class, 'bvnV2Retrieve'])->name('bvnV2Retrieve');

    //NIN Verification
    Route::get('/nin', [NINController::class, 'show'])->name('nin');
    Route::get('/nin-track', [NINController::class, 'show'])->name('nin-track');
    Route::get('/nin-phone', [NINController::class, 'show'])->name('nin-phone');
    Route::get('/nin-vnin', [NINController::class, 'show'])->name('nin-vnin');

    Route::post('/retrieveNIN', [NINController::class, 'retrieveNIN'])->name('retrieve-nin');
    Route::post('/retrieveNIN2', [NINController::class, 'retrieveNIN2'])->name('retrieve-nin-track');

    //NIN Version 2
    Route::get('/nin2', [NINController::class, 'ninV2Form'])->name('nin2');
    Route::post('/nin2-retrieve', [NINController::class, 'ninV2Retrieve'])->name('ninV2Retrieve');


    //Clain & Transfer
    Route::get('claim', [WalletController::class, 'claim'])->name('claim');
    Route::get('claim-bonus/{id}', [WalletController::class, 'claimBonus'])->name('claim-bonus');
    Route::get('p2p', [WalletController::class, 'p2p'])->name('p2p');
    Route::get('getReciever', [WalletController::class, 'getReciever']);
    Route::get('funding', [WalletController::class, 'funding'])->name('funding');
    Route::post('transfer-funds', [WalletController::class, 'transferP2p'])->name('transfer-funds');

    //Agency Services
    Route::get('crm', [AgencyController::class, 'showCRM'])->name('crm');
    Route::post('/requests/{id}/{type}/update-status', [AgencyController::class, 'updateRequestStatus'])->name('update-request-status');
    Route::get('/view-request/{id}/{type}/edit', [AgencyController::class, 'showRequests'])->name('view-request');

    Route::get('crm2', [AgencyController::class, 'showCRM2'])->name('crm2');
    Route::post('crm-request2', [AgencyController::class, 'crmRequest2'])->name('crmRequest2');

    Route::get('bvn-modification', [AgencyController::class, 'showBVN'])->name('bvn-modification');
    Route::post('modify-bvn', [AgencyController::class, 'bvnModRequest'])->name('modify-bvn');

    Route::get('crm', [AgencyController::class, 'showCRM'])->name('crm');
    Route::post('crm-request', [AgencyController::class, 'crmRequest'])->name('crmRequest');

    Route::get('crm2', [AgencyController::class, 'showCRM2'])->name('crm2');
    Route::post('crm-request2', [AgencyController::class, 'crmRequest2'])->name('crmRequest2');

    Route::get('nin-services', [AgencyController::class, 'ninServices'])->name('nin-services');

    Route::get('vnin-to-nibss', [AgencyController::class, 'vninToNibss'])->name('vnin-to-nibss');

    Route::get('bvn-enrollment', [AgencyController::class, 'showEnrollment'])->name('bvn-enrollment');
    Route::post('request-enrollment', [AgencyController::class, 'bvnEnrollmentRequest'])->name('enroll');
    Route::get('getUserdetails', [WalletController::class, 'getUserdetails']);

    Route::get('account-upgrade', [AgencyController::class, 'showUpgrade'])->name('account-upgrade');

    Route::get('/document/view/{id}/{type}', [AgencyController::class, 'viewDocument'])->name('document.view');
    Route::get('/document2/view/{id}/{type}', [AgencyController::class, 'viewDocument2'])->name('check.docs');

    Route::get('/wema-bank', function () {
        $path = 'docs/wema.pdf';

        return response()->file($path);
    })->name('wema');

    Route::get('/gtb-bank', function () {
        $path = 'docs/gtb.pdf';

        return response()->file($path);
    })->name('gtb');
    //Generate Reciept
    Route::get('/receipt/{referenceId}', [TransactionController::class, 'reciept'])->name('reciept');

    //Whatsapp API Support Routes----------------------------------------------------------------------------------------------
    Route::get('/support', function () {
        $phoneNumber = env('phoneNumber');
        $message = urlencode(env('message'));
        $url = env('API_URL') . "{$phoneNumber}&text={$message}";

        return redirect($url);
    })->name('support');
    //End Whatsapp API Support Routes ------------------------------------------------------------------------------------------

    //PDF Downloads -----------------------------------------------------------------------------------------------------
    Route::get('/standardBVN/{id}', [BVNController::class, 'standardBVN'])->name('standardBVN');
    Route::get('/premiumBVN/{id}', [BVNController::class, 'premiumBVN'])->name('premiumBVN');
    Route::get('/regularSlip/{id}', [NINController::class, 'regularSlip'])->name('regularSlip');
    Route::get('/standardSlip/{id}', [NINController::class, 'standardSlip'])->name('standardSlip');
    Route::get('/premiumSlip/{id}', [NINController::class, 'premiumSlip'])->name('premiumSlip');
    //End PDF Downloads Routes ------------------------------------------------------------------------------------------

    //KYC Routes---------------------------------------------------------------------------------------------------------
    Route::get('/kyc', [kycController::class, 'index'])->name('verification.kyc');
    Route::get('/get-users', [kycController::class, 'kycedUsers']);
    Route::post('/approveKYC', [kycController::class, 'approveKYC']);
    Route::post('/rejectKYC', [kycController::class, 'rejectKYC']);

    //End KYC Routes ------------------------------------------------------------------------------------------

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/pin-verify', [ProfileController::class, 'verifyPin'])->name('pin.verify');
    Route::post('/pin-update', [ProfileController::class, 'updatePin'])->name('pin.update');
    Route::post('/notification', [ProfileController::class, 'update'])->name('notification');
    Route::post('/notification/update', [ProfileController::class, 'notify'])->name('notification.update');

    Route::get('/airtime', [UtilityController::class, 'airtime'])->name('airtime');
    Route::post('/buy-airtime', [UtilityController::class, 'buyAirtime'])->name('buyairtime');

    Route::get('/data', [UtilityController::class, 'data'])->name('data');
    Route::post('/buy-data', [UtilityController::class, 'buydata'])->name('buydata');
    Route::get('/fetch-data-bundles', [UtilityController::class, 'fetchBundles']);
    Route::get('/fetch-data-bundles-price', [UtilityController::class, 'fetchBundlePrice']);

    //Account Upgrade Routes
    Route::get('/upgrade-list', [ProfileController::class, 'upgradeList'])->name('upgrade-list');
    Route::post('/upgrade', [ProfileController::class, 'approveUpgrade'])->name('upgrade');
    Route::post('/rejectUpgrade', [ProfileController::class, 'rejectUpgrade'])->name('rejectUpgrade');

    Route::get('/airtime', [UtilityController::class, 'airtime'])->name('airtime');
    Route::post('/buy-airtime', [UtilityController::class, 'buyAirtime'])->name('buyairtime');

    Route::get('/data', [UtilityController::class, 'data'])->name('data');
    Route::post('/buy-data', [UtilityController::class, 'buydata'])->name('buydata');
    Route::get('/fetch-data-bundles', [UtilityController::class, 'fetchBundles']);
    Route::get('/fetch-data-bundles-price', [UtilityController::class, 'fetchBundlePrice']);

    Route::get('/sme-data', [UtilityController::class, 'sme_data'])->name('sme-data');
    Route::get('/fetch-data-type', [UtilityController::class, 'fetchDataType']);
    Route::get('/fetch-data-plan', [UtilityController::class, 'fetchDataPlan']);
    Route::get('/fetch-sme-data-bundles-price', [UtilityController::class, 'fetchSmeBundlePrice']);
    Route::post('/buy-sme-data', [UtilityController::class, 'buySMEdata'])->name('buy-sme-data');

    Route::get('/education', [UtilityController::class, 'pin'])->name('education');
    Route::post('/buy-pin', [UtilityController::class, 'buypin'])->name('buypin');

    Route::get('/tv', [UtilityController::class, 'showTV'])->name('tv');
    Route::get('/validateno', [UtilityController::class, 'validateno']);

    Route::get('/transactions', [TransactionController::class, 'show'])->name('transactions');

    Route::get('/electricity', [ServicesController::class, 'show'])->name('electricity');

    //Services
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
    Route::post('/services/updateStatus', [ServicesController::class, 'updateStatus'])->name('services.updateStatus');
    Route::put('/services/update/{id}', [ServicesController::class, 'update'])->name('services.update');
    Route::get('/services/create', [ServicesController::class, 'create'])->name('services.create');
    Route::get('/services/edit/{id}', [ServicesController::class, 'edit'])->name('services.edit');


    //SME SERVICES
    Route::get('/sme-service', [ServicesController::class, 'smeIndex'])->name('sme-service');

    Route::post('/services', [ServicesController::class, 'store'])->name('services.store');

    Route::post('/verifyPayments', [WalletController::class, 'verify'])->name('verify');

    //AIRTIME & PRICE UPDATE QUERY move to admin
    Route::get('/bankcodes', [BankController::class, 'pullBankCodes']);
    Route::get('/variation', [UtilityController::class, 'getVariation'])->name('variation');
    //ONLY IF UPDATE IS NECCESSARY

    //Payout
    Route::get('/transfer', [WalletController::class, 'showpayout'])->name('transfer');
    Route::get('/verifyBankAccount', [BankController::class, 'verifyBankAccount'])->name('verifyBankAccount');
    Route::get('/fetchBanks', [BankController::class, 'fetchBanks']);
    Route::post('/payout', [WalletController::class, 'payout'])->name('payout');
    Route::post('/validatePin', [TransactionController::class, 'validatePin'])->name('pin.validate');
    Route::get('/delete/{id}', [WalletController::class, 'virtualDeleteAccount']);
});

require __DIR__ . '/auth.php';
