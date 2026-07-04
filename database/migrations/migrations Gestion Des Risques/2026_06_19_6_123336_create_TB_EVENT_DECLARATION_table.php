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

        Schema::create('TB_EVENT_DECLARATION', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            $table->bigInteger('REF_EVENT')->autoIncrement()->comment('SOR-YYYY-XXX');
            $table->string('TYPE_EVENT')->comment('EVENT = OBLIGATOIRE  // DANGER = volontaire (non obligatoire)');
            $table->bigInteger('CONFIDENTIEL')->comment('Peut être CONFIDENTIEL (ANOMYME) si DANGER');
            
            $table->unsignedBigInteger('RAPORTEUR')->nullable();
            $table->foreign('RAPORTEUR')->references('id')->on('utilisateurs')->nullable();

            $table->string('BASE_OPERATEUR');
            $table->date('DATE_EVENT')->comment('date du jour par defaut en automatique. modifiable');
            $table->time('HEURE_EVENT');
            $table->string('CLIENT_MISSION')->comment('Nom du client ou de la mission');
            
            $table->unsignedBigInteger('ID_BASE_MATERIEL')->nullable()->comment('Aeronef Véhicule Equipement impliqué dans évènement');
            $table->foreign('ID_BASE_MATERIEL')->references('ID')->on('TB_BASE_MATERIEL')->nullable();

            $table->string('EVENT_LOCALISATION')->comment('Localisation - Lieu');
            $table->string('GPS_POSITION')->comment('Position GPS');
            $table->string('EVENT_DESCRIPTION');
            $table->longText('FICHIERS_IMAGES');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_EVENT_DECLARATION');
    }
};
