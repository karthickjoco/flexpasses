<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEndTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('end_trips', function (Blueprint $table) {
            $table->bigInteger('id')->autoIncrement();
            $table->string('vechicleid');
            $table->string('name');
            $table->string('plate');
            $table->string('vin');
            $table->string('fleetid');
            $table->string('boxid');
            $table->integer('vehicle_status');
            $table->longText('zones');
            $table->boolean('releasable');
            $table->string('box_status');
            $table->string('autonomy');
            $table->string('autonomy2');
            $table->boolean('isCharging');
            $table->string('battery');
            $table->string('km');
            $table->string('speed');
            $table->longText('location');
            $table->longText('endZoneIds');
            $table->longText('productIds');
            $table->boolean('isDoorClosed');
            $table->boolean('isDoorLocked');
            $table->boolean('engineOn');
            $table->boolean('secureOn');
            $table->string('userId');
            $table->string('user_locale');
            $table->string('rfid');
            $table->string('orderId');
            $table->text('gatewayUrl');
            $table->integer('booking_status');
            $table->string('booking_date');
            $table->string('expiresOn');
            $table->string('last_active_date')->nullable();
            $table->string('last_wakeUp_date')->nullable();
            $table->string('version')->nullable();
            $table->string('pricingId');
            $table->string('start_date')->nullable();
            $table->string('theorStartDate')->nullable();
            $table->string('theorEndDate')->nullable();
            $table->longText('startZones');
            $table->longText('endZones');
            $table->boolean('disabled');
            $table->string('outOfServiceReason')->nullable();
            $table->string('ignitionOffGeohash')->nullable();
            $table->longText('geohashNeighbours');
            $table->boolean('cleanlinessStatus');
            $table->boolean('needsRedistribution');
            $table->boolean('batteryUnderThreshold');
            $table->boolean('isBeingTowed');
            $table->boolean('automaticallyEnableVehicleAfterRangeRecovery');
            $table->longText('key');
            $table->longText('startTripLocation');
            $table->string('username');
            $table->string('profileType');
            $table->string('comeFromApp');
            $table->string('serviceId');
            $table->string('serviceType');
            $table->string('profileId');
            $table->string('preAuthStatus');
            $table->string('start_mileage');
            $table->boolean('preAuthEnabled');
            $table->string('entityId');
            $table->string('amountPreAuth')->nullable();
            $table->integer('number_of_end_attempts')->nullable();
            $table->integer('succeded_end_attempt')->nullable();
            $table->integer('trip_ended')->default('0');
            $table->integer('trip_released')->default('0');
//            $table->integer('succeded_trip_terminate_attempt')->nullable();
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
        Schema::dropIfExists('end_trips');
    }
}
