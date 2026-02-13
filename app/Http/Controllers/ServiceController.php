<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Get all services
        $services = Service::all();
        
        // 2. Send them to the 'index' view
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        // 2. Save (Default is_active to true in migration or model)
        Service::create($request->all());

        // 3. Redirect
        return redirect()->route('services.index')->with('success', 'Service added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not used in this project
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // 1. Find the service by ID
        $service = Service::findOrFail($id);

        // 2. Return the edit view
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Find the service
        $service = Service::findOrFail($id);

        // 2. Validate
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        // 3. Update
        $service->update($validated);

        // 4. Redirect
        return redirect()->route('services.index')->with('success', 'Service updated successfully!');
    }

    /**
     * Toggle the service status (Active <-> Inactive).
     * This replaces the destructive delete action.
     */
    public function toggleStatus(string $id)
    {
        $service = Service::findOrFail($id);

        // Flip the status (Active becomes Inactive, Inactive becomes Active)
        $service->is_active = !$service->is_active;
        $service->save();

        $status = $service->is_active ? 'activated' : 'disabled';

        return redirect()->route('services.index')->with('success', "Service has been $status.");
    }

    /**
     * Remove the specified resource from storage.
     * (Kept for admin emergency use, but UI will use toggleStatus)
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}