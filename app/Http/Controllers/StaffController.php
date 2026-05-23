<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staffList = Staff::withSum([
            'orders as monthly_kg' => fn($q) => $q
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('service_type', ['cuci_setrika', 'setrika_saja']),
        ], 'weight')
        ->withSum([
            'orders as monthly_fee' => fn($q) => $q
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year),
        ], 'ironing_fee')
        ->withCount([
            'orders as monthly_orders' => fn($q) => $q
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('service_type', ['cuci_setrika', 'setrika_saja']),
        ])
        ->orderBy('name')
        ->get();

        // Riwayat order per staff (10 terbaru)
        $recentByStaff = [];
        foreach ($staffList as $s) {
            $recentByStaff[$s->id] = $s->orders()
                ->with('customer')
                ->whereIn('service_type', ['cuci_setrika', 'setrika_saja'])
                ->latest()
                ->take(10)
                ->get();
        }

        return view('staff.index', compact('staffList', 'recentByStaff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'fee_per_kg' => 'required|numeric|min:0',
        ], [
            'name.required'       => 'Nama staff wajib diisi.',
            'fee_per_kg.required' => 'Fee per kg wajib diisi.',
            'fee_per_kg.numeric'  => 'Fee per kg harus berupa angka.',
        ]);

        Staff::create([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'fee_per_kg' => $request->fee_per_kg,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Staff berhasil ditambahkan!');
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'fee_per_kg' => 'required|numeric|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $staff->update([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'fee_per_kg' => $request->fee_per_kg,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Data staff diperbarui!');
    }

    public function destroy(Staff $staff)
    {
        // Soft-deactivate agar riwayat order tetap terjaga
        $staff->update(['is_active' => false]);
        return back()->with('success', 'Staff dinonaktifkan.');
    }
}
