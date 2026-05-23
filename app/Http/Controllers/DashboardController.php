<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        // Today's stats
        $todayOrders   = Order::whereDate('created_at', $today)->count();
        $todayRevenue  = Order::whereDate('created_at', $today)
                              ->where('payment_status', 'lunas')
                              ->where('status', '!=', 'dibatalkan')->sum('total');
        $pendingOrders = Order::whereNotIn('status', ['selesai', 'dibatalkan'])->count();
        $siapAmbil     = Order::where('status', 'siap_ambil')->count();

        // Monthly revenue
        $monthlyRevenue = Order::whereMonth('created_at', $today->month)
                               ->whereYear('created_at', $today->year)
                               ->where('payment_status', 'lunas')
                               ->where('status', '!=', 'dibatalkan')
                               ->sum('total');

        // Revenue chart: last 7 days
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueChart[] = [
                'label'   => $date->format('d M'),
                'revenue' => (float) Order::whereDate('created_at', $date)
                                          ->where('payment_status', 'lunas')
                                          ->sum('total'),
                'orders'  => Order::whereDate('created_at', $date)->count(),
            ];
        }

        // Queue by status
        $queue = Order::whereNotIn('status', ['selesai', 'dibatalkan'])
                      ->select('status', DB::raw('count(*) as total'))
                      ->groupBy('status')
                      ->pluck('total', 'status');

        // Recent orders
        $recentOrders = Order::with('customer')
                             ->latest()
                             ->take(8)
                             ->get();

        // Low stock alerts
        $lowStocks = Stock::whereRaw('quantity <= min_quantity')->take(5)->get();

        // New vs returning customers this month
        $newCustomers      = Customer::whereMonth('created_at', $today->month)
                                     ->whereYear('created_at', $today->year)
                                     ->count();
        $totalCustomers    = Customer::count();
        $returningCustomers = $totalCustomers - $newCustomers;

        // Unpaid orders
        $unpaidTotal = Order::where('payment_status', 'belum_lunas')
                            ->whereNotIn('status', ['dibatalkan'])
                            ->sum('total');

        return view('dashboard', compact(
            'todayOrders', 'todayRevenue', 'pendingOrders', 'siapAmbil',
            'monthlyRevenue', 'revenueChart', 'queue', 'recentOrders',
            'lowStocks', 'newCustomers', 'returningCustomers', 'unpaidTotal'
        ));
    }
}
