<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisambiguationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disambiguations', function (Blueprint $table) {
            $table->id();
            $table->morphs('disambiguatable');
            $table->unsignedInteger('disambiguator');
            $table->timestamps();

            $table->unique(['disambiguatable_type', 'disambiguatable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disambiguations');
    }
}
