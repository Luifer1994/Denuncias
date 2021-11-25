<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('response_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->foreignId('id_complaint')->constrained('complaints');
            $table->foreignId('id_state_complaint')->constrained('state_complaints');
            $table->foreignId('id_user')->constrained('users');
            $table->integer('id_media')->nullable();
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
        Schema::dropIfExists('response_complaints');
    }
}
