<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('TB_BASE_MATERIEL', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            $table->unsignedBigInteger('BASE_ID')->nullable();
            $table->foreign('BASE_ID')->references('ID')->on('TB_BASE')->nullable();

            $table->unsignedBigInteger('AERONEF_ID')->nullable();
            $table->foreign('AERONEF_ID')->references('ID')->on('TB_AERONEFS')->nullable();

            $table->unsignedBigInteger('VEHICULE_ID')->nullable();
            $table->foreign('VEHICULE_ID')->references('ID')->on('TB_VEHICULES')->nullable();

            $table->unsignedBigInteger('EQUIPEMENT_ID')->nullable();
            $table->foreign('EQUIPEMENT_ID')->references('ID')->on('TB_EQUIPEMENTS')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_BASE_MATERIEL');
    }
};
