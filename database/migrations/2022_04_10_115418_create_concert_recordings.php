<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concert_recordings', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('description');
            $table->double('size');
            $table->timestamps();
            $table->foreignId('concerts_id')->constrained('concerts');
            $table->foreignId('type')->constrained('song_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concert_recordings');
    }
};
