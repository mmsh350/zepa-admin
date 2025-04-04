<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\Models\DataVariation;
use App\Models\Notification;
use App\Models\Services;
use App\Models\ServiceStatus;
use App\Traits\ActiveUsers;
use App\Traits\KycVerify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{


    public function index(Request $request)
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

        $search = $request->input('search');

        $perPage = $request->input('per_page', 10);

        $searchQuery = DB::table('services');

        if (!empty($searchQuery)) {
            $searchQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $services = $searchQuery->paginate($perPage)->withQueryString();

        return view('services.index', compact('notifications', 'notifyCount', 'servicesStatus', 'services', 'notificationsEnabled'));
    }

    public function smeIndex(Request $request)
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

        // Add search filter for variation code
        $searchVariation = $request->input('search-variation');

        $perPage = $request->input('per_page', 10);

        $variationQuery = DB::table('data_variations');

        if (!empty($searchVariation)) {
            $variationQuery->where(function ($query) use ($searchVariation) {
                $query->where('service_name', 'like', "%{$searchVariation}%")
                    ->orWhere('name', 'like', "%{$searchVariation}%");
            });
        }

        // Add search filter for sme_datas
        $search = $request->input('search');
        $perPage2 = $request->input('per_page2', 10);


        $smeQuery = DB::table('sme_datas')->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $smeQuery->where(function ($query) use ($search) {
                $query->where('network', 'like', "%{$search}%")
                    ->orWhere('plan_type', 'like', "%{$search}%");
            });
        }

        $smedatas = $smeQuery->paginate($perPage2)->withQueryString();

        $dataVariations =  $variationQuery->paginate($perPage)->withQueryString();

        return view('services.sme_data', compact('notifications', 'notifyCount', 'smedatas', 'notificationsEnabled', 'dataVariations'));
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


    public function createSMEData()
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
        return view('services.createSMEData', compact('notifications', 'notifyCount', 'notificationsEnabled'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_code' => 'required|numeric|unique:services',
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

    public function storeSMEData(Request $request)
    {

        $validated = $request->validate([
            'data_id'   => 'required|numeric|unique:sme_datas',
            'network'   => 'required|string',
            'amount'    => 'required|numeric',
            'plan_type' => 'required|string',
            'size'      => 'required|string',
            'validity'  => 'required|string',
        ]);

        $validated['created_at'] = Carbon::now();
        $validated['updated_at'] = Carbon::now();

        DB::table('sme_datas')->insert($validated);


        return redirect()->route('sme-service')->with('success', 'Service Created Successfully!');
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

    //Show variation edit form
    public function editVariations($id)
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

        $service = DataVariation::findOrFail($id);
        return view('services.editVariation', compact('service', 'notifications', 'notifyCount', 'notificationsEnabled'));
    }

    //Show SME edit form
    public function editSMEData($id)
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

        $service = DB::table('sme_datas')->where('id', $id)->firstOrFail();

        return view('services.editSMEData', compact('service', 'notifications', 'notifyCount', 'notificationsEnabled'));
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

    public function updateVariation(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:enabled,disabled',
        ]);

        $service = DataVariation::findOrFail($id);
        $service->update($request->all());
        return redirect()->route('sme-service')->with('success', 'Service Updated Successfully!');
    }

    public function updateSMEData(Request $request, $id)
    {

        $validated = $request->validate([
            'network'   => 'required|string',
            'amount'    => 'required|numeric',
            'plan_type' => 'required|string',
            'size'      => 'required|string',
            'validity'  => 'required|string',
            'status'    => 'required|in:enabled,disabled',
        ]);


        $service = DB::table('sme_datas')->where('id', $id)->first();

        if (!$service) {
            abort(404, 'Service not found.');
        }

        DB::table('sme_datas')->where('id', $id)->update($validated);

        return redirect()->route('sme-service')->with('success', 'Service Updated Successfully!');
    }
}
