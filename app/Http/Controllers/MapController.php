<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\MapMarker;
use App\Services\GeolocationService;
use Illuminate\Http\Request;

class MapController extends Controller
{
    protected GeolocationService $geolocationService;

    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    public function displayMap(Group $group)
    {
        $markers = MapMarker::where('group_id', $group->id)->get();
        return view('map.display', compact('group', 'markers'));
    }

    public function index(Group $group)
    {
        $mapMarkers = MapMarker::where('group_id', $group->id)->get();

        return view('map.index', [
            'group' => $group,
            'mapMarkers' => $mapMarkers,
        ]);
    }

    public function create(Group $group)
    {
        return view('map.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:256',
            'address' => 'required|string|max:128',
            'emoji' => 'nullable|string|max:1',
        ]);

        $coordinates = $this->geolocationService->getCoordinates($validated['address']);

        if ($coordinates === null) {
            return redirect()->back()->with('error', 'Could not find address..');
        }

        $marker = MapMarker::create([
            'group_id' => $group->id,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'address' => $validated['address'],
            'lat' => $coordinates['lat'],
            'lon' => $coordinates['lon'],
            'emoji' => $validated['emoji'] ?? '📍',
        ]);

        return redirect()->route('groups.mapMarkers.index', $group->id)->with('success', 'Map marker created successfully.');
    }

    public function edit(Group $group, MapMarker $mapMarker)
    {
        return view('map.edit', compact('group', 'mapMarker'));
    }

    public function show(Group $group, MapMarker $mapMarker)
    {
        return view('map.show', compact('group', 'mapMarker'));
    }

    public function update(Request $request, Group $group, MapMarker $mapMarker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:256',
            'address' => 'required|string|max:128',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'emoji' => 'nullable|string|max:1',
        ]);

        if ($validated['address'] !== $mapMarker->address) {
            $coordinates = $this->geolocationService->getCoordinates($validated['address']);

            if ($coordinates === null) {
                return redirect()->back()->with('error', 'Could not find address..');
            }

            $validated['lat'] = $coordinates['lat'];
            $validated['lon'] = $coordinates['lon'];
        }

        $mapMarker->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'lat' => $validated['lat'],
            'lon' => $validated['lon'],
            'emoji' => $validated['emoji'] ?? '📍',
        ]);

        return redirect()->route('groups.mapMarkers.index', $group->id)->with('success', 'Map marker updated successfully.');
    }

    public function destroy(Group $group, MapMarker $mapMarker)
    {
        if ($mapMarker->created_by !== auth()->id()) {
            return redirect()->route('groups.mapMarkers.index', $group->id)->with('error', 'You are not authorized to delete this map marker.');
        }

        $mapMarker->delete();

        return redirect()->route('groups.mapMarkers.index', $group->id)->with('success', 'Map marker deleted successfully.');
    }
}
