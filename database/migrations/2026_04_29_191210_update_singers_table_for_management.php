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
        Schema::table('singers', function (Blueprint $table) {
            $table->string('name')->after('user_id');
            $table->string('stage_name')->nullable()->after('name');
            $table->string('profile_image')->nullable()->after('stage_name');
            $table->renameColumn('about', 'biography');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('singers', function (Blueprint $table) {
            $table->dropColumn(['name', 'stage_name', 'profile_image']);
            $table->renameColumn('biography', 'about');
        });
    }
};
