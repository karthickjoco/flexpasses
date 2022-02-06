<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVechilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('vechiles', function (Blueprint $table) {
            $table->id();
            $table->string('vechicleid');
            $table->string('vin');
            $table->string('name');
            $table->string('plate');
            $table->json('model')->nullable();
            $table->json('options')->nullable();
            $table->string('createDate')->nullable();
            $table->string('description')->nullable();
            $table->string('fleetId')->nullable();
            $table->string('vuboxId')->nullable();
            $table->string('externalId')->nullable();
            $table->string('wakeupProvider')->nullable();
            $table->string('serviceId')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('iccid')->nullable();
            $table->string('imsi')->nullable();
            $table->string('published')->nullable();
            $table->string('archived')->nullable();
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
        Schema::dropIfExists('vechiles');
    }
}
