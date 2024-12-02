<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('house_id');
            $table->string('room_name');
            $table->decimal('rent', 10, 2);
            $table->timestamps();
        
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
            
            $table->unique(['house_id', 'room_name']);
        });        
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
