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

        Schema::create('TB_SAFETY_ACTION', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            
            $table->string('INTITULE');
            $table->text('DESCRIPTION');
            $table->date('DATE_OUVERTURE');

            $table->unsignedBigInteger('ID_TYPE_ORIGINE_ACTION')->comment('Limité à ANALYSE EVENEMENT uniquement !! (pas  de : // FORMATION // PUBLICATION //   EVENEMENT // GESTION DU CHANGEMENT /// etc)')->nullable();
            $table->foreign('ID_TYPE_ORIGINE_ACTION')->references('ID')->on('TB_TYPE_ORIGINE_ACTION')->nullable();
            
            $table->unsignedBigInteger('ID_TASK_SAFETY')->nullable();
            $table->foreign('ID_TASK_SAFETY')->references('ID')->on('TB_TASK_SAFETY')->nullable();
            
            $table->longText('FICHIERS_IMAGES')->nullable();
            
            $table->unsignedBigInteger('ID_RECURRENCE')->nullable(); 
            $table->foreign('ID_RECURRENCE')->references('ID')->on('TB_RECURRENCE')->nullable();
            
            $table->unsignedBigInteger('ID_STATUT')->nullable(); 
            $table->foreign('ID_STATUT')->references('ID')->on('TB_STATUT')->nullable();
            
            $table->unsignedBigInteger('ID_AVANCEMENT')->nullable(); 
            $table->foreign('ID_AVANCEMENT')->references('ID')->on('TB_AVANCEMENT')->nullable();

            $table->date('DATE_CLOTURE')->nullable();
            
            $table->unsignedBigInteger('RESPONSABLE')->nullable();
            $table->foreign('RESPONSABLE')->references('id')->on('utilisateurs')->nullable();

            $table->unsignedBigInteger('ID_TAG')->nullable();
            $table->foreign('ID_TAG')->references('ID')->on('TB_TAG_ETIQUETTE')->nullable();

            $table->enum('ACTION_LIEE_RISQUE', ["OUI", "NON","NON_APPLICABLE"]);

            $table->unsignedBigInteger('ID_RISQUE')->nullable();
            $table->foreign('ID_RISQUE')->references('ID')->on('TB_RISQUES')->nullable();
            $table->enum('ACTION_FREQUENCE_RISQUE', ["OUI", "NON","NON_APPLICABLE"]);
            $table->enum('ACTION_GRAVITE_RISQUE', ["OUI", "NON","NON_APPLICABLE"]);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_SAFETY_ACTIONS');
    }
};
