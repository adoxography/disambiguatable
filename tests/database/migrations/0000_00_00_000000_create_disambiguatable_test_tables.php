<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisambiguatableTestTables extends Migration
{
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dummies', function (Blueprint $table) {
            $table->id();
            $table->string('field_1')->nullable();
            $table->string('field_2')->nullable();
            $table->string('field_3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migration
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dummies');
    }
}
