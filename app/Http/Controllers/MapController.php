<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\MapMarker;
use Illuminate\Http\Request;

class MapController extends Controller
{
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
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'emoji' => 'nullable|string',
        ]);

        $marker = MapMarker::create([
            'group_id' => $group->id,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'lat' => $validated['lat'],
            'lon' => $validated['lon'],
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
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
            'emoji' => 'nullable|string',
        ]);

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
