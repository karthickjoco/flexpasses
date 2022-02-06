<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('userId')->unique();
            $table->string('fleetId')->nullable();
            $table->string('userName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('firstName')->nullable();
            $table->string('middleName')->nullable();
            $table->string('preferredName')->nullable();
            $table->string('accountStatus')->nullable();
            $table->string('gender')->nullable();
            $table->string('locale')->nullable();
            $table->string('registrationDate')->nullable();
            $table->string('birthDate')->nullable();
            $table->string('nationality')->nullable();
            $table->string('membershipNumber')->nullable();
            $table->longText('notes')->nullable();
            $table->string('dataPrivacyConsent')->nullable();
            $table->string('dateOfAgreements')->nullable();
            $table->string('dataPrivacyConsentUpdateDate')->nullable();
            $table->string('profilingConsent')->nullable();
            $table->string('profilingConsentUpdateDate')->nullable();
            $table->string('marketingConsent')->nullable();
            $table->string('marketingConsentUpdateDate')->nullable();
            $table->string('updateDate')->nullable();
            $table->string('profileId')->nullable();
            $table->string('profileType')->nullable();
            $table->json('services')->nullable();
            $table->string('accesscode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
