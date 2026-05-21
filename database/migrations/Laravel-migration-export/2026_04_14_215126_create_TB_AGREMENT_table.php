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

        Schema::create('TB_AGREMENT', function (Blueprint $table) {
            $table->bigIncrements('ID'); //id
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigInteger('INTITULE');
            $table->bigInteger('DESCRIPTION');
            $table->date('DATE_OBTENTION');
            $table->date('DATE_VALIDITE');
            $table->bigInteger('DELAI_RENOUVELLEMENT*')->comment('Calcul de date Délai renouvellement  30J // 60 J');
            $table->unsignedBigInteger('id_DOCUMENTATION');
            $table->foreign('id_DOCUMENTATION')->references('ID')->on('TB_DOCUMENTATION');
            $table->bigInteger('FICHIERS_IMAGES');
            $table->dateTime('DATE_CREATION');
            $table->dateTime('DATE_MODIFICATION');
            $table->bigInteger('MODIFICATION');
            $table->dateTime('SI_DELETE');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_AGREMENT');
    }
};
