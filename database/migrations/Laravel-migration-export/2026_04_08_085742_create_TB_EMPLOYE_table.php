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

            $table->unsignedBigInteger('USER_ID');
            $table->foreign('USER_ID')->references('id')->on('utilisateurs');

            $table->string('PHONE_whatsApp');
            $table->string('PHONE2');
            $table->string('WHATSAPP');
            $table->string('E-MAIL');
            $table->string('ADRESSE1');
            $table->string('ADRESSE2');
            $table->string('ADRESSE3');
            $table->string('CODE POSTAL');
            $table->string('VILLE');
            $table->string('PAYS');
            $table->dateTime('DATE_EMBAUCHE');
            $table->dateTime('DATE_FIN_CONTRAT');
            $table->enum('STATUT', ['ACTIF', 'NON_ACTIF'])->comment('ACTIF ou  NON_ACTIF');
            $table->string('NATIONALITE1');
            $table->longText('FICHIER_PHOTO_PASSEPORT1')->nullable();
            $table->string('NATIONALITE2');
            $table->longText('FICHIER_PHOTO_NATIONALITE2')->nullable();
            $table->string('GROUPE_SANGUIN');
            
            $table->unsignedBigInteger('PROFIL_EMPLOYE_ID');
            $table->foreign('PROFIL_EMPLOYE_ID')->references('ID')->on('TB_PROFIL_EMPLOYE');

            $table->string('URG_NOM');
            $table->string('URG_PRENOM');
            $table->string('URG_LIEN_PARENTE');
            $table->string('URG_TEL1');
            $table->string('URG_TEL2');
            $table->string('URG_EMAIL');
            $table->string('MODIFICATION');
            
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

