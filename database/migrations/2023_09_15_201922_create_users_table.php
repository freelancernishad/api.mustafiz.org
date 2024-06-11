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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('adult_family_members')->nullable();
            $table->string('applicant_name')->nullable();
            $table->string('applicant_signature')->nullable();
            $table->string('application_preparer_name')->nullable();
            $table->string('arrival_legality')->nullable();
            $table->datetime('arriving_date')->nullable();
            $table->string('category')->nullable();
            $table->string('country_of_birth')->nullable();
            $table->string('country_of_conflict')->nullable();
            $table->string('current_address')->nullable();
            $table->string('current_institution')->nullable();
            $table->string('current_living')->nullable();
            $table->datetime('dob')->nullable();
            $table->string('education_level')->nullable();
            $table->string('father_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('head_name')->nullable();
            $table->string('head_phone')->nullable();
            $table->string('highest_education')->nullable();
            $table->string('institution_address')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('minor_family_members')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('national_id_or_ssn')->nullable();
            $table->string('nationality')->nullable();
            $table->text('perjury_declaration')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('phone')->nullable();
            $table->string('preparer_address')->nullable();
            $table->string('preparer_email')->nullable();
            $table->string('preparer_phone')->nullable();
            $table->string('race')->nullable();
            $table->string('recent_exam_grade')->nullable();
            $table->string('reference1_address')->nullable();
            $table->string('reference1_email')->nullable();
            $table->string('reference1_name')->nullable();
            $table->string('reference1_phone')->nullable();
            $table->string('reference1_relationship')->nullable();
            $table->string('reference2_address')->nullable();
            $table->string('reference2_email')->nullable();
            $table->string('reference2_name')->nullable();
            $table->string('reference2_phone')->nullable();
            $table->string('reference2_relationship')->nullable();
            $table->string('religion')->nullable();
            $table->string('sheltering_country')->nullable();
            $table->text('situation')->nullable();
            $table->string('terms_agreement')->nullable();
            $table->string('total_family_members')->nullable();
            $table->string('role')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
