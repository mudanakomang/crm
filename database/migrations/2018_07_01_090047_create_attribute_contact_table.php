<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_contact', function (Blueprint $table) {
           $table->integer('contact_id')->unsigned();
           $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
           $table->integer('attribute_id')->unsigned();
           $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
           $table->string('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_contact');
    }
}
