<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuplicatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duplicates', function (Blueprint $table) {
            $table->string('folio',20)->unique();
            $table->string('folio_master',20);
            $table->string('status',30);
            $table->string('room',4);
            $table->string('fname');
            $table->string('lname');
            $table->string('email');
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
        Schema::dropIfExists('duplicates');
    }
}
