<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Mails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->bigIncrements('id'); //id
            $table->timestamps();
            $table->softDeletes();
            $table->string('classe_mailable', 50);//nom de la classe mailable à utiliser pour l'envoi
            $table->longText('parametres_mailable')->nullable();//chaine json des parametres d'envoi à passer au mailable
            $table->string('destinataire', 50);//email du destinataire du mail
            $table->enum('statut', ['EN_ATTENTE', 'ENVOYE', 'NON_ENVOYE'])->default('EN_ATTENTE'); //statut d'envoi du mail
            $table->enum('priorite', ['0_IMMEDIATE', '1_ELEVEE', '2_NORMALE', '3_BASSE'])->default('2_NORMALE'); //priorité d'envoi du mail
            $table->longText('raison_echec_envoi')->nullable();//chaine json détail sur l'échec d'envoi du mail
            $table->dateTime('date_envoi')->nullable();//date de l'envoi effectif du mail

            $table->bigInteger('utilisateur')->nullable()->unsigned(); //id de l'utilisateur destinataire du mail

            $table->foreign('utilisateur')->references('id')->on('utilisateurs');
            $table->index('classe_mailable');
            $table->index('statut');
            $table->index('priorite');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mails');
    }
}
