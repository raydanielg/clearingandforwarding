<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\ShipmentFile;
use App\Models\Truck;
use App\Models\Driver;
use App\Models\Transporter;
use App\Models\Container;
use App\Services\MilestoneService;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function tripsIndex(Request $request)
    {
        $query = Trip::with(['shipmentFile.client', 'truck', 'driver', 'container']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('origin', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhereHas('shipmentFile', fn($fq) => $fq->where('file_no', 'like', "%{$search}%"))
                  ->orWhereHas('truck', fn($tq) => $tq->where('plate_no', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trips = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.transport.trips.partials.table', compact('trips'))->render(),
            ]);
        }

        return view('admin.transport.trips.index', compact('trips'));
    }

    public function tripsCreate()
    {
        $files = ShipmentFile::whereIn('status', ['port_clearance', 'transport', 'delivered'])->with('client')->latest()->get();
        $trucks = Truck::where('is_active', true)->with('transporter')->orderBy('plate_no')->get();
        $drivers = Driver::where('is_active', true)->orderBy('name')->get();
        return view('admin.transport.trips.create', compact('files', 'trucks', 'drivers'));
    }

    public function tripsStore(Request $request)
    {
        $validated = $request->validate([
            'shipment_file_id' => 'required|exists:shipment_files,id',
            'container_id' => 'nullable|exists:containers,id',
            'truck_id' => 'required|exists:trucks,id',
            'driver_id' => 'required|exists:drivers,id',
            'origin' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'dispatched_at' => 'nullable|date',
            'transport_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'planned';
        $trip = Trip::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Trip created', 'redirect' => route('admin.transport.trips.show', $trip)]);
        }

        return redirect()->route('admin.transport.trips.show', $trip)->with('status', 'Trip created');
    }

    public function tripsShow(Trip $trip)
    {
        $trip->load(['shipmentFile.client', 'truck.transporter', 'driver', 'container']);
        return view('admin.transport.trips.show', compact('trip'));
    }

    public function tripsUpdateStatus(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'status' => 'required|in:planned,loading,in_transit,delivered,closed',
        ]);

        $trip->update(['status' => $validated['status']]);

        if ($validated['status'] === 'in_transit') {
            $trip->update(['dispatched_at' => now()]);
            MilestoneService::record($trip->shipmentFile, 'in_transit', auth()->id());
        }
        if ($validated['status'] === 'delivered') {
            $trip->update(['delivered_at' => now()]);
            MilestoneService::record($trip->shipmentFile, 'delivered', auth()->id());
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Trip status updated']);
        }

        return back()->with('status', 'Trip status updated');
    }

    public function tripsDestroy(Request $request, Trip $trip)
    {
        $trip->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Trip deleted']);
        }

        return redirect()->route('admin.transport.trips.index')->with('status', 'Trip deleted');
    }

    // ── Trucks ──
    public function trucksIndex(Request $request)
    {
        $query = Truck::with(['transporter', 'drivers']);

        if ($request->filled('search')) {
            $query->where('plate_no', 'like', "%{$request->search}%")
                  ->orWhere('trailer_no', 'like', "%{$request->search}%");
        }

        $trucks = $query->latest()->paginate(15);
        $transporters = Transporter::where('is_active', true)->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.transport.trucks.partials.table', compact('trucks'))->render(),
            ]);
        }

        return view('admin.transport.trucks.index', compact('trucks', 'transporters'));
    }

    public function trucksStore(Request $request)
    {
        $validated = $request->validate([
            'plate_no' => 'required|string|max:50|unique:trucks,plate_no',
            'trailer_no' => 'nullable|string|max:50',
            'capacity_tons' => 'nullable|numeric|min:0',
            'transporter_id' => 'nullable|exists:transporters,id',
            'is_active' => 'boolean',
        ]);

        $truck = Truck::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Truck created', 'truck' => $truck]);
        }

        return back()->with('status', 'Truck created');
    }

    public function trucksUpdate(Request $request, Truck $truck)
    {
        $validated = $request->validate([
            'plate_no' => 'required|string|max:50|unique:trucks,plate_no,' . $truck->id,
            'trailer_no' => 'nullable|string|max:50',
            'capacity_tons' => 'nullable|numeric|min:0',
            'transporter_id' => 'nullable|exists:transporters,id',
            'is_active' => 'boolean',
        ]);

        $truck->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Truck updated']);
        }

        return back()->with('status', 'Truck updated');
    }

    public function trucksDestroy(Request $request, Truck $truck)
    {
        $truck->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Truck deleted']);
        }

        return back()->with('status', 'Truck deleted');
    }

    // ── Drivers ──
    public function driversIndex(Request $request)
    {
        $query = Driver::with('truck');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('license_no', 'like', "%{$request->search}%");
        }

        $drivers = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.transport.drivers.partials.table', compact('drivers'))->render(),
            ]);
        }

        return view('admin.transport.drivers.index', compact('drivers'));
    }

    public function driversStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'license_no' => 'nullable|string|max:50',
            'license_expiry' => 'nullable|date',
            'truck_id' => 'nullable|exists:trucks,id',
            'is_active' => 'boolean',
        ]);

        $driver = Driver::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Driver created', 'driver' => $driver]);
        }

        return back()->with('status', 'Driver created');
    }

    public function driversUpdate(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'license_no' => 'nullable|string|max:50',
            'license_expiry' => 'nullable|date',
            'truck_id' => 'nullable|exists:trucks,id',
            'is_active' => 'boolean',
        ]);

        $driver->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Driver updated']);
        }

        return back()->with('status', 'Driver updated');
    }

    public function driversDestroy(Request $request, Driver $driver)
    {
        $driver->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Driver deleted']);
        }

        return back()->with('status', 'Driver deleted');
    }

    // ── Transporters ──
    public function transportersIndex(Request $request)
    {
        $query = Transporter::withCount('trucks');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('tin', 'like', "%{$request->search}%");
        }

        $transporters = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.transport.transporters.partials.table', compact('transporters'))->render(),
            ]);
        }

        return view('admin.transport.transporters.index', compact('transporters'));
    }

    public function transportersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tin' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'rate_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $transporter = \App\Models\Transporter::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transporter created', 'transporter' => $transporter]);
        }

        return back()->with('status', 'Transporter created');
    }

    public function transportersUpdate(Request $request, \App\Models\Transporter $transporter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tin' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'rate_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $transporter->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transporter updated']);
        }

        return back()->with('status', 'Transporter updated');
    }

    public function transportersDestroy(Request $request, \App\Models\Transporter $transporter)
    {
        $transporter->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transporter deleted']);
        }

        return back()->with('status', 'Transporter deleted');
    }
}
