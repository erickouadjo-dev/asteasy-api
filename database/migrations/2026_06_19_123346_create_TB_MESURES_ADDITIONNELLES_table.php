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

        Schema::create('TB_MESURES_ADDITIONNELLES', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); 
            
            $table->string('INTITULE');
            $table->longText('DESCRIPTION');
            $table->string('FREQUENCE');
            $table->string('GRAVITE');
            $table->longText('COMMENTAIRES');
            
            $table->unsignedBigInteger('ID_TAG_ETIQUETTE')->nullable(); 
            $table->foreign('ID_TAG_ETIQUETTE')->references('ID')->on('TB_TAG_ETIQUETTE')->nullable();

            $table->unsignedBigInteger('DEPARTEMENT_RESPONSABLE')->nullable();
            $table->foreign('DEPARTEMENT_RESPONSABLE')->references('id')->on('utilisateurs')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_MESURES_ADDITIONNELLES');
    }
};
