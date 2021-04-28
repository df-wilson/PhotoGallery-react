<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('user_id')->required();
            $table->string('name');
            $table->string('filepath')->required();
            $table->string('thumbnail_filepath')->required();
            $table->boolean('is_public');
            $table->text('description');
            $table->timestamps();
            $table->dateTime('photo_datetime');
            $table->string('camera_brand', 25);
            $table->string('camera_model', 25);
            $table->string('iso', 25);
            $table->string('aperture', 25);
            $table->string('shutter_speed', 25);
            $table->string('height', 25);
            $table->string('width',25);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photos');
    }
}
