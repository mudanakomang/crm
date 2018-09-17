<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ccid');
            $table->string('idnumber')->nullable();
            $table->string('fname');
            $table->string('lname')->nullable();
            $table->string('email');
            $table->date('birthday')->nullable();
            $table->enum('salutation',['Mr','Mrs','Miss']);
            $table->enum('gender',['Male','Female']);
	        $table->enum('marital_status',['Divorced','Married','Single','Widowed'])->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
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
        Schema::dropIfExists('contacts');
    }
}
