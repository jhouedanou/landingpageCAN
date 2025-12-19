<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bar;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    /**
     * Display a listing of the venues with filtering
     */
    public function index(Request $request)
    {
        $query = Bar::query();

        // Filtrage par type PDV
        if ($request->filled('type_pdv')) {
            $query->where('type_pdv', $request->type_pdv);
        }

        // Filtrage par zone
        if ($request->filled('zone')) {
            $query->where('zone', 'LIKE', '%' . $request->zone . '%');
        }

        // Filtrage par statut
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'active');
        }

        // Recherche par nom
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $venues = $query->orderBy('name')->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Bar::count(),
            'active' => Bar::where('is_active', true)->count(),
            'inactive' => Bar::where('is_active', false)->count(),
            'by_type' => [
                'dakar' => Bar::where('type_pdv', 'dakar')->count(),
                'regions' => Bar::where('type_pdv', 'regions')->count(),
                'chr' => Bar::where('type_pdv', 'chr')->count(),
                'fanzone' => Bar::where('type_pdv', 'fanzone')->count(),
            ],
        ];

        $typePdvOptions = Bar::getTypePdvOptions();

        return view('admin.venues.index', compact('venues', 'stats', 'typePdvOptions'));
    }

    /**
     * Show the form for creating a new venue
     */
    public function create()
    {
        $typePdvOptions = Bar::getTypePdvOptions();
        return view('admin.venues.create', compact('typePdvOptions'));
    }

    /**
     * Store a newly created venue
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'zone' => 'nullable|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type_pdv' => 'required|in:dakar,regions,chr,fanzone',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Bar::create($validated);

        return redirect()->route('admin.venues.index')
            ->with('success', 'Point de vente créé avec succès.');
    }

    /**
     * Show the form for editing the specified venue
     */
    public function edit(Bar $venue)
    {
        $typePdvOptions = Bar::getTypePdvOptions();
        return view('admin.venues.edit', compact('venue', 'typePdvOptions'));
    }

    /**
     * Update the specified venue
     */
    public function update(Request $request, Bar $venue)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'zone' => 'nullable|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type_pdv' => 'required|in:dakar,regions,chr,fanzone',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $venue->update($validated);

        return redirect()->route('admin.venues.index')
            ->with('success', 'Point de vente mis à jour avec succès.');
    }

    /**
     * Remove the specified venue
     */
    public function destroy(Bar $venue)
    {
        $venue->delete();

        return redirect()->route('admin.venues.index')
            ->with('success', 'Point de vente supprimé avec succès.');
    }

    /**
     * Bulk update type_pdv for multiple venues
     */
    public function bulkUpdateType(Request $request)
    {
        $request->validate([
            'venue_ids' => 'required|array',
            'venue_ids.*' => 'exists:bars,id',
            'type_pdv' => 'required|in:dakar,regions,chr,fanzone',
        ]);

        Bar::whereIn('id', $request->venue_ids)
            ->update(['type_pdv' => $request->type_pdv]);

        $count = count($request->venue_ids);

        return redirect()->route('admin.venues.index')
            ->with('success', "{$count} point(s) de vente mis à jour avec succès.");
    }

    /**
     * Bulk update zone for multiple venues
     */
    public function bulkUpdateZone(Request $request)
    {
        $request->validate([
            'venue_ids' => 'required|array',
            'venue_ids.*' => 'exists:bars,id',
            'zone' => 'required|string|max:100',
        ]);

        Bar::whereIn('id', $request->venue_ids)
            ->update(['zone' => $request->zone]);

        $count = count($request->venue_ids);

        return redirect()->route('admin.venues.index')
            ->with('success', "{$count} point(s) de vente réassigné(s) avec succès.");
    }
}
