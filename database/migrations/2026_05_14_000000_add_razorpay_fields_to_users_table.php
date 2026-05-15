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
        if (!Schema::hasColumn('users', 'razorpay_order_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('razorpay_order_id')->nullable()->after('password');
            });
        }
        if (!Schema::hasColumn('users', 'razorpay_payment_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            });
        }
        if (!Schema::hasColumn('users', 'payment_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('payment_status')->nullable()->after('razorpay_payment_id');
            });
        }
        if (!Schema::hasColumn('users', 'razorpay_signature')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('razorpay_signature')->nullable()->after('payment_status');
            });
        }
        if (!Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status')->default('active')->after('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['razorpay_order_id', 'razorpay_payment_id', 'payment_status', 'razorpay_signature', 'status']);
        });
    }
};
