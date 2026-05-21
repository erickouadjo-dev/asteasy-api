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
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            $table->string('INTITULE',500);
            $table->longText('DESCRIPTION')->nullable();
            $table->date('DATE_OBTENTION')->nullable();
            $table->date('DATE_VALIDITE')->nullable();
            $table->bigInteger('DELAI_RENOUVELLEMENT')->nullable()->comment('Calcul de date Délai renouvellement  30J // 60 J');
            
            $table->unsignedBigInteger('DOCUMENTATION_ID')->nullable();
            $table->foreign('DOCUMENTATION_ID')->references('ID')->on('TB_DOCUMENTATION')->nullable();

            $table->LongText('FICHIERS_IMAGES');

            $table->unsignedBigInteger('ENTREPRISE_ID')->nullable();
            $table->foreign('ENTREPRISE_ID')->references('ID')->on('TB_ENTREPRISE')->nullable();
            
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
