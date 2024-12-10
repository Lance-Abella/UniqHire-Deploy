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
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->string('position');
            $table->text('description');
            $table->double('salary')->default(0);
            $table->date('end_date');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('location')->nullable();
            $table->foreignId('worktype_id')->constrained('work_types')->onDelete('cascade');
            $table->foreignId('worksetup_id')->constrained('work_setups')->onDelete('cascade');
            $table->enum('status', ['Ongoing', 'Ended', 'Cancelled'])->default('Ongoing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
