<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
        });

        // Seed nilai default
        DB::table('settings')->insert([
            ['key' => 'shop_name',   'value' => 'LinenFlow POS'],
            ['key' => 'admin_phone', 'value' => ''],
            ['key' => 'shop_logo',   'value' => ''],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
