<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('singers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('genre')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('availability_status')->default('available');
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->text('about')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('singers'); }
};