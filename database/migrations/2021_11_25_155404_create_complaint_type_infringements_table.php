<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintTypeInfringementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaint_type_infringements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_complaint_type')->constrained('complaint_types');
            $table->foreignId('id_infringement')->constrained('infringements');
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
        Schema::dropIfExists('complaint_type_infringements');
    }
}
