<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousesTable extends Migration
{
    public function up()
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('house_name')->unique();
            $table->string('house_location');
            $table->string('street_name');
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->string('house_owner');
            $table->string('phone_number');
            $table->string('plot_number');

            $table->timestamps();

            $table->foreign('supervisor_id')->references('id')->on('supervisors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('houses');
    }
}
