<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('category')->orderBy('sort_order')->orderBy('name')->get();
        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'category'    => 'required|in:kiloan,satuan,ongkir',
            'unit'        => 'nullable|string|max:30',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $validated['is_active']  = true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_ironing'] = $request->boolean('is_ironing', false);

        Service::create($validated);

        return back()->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'category'    => 'required|in:kiloan,satuan,ongkir',
            'unit'        => 'nullable|string|max:30',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $validated['is_ironing'] = $request->boolean('is_ironing', false);
        $service->update($validated);

        return back()->with('success', 'Layanan berhasil diperbarui!');
    }

    public function toggleActive(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);
        return back()->with('success', $service->is_active ? 'Layanan diaktifkan.' : 'Layanan dinonaktifkan.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Layanan dihapus.');
    }
}
