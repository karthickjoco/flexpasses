<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompletedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('completeds', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('completedid');
            $table->string('tripId');
            $table->string('fleetId');
            $table->string('userId');
            $table->string('profileId');
            $table->string('profileType');
            $table->string('vehicleId');
            $table->string('length');
            $table->string('duration');
            $table->string('pauseDuration');
            $table->string('tripDuration');
            $table->string('bookingDuration');
            $table->string('drivingDuration');
            $table->string('date');
            $table->string('endDate');
            $table->json('additionalInfo');
            $table->string('pricingId');
            $table->json('productIds');
            $table->string('serviceId');
            $table->string('serviceType');
            $table->string('theorStartDate')->nullable();
            $table->string('theorEndDate')->nullable();
            $table->json('ticketInfo');
            $table->json('tripEvents');
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
        Schema::dropIfExists('completeds');
    }
}
