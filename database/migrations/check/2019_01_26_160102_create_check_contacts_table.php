<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_contacts', function (Blueprint $table) {
            $table->string('folio')->unique()->primary();
            $table->string('folio_master');
            $table->string('idnumber')->nullable();
            $table->string('fname');
            $table->string('lname')->nullable();
            $table->string('email')->unique();
            $table->date('birthday')->nullable();
            $table->string('salutation')->nullable();
            $table->enum('gender',['M','F'])->nullable();
            $table->text('problems')->nullable();
            $table->string('country_id')->nullable();
            $table->string('area')->nullable();
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
        Schema::dropIfExists('check_contacts');
    }
}
