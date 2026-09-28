<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_code', 26)->nullable()->after('id');
        });

        DB::table('orders')->orderBy('id')->eachById(function (object $order): void {
            DB::table('orders')->where('id', $order->id)->update([
                'tracking_code' => (string) Str::ulid(),
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('tracking_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['tracking_code']);
            $table->dropColumn('tracking_code');
        });
    }
};
