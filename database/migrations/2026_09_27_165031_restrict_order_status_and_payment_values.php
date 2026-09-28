<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('orders')->whereRaw('LOWER(payment_status) = ?', ['paid'])->update(['payment_status' => 'PAID']);
        DB::table('orders')->whereRaw('LOWER(payment_status) = ?', ['pending'])->update(['payment_status' => 'PENDING']);

        DB::table('orders')->whereRaw('LOWER(payment_method) IN (?, ?)', ['cash', 'cash_on_delivery'])->update(['payment_method' => 'CASH']);
        DB::table('orders')->whereRaw('LOWER(payment_method) = ?', ['card'])->update(['payment_method' => 'CARD']);
        DB::table('orders')->whereRaw('LOWER(payment_method) = ?', ['paypal'])->update(['payment_method' => 'PAYPAL']);

        DB::table('orders')->whereRaw('LOWER(status) = ?', ['active'])->update(['status' => 'ACTIVE']);
        DB::table('orders')->whereRaw('LOWER(status) = ?', ['cancelled'])->update(['status' => 'CANCELLED']);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['PAID', 'PENDING'])->default('PENDING')->change();
            $table->enum('payment_method', ['CASH', 'CARD', 'PAYPAL'])->change();
            $table->enum('status', ['ACTIVE', 'CANCELLED'])->default('ACTIVE')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 32)->default('pending')->change();
            $table->string('payment_method', 32)->change();
            $table->string('status', 32)->default('active')->change();
        });

        DB::table('orders')->where('payment_status', 'PAID')->update(['payment_status' => 'paid']);
        DB::table('orders')->where('payment_status', 'PENDING')->update(['payment_status' => 'pending']);

        DB::table('orders')->where('payment_method', 'CASH')->update(['payment_method' => 'cash_on_delivery']);
        DB::table('orders')->where('payment_method', 'CARD')->update(['payment_method' => 'card']);
        DB::table('orders')->where('payment_method', 'PAYPAL')->update(['payment_method' => 'paypal']);

        DB::table('orders')->where('status', 'ACTIVE')->update(['status' => 'active']);
        DB::table('orders')->where('status', 'CANCELLED')->update(['status' => 'cancelled']);
    }
};
