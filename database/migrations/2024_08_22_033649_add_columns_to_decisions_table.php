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
        Schema::table('decisions', function (Blueprint $table) {
            $table->decimal('approved_amount', 10, 2)->nullable()->after('how_much');
            $table->text('feedback')->nullable()->after('approved_amount');
            $table->date('date')->nullable()->after('feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('decisions', function (Blueprint $table) {
            $table->dropColumn(['approved_amount', 'feedback', 'date']);
        });
    }
};
