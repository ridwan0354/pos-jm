<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockLog;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('low_only')) {
            $query->whereRaw('quantity <= min_quantity');
        }

        $stocks = $query->paginate(15)->withQueryString();
        $lowCount = Stock::whereRaw('quantity <= min_quantity')->count();

        return view('stocks.index', compact('stocks', 'lowCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'category'       => 'nullable|string|max:50',
            'unit'           => 'required|string|max:20',
            'quantity'       => 'required|numeric|min:0',
            'min_quantity'   => 'required|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string|max:255',
        ]);

        $stock = Stock::create($validated);

        if ($validated['quantity'] > 0) {
            StockLog::create([
                'stock_id'   => $stock->id,
                'type'       => 'in',
                'qty'        => $validated['quantity'],
                'qty_before' => 0,
                'qty_after'  => $validated['quantity'],
                'reason'     => 'Stok awal',
                'created_by' => 'Staff',
            ]);
        }

        return back()->with('success', 'Stok berhasil ditambahkan!');
    }

    public function addStock(Request $request, Stock $stock)
    {
        $request->validate([
            'qty'    => 'required|numeric|min:0.1',
            'reason' => 'nullable|string|max:100',
        ]);

        $before = $stock->quantity;
        $stock->increment('quantity', $request->qty);

        StockLog::create([
            'stock_id'   => $stock->id,
            'type'       => 'in',
            'qty'        => $request->qty,
            'qty_before' => $before,
            'qty_after'  => $before + $request->qty,
            'reason'     => $request->reason ?? 'Penambahan stok',
            'created_by' => 'Staff',
        ]);

        return back()->with('success', 'Stok berhasil ditambahkan!');
    }

    public function reduceStock(Request $request, Stock $stock)
    {
        $request->validate([
            'qty'    => 'required|numeric|min:0.1',
            'reason' => 'nullable|string|max:100',
        ]);

        $before = $stock->quantity;
        $newQty = max(0, $before - $request->qty);
        $stock->update(['quantity' => $newQty]);

        StockLog::create([
            'stock_id'   => $stock->id,
            'type'       => 'out',
            'qty'        => $request->qty,
            'qty_before' => $before,
            'qty_after'  => $newQty,
            'reason'     => $request->reason ?? 'Pengurangan stok',
            'created_by' => 'Staff',
        ]);

        return back()->with('success', 'Stok berhasil dikurangi!');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return back()->with('success', 'Item stok dihapus.');
    }
}
