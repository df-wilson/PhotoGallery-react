<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotoKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_keywords', function (Blueprint $table) {
            $table->primary(['photo_id', 'keyword_id']);
            $table->unsignedInteger('photo_id');
            $table->unsignedInteger('keyword_id');
            $table->timestamps();
            $table->foreign('photo_id')->references('id')->on('photos');
            $table->foreign('keyword_id')->references('id')->on('keywords');
            $table->index('photo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photo_keywords');
    }
}
