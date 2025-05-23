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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('disability_id')->constrained('disabilities')->onDelete('cascade');
            $table->foreignId('educational_id')->constrained('disabilities')->onDelete('cascade');
            $table->string('name');
            $table->string('contactnumber');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('location')->nullable();
            $table->string('pwd_id')->nullable();
            $table->integer('age')->default(0);
            $table->text('about')->nullable();
            $table->string('founder')->default('');
            $table->integer('year_established')->default(0);
            $table->text('affiliations')->nullable();
            $table->text('awards')->nullable();
            $table->string('profile_path')->nullable();
            $table->string('paypal_account')->nullable();
            $table->enum('registration_status', ['Pending', 'Activated', 'Deactivated'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
