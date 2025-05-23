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
        Schema::create('certification_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('training_programs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('user_infos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certification_details');
    }
};
