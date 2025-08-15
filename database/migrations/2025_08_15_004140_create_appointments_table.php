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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            
            $table->dateTime('start_time')->nullable(false);
            $table->dateTime('end_time')->nullable(false);

            $table->decimal('total_price', 8, 2)->nullable();
            $table->enum('status', ['scheduled', 'complet', 'canceled'])->default('scheduled');
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
