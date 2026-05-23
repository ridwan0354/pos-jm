<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('referral_code', 20)->unique()->nullable();
            $table->string('referred_by', 20)->nullable(); // referral code of referrer
            $table->enum('member_level', ['Bronze', 'Silver', 'Gold'])->default('Bronze');
            $table->decimal('wallet_balance', 12, 2)->default(0);
            $table->decimal('total_weight', 10, 2)->default(0); // total kg processed
            $table->integer('total_orders')->default(0);
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
