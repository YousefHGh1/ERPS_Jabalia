<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct(
        protected VehicleService $vehicleService
    ) {
        $this->middleware('permission:index vehicle', ['only' => ['index']]);
        $this->middleware('permission:store vehicle', ['only' => ['store']]);
        $this->middleware('permission:update vehicle', ['only' => ['update']]);
        $this->middleware('permission:delete vehicle', ['only' => ['destroy']]);
    }

    public function index()
    {
        $vehicles = Vehicle::paginate(10);
        return view('vehicle.index', compact('vehicles'));
    }

    public function store(StoreVehicleRequest $request)
    {
        $this->vehicleService->createVehicle(
            $request->validated(),
            $request->hasFile('images') ? $request->file('images') : []
        );

        return redirect()->route('vehicle.index')
            ->with('success', __('saved successfully'));
    }

    public function update(UpdateVehicleRequest $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->vehicleService->updateVehicle(
            $vehicle,
            $request->validated(),
            $request->hasFile('images') ? $request->file('images') : []
        );

        return redirect()->back()
            ->with('success', __('updated successfully'));
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->back()
            ->with('success', __('deleted successfully'));
    }
}