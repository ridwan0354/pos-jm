<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'week');
        $from   = match ($period) {
            'today'  => today(),
            'week'   => now()->startOfWeek(),
            'month'  => now()->startOfMonth(),
            'year'   => now()->startOfYear(),
            default  => now()->startOfWeek(),
        };
        $to = now()->endOfDay();

        // Revenue summary
        $totalRevenue  = Order::whereBetween('created_at', [$from, $to])
                              ->where('payment_status', 'lunas')
                              ->where('status', '!=', 'dibatalkan')->sum('total');
        $totalOrders   = Order::whereBetween('created_at', [$from, $to])->count();
        $totalWeight   = Order::whereBetween('created_at', [$from, $to])
                              ->where('category', 'kiloan')->sum('weight');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Revenue by day (last 30 days always for chart)
        $chartDays = $period === 'year' ? 12 : 30;
        $revenueByDay = [];

        if ($period === 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $revenueByDay[] = [
                    'label'   => $date->format('M Y'),
                    'revenue' => (float) Order::whereYear('created_at', $date->year)
                                              ->whereMonth('created_at', $date->month)
                                              ->where('payment_status', 'lunas')
                                              ->where('status', '!=', 'dibatalkan')
                                              ->sum('total'),
                ];
            }
        } elseif ($period === 'today') {
            for ($h = 0; $h < 24; $h++) {
                $revenueByDay[] = [
                    'label'   => sprintf('%02d:00', $h),
                    'revenue' => (float) Order::whereDate('created_at', today())
                                              ->whereRaw('HOUR(created_at) = ?', [$h])
                                              ->where('payment_status', 'lunas')
                                              ->sum('total'),
                ];
            }
        } else {
            $days = $period === 'week' ? 7 : 30;
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $revenueByDay[] = [
                    'label'   => $date->format('d M'),
                    'revenue' => (float) Order::whereDate('created_at', $date)
                                              ->where('payment_status', 'lunas')
                                              ->where('status', '!=', 'dibatalkan')
                                              ->sum('total'),
                ];
            }
        }

        // Orders by status
        $rawStatus = Order::whereBetween('created_at', [$from, $to])
                          ->select('status', DB::raw('count(*) as total'))
                          ->groupBy('status')->pluck('total', 'status');

        $byStatus = collect([
            'antri'      => 0,
            'proses'     => 0,
            'selesai'    => 0,
            'dibatalkan' => 0,
        ]);

        foreach ($rawStatus as $status => $count) {
            if (in_array($status, ['proses', 'dicuci', 'dijemur', 'disetrika', 'siap_ambil'])) {
                $byStatus['proses'] += $count;
            } else {
                $byStatus[$status] = $count;
            }
        }

        // Orders by service type
        $byService = Order::whereBetween('created_at', [$from, $to])
                          ->select('service_type', DB::raw('count(*) as total, sum(total) as revenue'))
                          ->groupBy('service_type')->get();

        // Top customers
        $topCustomers = Customer::withSum([
            'orders as period_revenue' => fn($q) => $q->whereBetween('created_at', [$from, $to])
                                                       ->where('payment_status', 'lunas')
        ], 'total')
            ->withCount(['orders as period_orders' => fn($q) => $q->whereBetween('created_at', [$from, $to])])
            ->having('period_revenue', '>', 0)
            ->orderByDesc('period_revenue')
            ->take(5)->get();

        return view('reports.index', compact(
            'period', 'totalRevenue', 'totalOrders', 'totalWeight',
            'avgOrderValue', 'revenueByDay', 'byStatus', 'byService', 'topCustomers'
        ));
    }
}
