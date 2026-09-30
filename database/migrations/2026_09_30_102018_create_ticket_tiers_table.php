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
        Schema::create('ticket_tiers', function (Blueprint $table) {
              $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('price_cents'); // $99.00 stored as 9900 to avoid floating point math drift
            $table->unsignedInteger('total_capacity');
            $table->unsignedInteger('remaining_capacity');
            $table->timestamps();

           $table->index(['event_id', 'remaining_capacity']); // Add an index for event_id and remaining_capacity for faster lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_tiers');
    }
};
