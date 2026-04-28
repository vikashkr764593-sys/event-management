<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('singer_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('singer_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('status')->default('Available'); // Available, Busy
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('singer_availabilities'); }
};