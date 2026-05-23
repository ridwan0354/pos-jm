<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Stock;
use App\Models\Order;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Services ──────────────────────────────────────────────────────────
        $services = [
            ['name' => 'Cuci + Setrika',  'category' => 'kiloan', 'unit' => 'kg',  'price' => 7000,  'sort_order' => 1],
            ['name' => 'Cuci Kering',     'category' => 'kiloan', 'unit' => 'kg',  'price' => 5000,  'sort_order' => 2],
            ['name' => 'Setrika Saja',    'category' => 'kiloan', 'unit' => 'kg',  'price' => 4000,  'sort_order' => 3],
            ['name' => 'Jas / Blazer',    'category' => 'satuan', 'unit' => 'pcs', 'price' => 35000, 'sort_order' => 4],
            ['name' => 'Sepatu',          'category' => 'satuan', 'unit' => 'pcs', 'price' => 25000, 'sort_order' => 5],
            ['name' => 'Selimut',         'category' => 'satuan', 'unit' => 'pcs', 'price' => 20000, 'sort_order' => 6],
            ['name' => 'Boneka Besar',    'category' => 'satuan', 'unit' => 'pcs', 'price' => 30000, 'sort_order' => 7],
            ['name' => 'Ongkos Antar',   'category' => 'ongkir', 'unit' => 'trip','price' => 10000, 'sort_order' => 8],
            ['name' => 'Ongkos Jemput',  'category' => 'ongkir', 'unit' => 'trip','price' => 10000, 'sort_order' => 9],
        ];
        foreach ($services as $s) {
            Service::firstOrCreate(['name' => $s['name'], 'category' => $s['category']], $s);
        }

        // ── Stocks ────────────────────────────────────────────────────────────
        $stocks = [
            ['name' => 'Deterjen Bubuk',     'category' => 'deterjen',  'unit' => 'kg',    'quantity' => 25,  'min_quantity' => 5,  'price_per_unit' => 18000],
            ['name' => 'Deterjen Cair',      'category' => 'deterjen',  'unit' => 'liter', 'quantity' => 15,  'min_quantity' => 3,  'price_per_unit' => 22000],
            ['name' => 'Pelembut Pakaian',   'category' => 'pelembut',  'unit' => 'liter', 'quantity' => 10,  'min_quantity' => 3,  'price_per_unit' => 25000],
            ['name' => 'Pewangi Sakura',     'category' => 'pewangi',   'unit' => 'botol', 'quantity' => 8,   'min_quantity' => 2,  'price_per_unit' => 35000],
            ['name' => 'Pewangi Lavender',   'category' => 'pewangi',   'unit' => 'botol', 'quantity' => 6,   'min_quantity' => 2,  'price_per_unit' => 35000],
            ['name' => 'Pewangi Harum',      'category' => 'pewangi',   'unit' => 'botol', 'quantity' => 2,   'min_quantity' => 3,  'price_per_unit' => 35000],
            ['name' => 'Plastik Kemasan',    'category' => 'packaging', 'unit' => 'pcs',   'quantity' => 200, 'min_quantity' => 50, 'price_per_unit' => 500],
            ['name' => 'Hanger Plastik',     'category' => 'packaging', 'unit' => 'pcs',   'quantity' => 100, 'min_quantity' => 30, 'price_per_unit' => 2000],
            ['name' => 'Struk Printer Roll', 'category' => 'peralatan', 'unit' => 'roll',  'quantity' => 3,   'min_quantity' => 2,  'price_per_unit' => 15000],
        ];
        foreach ($stocks as $s) {
            Stock::firstOrCreate(['name' => $s['name']], $s);
        }

        // ── Sample Customers ──────────────────────────────────────────────────
        $customers = [
            ['name' => 'Budi Santoso',   'phone' => '081234567890', 'member_level' => 'Gold',   'total_orders' => 35],
            ['name' => 'Sari Dewi',      'phone' => '082345678901', 'member_level' => 'Silver',  'total_orders' => 12],
            ['name' => 'Ahmad Fauzi',    'phone' => '083456789012', 'member_level' => 'Bronze',  'total_orders' => 4],
            ['name' => 'Rina Wulandari', 'phone' => '084567890123', 'member_level' => 'Silver',  'total_orders' => 15],
            ['name' => 'Doni Prasetyo',  'phone' => '085678901234', 'member_level' => 'Bronze',  'total_orders' => 2],
        ];

        foreach ($customers as $c) {
            $customer = Customer::firstOrCreate(['phone' => $c['phone']], array_merge($c, [
                'referral_code' => Customer::generateReferralCode($c['name']),
            ]));
        }

        // ── Sample Orders ─────────────────────────────────────────────────────
        $sampleCustomers = Customer::all();
        $statuses = ['antri', 'dicuci', 'dijemur', 'disetrika', 'siap_ambil', 'selesai'];
        $serviceTypes = ['cuci_setrika', 'cuci_kering', 'setrika_saja'];
        $perfumes = ['harum', 'sakura', 'lavender', 'tanpa'];

        for ($i = 0; $i < 20; $i++) {
            $customer   = $sampleCustomers->random();
            $serviceType = $serviceTypes[array_rand($serviceTypes)];
            $weight     = round(rand(15, 80) / 10, 1);
            $prices     = ['cuci_setrika' => 7000, 'cuci_kering' => 5000, 'setrika_saja' => 4000];
            $subtotal   = $weight * $prices[$serviceType];
            $isPaid     = (bool) rand(0, 1);
            $status     = $statuses[array_rand($statuses)];
            $daysAgo    = rand(0, 14);

            Order::create([
                'order_number'    => 'LF-' . now()->subDays($daysAgo)->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'customer_id'     => $customer->id,
                'customer_name'   => $customer->name,
                'customer_phone'  => $customer->phone,
                'category'        => 'kiloan',
                'service_type'    => $serviceType,
                'weight'          => $weight,
                'perfume'         => $perfumes[array_rand($perfumes)],
                'speed'           => 'reguler',
                'estimated_done'  => now()->subDays($daysAgo)->addDays(3)->toDateString(),
                'subtotal'        => $subtotal,
                'speed_surcharge' => 0,
                'discount'        => 0,
                'total'           => $subtotal,
                'payment_status'  => $isPaid ? 'lunas' : 'belum_lunas',
                'payment_method'  => $isPaid ? 'cash' : null,
                'paid_at'         => $isPaid ? now()->subDays($daysAgo) : null,
                'status'          => $status,
                'created_by'      => 'Staff',
                'created_at'      => now()->subDays($daysAgo),
                'updated_at'      => now()->subDays($daysAgo),
            ]);
        }
    }
}
