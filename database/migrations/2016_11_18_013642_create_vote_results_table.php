<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteResultsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_results', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid')->index();
            $table->string('voteid')->index();
            $table->json('selected');
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
        Schema::drop('vote_results');
    }

}
