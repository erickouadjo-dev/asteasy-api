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

        Schema::create('TB_RISQUES', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); 
            
            $table->string('REFERENCE_RISK');
            $table->string('INTITULE_RISK');
            
            $table->unsignedBigInteger('ID_RISK_CATEGORY')->nullable();
            $table->foreign('ID_RISK_CATEGORY')->references('ID')->on('TB_RISK_CATEGORY')->nullable();

            $table->unsignedBigInteger('ID_RISK_SUBCATEGORY')->nullable();
            $table->foreign('ID_RISK_SUBCATEGORY')->references('ID')->on('RISK_SUBCATEGORY')->nullable();
            
            $table->string('CONSEQUENSE_ULTIME');

            $table->unsignedBigInteger('ID_MESURES_CONTROLE')->nullable();
            $table->foreign('ID_MESURES_CONTROLE')->references('ID')->on('MESURES_CONTROLE');
            
            $table->string('FREQUENCE_RISK_INITIAL');
            $table->string('GRAVITE_RISK_INITIAL');
            
            $table->unsignedBigInteger('ID_MATRICE_RISQUE')->nullable()->comment('NIVEAU_RISK_FINAL');
            $table->foreign('ID_MATRICE_RISQUE')->references('ID')->on('TB_MATRICE_RISQUE')->nullable();
            
            $table->unsignedBigInteger('ID_MESURES_ADDITIONNELLES')->nullable();
            $table->foreign('ID_MESURES_ADDITIONNELLES')->references('ID')->on('TB_MESURES_ADDITIONNELLES')->nullable();
            
            $table->string('FREQUENCE_RISK_FINAL');
            $table->string('GRAVITE_RISK_FINAL');
            
            $table->enum('NIVEAU_MAITRISE', ["ELEVE", "MOYENNE", "FAIBLE"]);
            
            $table->date('DATE_STATUT_RISK');
            $table->enum('STATUT_RISK', ["MAITRISE","PARTIELLEMENT_MAITRISE", "NON_MAITRISE"]);

            $table->unsignedBigInteger('RESPONSABLE')->nullable();
            $table->foreign('RESPONSABLE')->references('id')->on('utilisateurs')->nullable();

            $table->date('DATE_CONTROLE');
            $table->longText('COMMENTAIRES');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_RISQUES');
    }
};
