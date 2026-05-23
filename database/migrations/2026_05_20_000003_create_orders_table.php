<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique(); // e.g. LF-20260520-001
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name'); // snapshot in case customer deleted
            $table->string('customer_phone', 20);

            // Service info
            $table->enum('category', ['kiloan', 'satuan', 'ongkir'])->default('kiloan');
            $table->string('service_type')->nullable(); // cuci_setrika, cuci_kering, setrika_saja
            $table->decimal('weight', 8, 2)->nullable(); // for kiloan
            $table->string('perfume')->nullable(); // harum, sakura, lavender, tanpa
            $table->enum('speed', ['reguler', 'kilat', 'ekspres'])->default('reguler');
            $table->date('estimated_done')->nullable();

            // Pricing
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('speed_surcharge', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Payment
            $table->enum('payment_status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'wallet'])->nullable();
            $table->timestamp('paid_at')->nullable();

            // Order status / queue
            $table->enum('status', [
                'antri',       // Diterima
                'dicuci',      // Sedang Dicuci
                'dijemur',     // Dijemur/Dikeringkan
                'disetrika',   // Disetrika
                'siap_ambil',  // Siap Diambil
                'selesai',     // Selesai / Sudah Diambil
                'dibatalkan',  // Dibatalkan
            ])->default('antri');

            $table->text('notes')->nullable();
            $table->string('created_by')->nullable(); // staff name
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
