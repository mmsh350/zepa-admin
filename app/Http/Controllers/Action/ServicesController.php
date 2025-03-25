<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\DataVariation;
use App\Models\Notification;
use App\Models\Services;
use App\Models\ServiceStatus;
use App\Traits\ActiveUsers;
use App\Traits\KycVerify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{


    public function index()
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

        $servicesStatus = ServiceStatus::excludeAdminPayout()->get();

        $services = Services::orderBy('id', 'desc')->paginate(15); // Show 10 per page

        return view('services.index', compact('notifications', 'notifyCount', 'servicesStatus', 'services', 'notificationsEnabled'));
    }

    public function smeIndex()
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



        $dataVariations = DataVariation::paginate(12);

        $smedatas = DB::table('sme_datas')->paginate(12);

        return view('services.sme_data', compact('notifications', 'notifyCount',  'smedatas', 'notificationsEnabled', 'dataVariations'));
    }

    public function updateStatus(Request $request)
    {
        $serviceIds = $request->input('services', []); // Get selected services

        // Update all services based on selection
        ServiceStatus::query()->update(['is_enabled' => 0]); // Set all to inactive
        ServiceStatus::whereIn('id', $serviceIds)->update(['is_enabled' => 1]); // Activate selected

        return redirect()->route('services.index')->with('success', 'Service Updated successfully.');
    }

    public function create()
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
        return view('services.create', compact('notifications', 'notifyCount', 'notificationsEnabled'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'service_code' => 'required|unique:services',
            'name' => 'required',
            'category' => 'required',
            'type' => 'required',
            'amount' => 'required|numeric',
            'description' => 'nullable',
            'status' => 'required|in:enabled,disabled',
        ]);

        Services::create($request->all());
        return redirect()->route('services.index')->with('success', 'Service Created Successfully!');
    }

    // Show edit form
    public function edit($id)
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

        $service = Services::findOrFail($id);
        return view('services.edit', compact('service', 'notifications', 'notifyCount', 'notificationsEnabled'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required',
            'description' => 'nullable',
            'status' => 'required|in:enabled,disabled',
        ]);

        $service = Services::findOrFail($id);
        $service->update($request->all());
        return redirect()->route('services.index')->with('success', 'Service Updated Successfully!');
    }
}
