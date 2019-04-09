<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_custumer');
            $table->unsignedBigInteger('id_seller');
            $table->timestamps();
        });

        Schema::table('sales', function($table)
        {
        $table->foreign('id_custumer')->references('id')->on('custumers')->onDelete('cascade');
        $table->foreign('id_seller')->references('id')->on('sellers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
