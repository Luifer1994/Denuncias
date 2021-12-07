<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('cod')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('address');
            $table->string('name_offender')->nullable();
            $table->longText('description');
            $table->foreignId('id_complaint_type')->constrained('complaint_types');
            $table->integer('id_user')->nullable();
            $table->integer('id_user_asigne')->nullable();
            $table->foreignId('id_state')->constrained('state_complaints');
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
        Schema::dropIfExists('complaints');
    }
}
