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

        Schema::create('TB_EMPLOYE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id

            $table->string('PHONE_WHATSAPP')->nullable();
            $table->string('PHONE2')->nullable();
            $table->string('WHATSAPP')->nullable();
            $table->string('E-MAIL')->nullable();
            $table->string('ADRESSE1')->nullable();
            $table->string('ADRESSE2')->nullable();
            $table->string('ADRESSE3')->nullable();
            $table->string('CODE POSTAL')->nullable();
            $table->string('VILLE')->nullable();
            $table->string('PAYS')->nullable();
            $table->dateTime('DATE_EMBAUCHE')->nullable();
            $table->dateTime('DATE_FIN_CONTRAT')->nullable();
            $table->enum('STATUT', ['ACTIF', 'NON_ACTIF'])->comment('ACTIF ou  NON_ACTIF');
            $table->string('NATIONALITE1');
            $table->longText('FICHIER_PHOTO_PASSEPORT1')->nullable();
            $table->string('NATIONALITE2');
            $table->longText('FICHIER_PHOTO_NATIONALITE2')->nullable();
            $table->string('GROUPE_SANGUIN')->nullable();
            
            $table->unsignedBigInteger('PROFIL_EMPLOYE_ID')->nullable();
            $table->foreign('PROFIL_EMPLOYE_ID')->references('ID')->on('TB_PROFIL_EMPLOYE')->nullable();

            $table->unsignedBigInteger('USER_ID')->nullable();
            $table->foreign('USER_ID')->references('id')->on('utilisateurs')->nullable();

            $table->string('URG_NOM')->nullable();
            $table->string('URG_PRENOM')->nullable();
            $table->string('URG_LIEN_PARENTE')->nullable();
            $table->string('URG_TEL1')->nullable();
            $table->string('URG_TEL2')->nullable();
            $table->string('URG_EMAIL')->nullable();
            $table->string('MODIFICATION')->nullable();

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
        Schema::dropIfExists('TB_EMPLOYE');
    }
};

