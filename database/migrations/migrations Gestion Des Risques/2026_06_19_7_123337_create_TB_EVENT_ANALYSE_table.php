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

        Schema::create('TB_EVENT_ANALYSE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            $table->unsignedBigInteger('ID_EVENT_DECLARATION')->nullable();
            $table->foreign('ID_EVENT_DECLARATION')->references('ID')->on('TB_EVENT_DECLARATION')->nullable();
            
            $table->unsignedBigInteger('ID_STATUT_EVENEMENT')->nullable();
            $table->foreign('ID_STATUT_EVENEMENT')->references('ID')->on('TB_STATUT_EVENEMENT')->nullable();
            
            $table->date('DATE_ANALYSE')->comment('Date ouverture analyse // Automatique');
            $table->text('TITRE_EVENT')->comment('Titre / Résumé de lévènement');
            $table->longText('EVENT_DESCRIPTION_ANALYSE')->comment('Duplication EVENT_DESCRIPTION mais modifiable');
            $table->bigInteger('EVENT_LOCATION_ANALYSE')->comment('Duplication EVENT_LOCATION mais modifiable');
            $table->enum('EVENT_TYPE', ['ACCIDENT', 'INCIDENT MAJEUR', 'INCIDENT MINEUR', 'DANGER'])->comment('Liste de valeur (4) : ACCIDENT, INCIDENT MAJEUR, INCIDENT MINEUR, DANGER');
            $table->text('ROOTCAUSE');
            $table->text('FACTEURS_CONTRIBUTIFS');

            $table->unsignedBigInteger('ID_RISQUE')->nullable();
            $table->foreign('ID_RISQUE')->references('id')->on('RISQUES');

            $table->unsignedBigInteger('ID_MATRICE_RISQUE')->nullable();
            $table->foreign('ID_MATRICE_RISQUE')->references('ID')->on('TB_MATRICE_RISQUE');

            $table->unsignedBigInteger('ID_SAFETY_ACTION')->nullable();
            $table->foreign('ID_SAFETY_ACTION')->references('ID')->on('TB_SAFETY_ACTION');
            
            $table->text('RISKLEVEL_FINAL_ACCEPTANCE')->comment('Validation Responsable ou DR');

            $table->unsignedBigInteger('ANALYSE_PAR')->nullable();
            $table->foreign('ANALYSE_PAR')->references('id')->on('utilisateurs')->nullable();
            
            $table->enum('INFO_AUTORITE', ["OUI","NON"]);
            $table->date('DATE_INFO_AUTORITE')->nullable();
            
            $table->enum('INFO_CLIENT', ["OUI","NON"]);
            $table->date('DATE_INFO_CLIENT')->nullable();
            
            $table->text('COMMENTAIRE');
            
            $table->enum('RISQUE_SUBSIDIAIRE', ["OUI","NON"]);
            
            $table->unsignedBigInteger('ID_STATUT')->nullable(); 
            $table->foreign('ID_STATUT')->references('ID')->on('TB_STATUT')->nullable();
            
            $table->enum('PUBLIE', ["OUI","NON"]);
            $table->date('DATE_PUBLIE')->nullable();

            $table->unsignedBigInteger('ID_TAG_ETIQUETTE')->nullable(); 
            $table->foreign('ID_TAG_ETIQUETTE')->references('ID')->on('TB_TAG_ETIQUETTE')->nullable();
            
            $table->date('DATE_CLOTURE');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_EVENT_ANALYSE');
    }
};
