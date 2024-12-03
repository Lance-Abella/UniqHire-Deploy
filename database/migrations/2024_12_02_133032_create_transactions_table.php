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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->foreignId('crowdfund_id')->constrained('crowdfund_events')->onDelete('cascade');
            $table->foreignId('sponsor_id')->constrained('user_infos')->onDelete('cascade');
            $table->decimal('amount', 10, 2)->default(0); // Example: up to 10 digits, 2 decimal places
            $table->string('transaction_id')->unique()->nullable();
            $table->enum('status', ['Completed', 'Failed', 'Unknown'])->default('Unknown');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
