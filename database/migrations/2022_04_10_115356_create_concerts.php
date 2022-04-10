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
        Schema::create('concerts', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('place_street');
            $table->string('place_number', 5);
            $table->string('place_description');
            $table->string('organizer_description');
            $table->timestamps();
            $table->foreignId('band_id')
                ->constrained('bands');
            $table->foreignId('place_plz')
                ->constrained('places', 'plz');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concerts');
    }
};
