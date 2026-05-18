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
        Schema::table('pending_users', function (Blueprint $table) {
            $table->string('artist_name')->nullable()->after('name');
            $table->string('city')->nullable()->after('password');
            $table->string('category')->nullable()->after('city');
            $table->string('profile_image')->nullable()->after('category');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->nullable()->after('phone');
        });

        Schema::table('singers', function (Blueprint $table) {
            $table->json('languages')->nullable()->after('biography');
            $table->boolean('travel_available')->default(false)->after('languages');
            $table->string('instagram_link')->nullable()->after('travel_available');
            $table->string('youtube_link')->nullable()->after('instagram_link');
            $table->string('spotify_link')->nullable()->after('youtube_link');
            $table->string('cover_image')->nullable()->after('profile_image');
            $table->string('sample_audio')->nullable()->after('cover_image');
            $table->string('sample_video')->nullable()->after('sample_audio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_users', function (Blueprint $table) {
            $table->dropColumn(['artist_name', 'city', 'category', 'profile_image']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('city');
        });

        Schema::table('singers', function (Blueprint $table) {
            $table->dropColumn([
                'languages', 'travel_available', 'instagram_link', 'youtube_link',
                'spotify_link', 'cover_image', 'sample_audio', 'sample_video'
            ]);
        });
    }
};
