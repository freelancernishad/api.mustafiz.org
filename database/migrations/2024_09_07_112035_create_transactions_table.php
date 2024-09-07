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
            $table->foreignId('decision_id')->constrained()->onDelete('cascade'); // Foreign key to the Decision model
            $table->string('currency'); // Currency type (e.g., USD)
            $table->decimal('amount', 15, 2); // Amount of the transaction
            $table->string('payment_by'); // Payment method (e.g., PayPal)
            $table->timestamp('datetime'); // Date and time of the transaction
            $table->text('note')->nullable(); // Optional note for the transaction
            $table->timestamps(); // Created at and updated at timestamps
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
