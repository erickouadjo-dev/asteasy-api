<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilisateursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utilisateurs', function (Blueprint $table) {

            $table->bigIncrements('id'); //id
            $table->timestamps();
            $table->softDeletes();
            $table->string('nom', 100); //nom du compte
            $table->string('prenom',250);
            $table->string('email',50)->unique();
            $table->string('identifiant',300)->comment('Par défaut NOM.prenom');
            $table->longText('mot_de_passe')->nullable();
            $table->string('telephone',20);
            $table->longText('photo')->nullable(); //url de la photo du compte

            $table->enum('etat', ['actif', 'inactif'])->default('actif');
            $table->enum('type_utilisateur', ['ADMIN', 'POWER_USER', 'SIMPLE_USER', 'AUTRE'])->default('SIMPLE_USER');
            
            $table->unsignedBigInteger('USER_TYPE_ID')->nullable();
            $table->foreign('USER_TYPE_ID')->references('ID')->on('TB_USER_TYPE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utilisateurs');
    }
}
