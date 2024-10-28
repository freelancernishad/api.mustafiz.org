<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonnersTable extends Migration
{
    public function up()
    {
        Schema::create('donners', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('zip')->nullable();
            $table->enum('payment_type', ['once', 'monthly'])->default('once');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donners');
    }
}
