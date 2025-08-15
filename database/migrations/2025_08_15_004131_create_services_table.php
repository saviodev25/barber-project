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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false); 
            $table->text('description')->nullable(); // Optional description field
            $table->decimal('price', 8, 2)->nullable(false); // Assuming price is a decimal with 2 decimal places
            $table->boolean('active')->default(true); // Active status, default is true
            $table->integer('duration_minutes')->default(30); // Duration in minutes, default is 30 minutes
            $table->string('image')->nullable(); // Optional image field for service representation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
