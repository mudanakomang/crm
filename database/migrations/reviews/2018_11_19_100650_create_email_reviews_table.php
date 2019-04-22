<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id');
            $table->string('fname');
            $table->string('lname');
            $table->integer('cleanliness');
            $table->integer('comfort');
            $table->integer('location');
            $table->integer('facilities');
            $table->integer('staff');
            $table->integer('vfm');
            $table->integer('wifi');
            $table->text('suggestion');
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
        Schema::dropIfExists('email_reviews');
    }
}
