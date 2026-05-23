<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('ironing_staff_id')
                  ->nullable()
                  ->after('notes')
                  ->constrained('staff')
                  ->nullOnDelete();
            $table->decimal('ironing_fee', 12, 2)
                  ->nullable()
                  ->after('ironing_staff_id')
                  ->comment('Fee yang diterima staff setrika untuk order ini');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['ironing_staff_id']);
            $table->dropColumn(['ironing_staff_id', 'ironing_fee']);
        });
    }
};
