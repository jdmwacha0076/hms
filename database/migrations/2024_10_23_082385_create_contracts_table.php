<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('house_id');
            $table->unsignedBigInteger('room_id');

            $table->date('start_date');
            $table->date('end_date');
            $table->integer('contract_interval');

            $table->decimal('amount_paid', 10, 2);
            $table->decimal('amount_remaining', 10, 2);
            $table->decimal('total', 10, 2);

            // Add the contract_status column
            $table->enum('contract_status', ['BADO', 'UNAENDELEA', 'UMEISHA'])
                ->default('BADO'); // Set default value as 'BADO'

            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
