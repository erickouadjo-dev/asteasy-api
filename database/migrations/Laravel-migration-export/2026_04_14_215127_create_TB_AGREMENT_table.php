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
            $table->longText('DESCRIPTION');
            $table->date('DATE_OBTENTION');
            $table->date('DATE_VALIDITE');
            $table->bigInteger('DELAI_RENOUVELLEMENT')->comment('Calcul de date Délai renouvellement  30J // 60 J');
            
            $table->unsignedBigInteger('DOCUMENTATION_ID');
            $table->foreign('DOCUMENTATION_ID')->references('ID')->on('TB_DOCUMENTATION')->nullable();

            $table->LongText('FICHIERS_IMAGES');
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
