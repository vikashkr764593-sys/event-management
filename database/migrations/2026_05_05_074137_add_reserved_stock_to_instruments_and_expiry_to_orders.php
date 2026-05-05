<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('instruments', function (Blueprint $table) {
            $table->integer('reserved_stock')->default(0)->after('stock');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('reservation_expires_at')->nullable()->after('shipping_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instruments', function (Blueprint $table) {
            $table->dropColumn('reserved_stock');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('reservation_expires_at');
        });
    }
};
