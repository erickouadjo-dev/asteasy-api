<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraceActivitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trace_activites', function (Blueprint $table) {
            $table->bigIncrements('id'); //id
            $table->timestamps();
            $table->softDeletes();

            $table->enum('operation', ['AJOUT', 'MODIFICATION', 'LECTURE', 'SUPPRESSION', 'AUTRE']); //operation effectuée
            $table->longText('description');//description de l'opération
            $table->longText('donnees')->nullable(); //chaine JSON des données utilisées lors de l'opération
            $table->string('table_cible', 50)->nullable(); //nom de la table cible de l'opération

            $table->bigInteger('utilisateur')->nullable()->unsigned(); //id de l'utilisateur à l'origine de l'activité

            $table->foreign('utilisateur')->references('id')->on('utilisateurs');
            $table->index('operation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trace_activites');
    }
}
