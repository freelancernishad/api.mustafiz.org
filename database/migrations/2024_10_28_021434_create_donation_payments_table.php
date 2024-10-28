<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('donation_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donner_id')->constrained('donners')->onDelete('cascade');
            $table->string('trx_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->date('date');
            $table->string('month');
            $table->year('year');
            $table->string('payment_url')->nullable();
            $table->json('ipn_response')->nullable();
            $table->string('method');
            $table->string('checkout_session_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donation_payments');
    }
}
