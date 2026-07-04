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

        Schema::create('TB_TASKS_SAFETY', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id

            $table->unsignedBigInteger('ID_ORIGINE')->nullable(); 
            $table->foreign('ID_ORIGINE')->references('ID')->on('TB_TYPE_ORIGINE_ACTION')->nullable();

            $table->string('REF_ACTION')->comment('Voir');
            $table->enum('TASKS_TYPE', ["CURRATIF","PREVENTIF","AUTRE"]);
            $table->date('DATE_OUVERTURE')->comment('automatique date du jour / modifiable');
            $table->longText('DESCRIPTION_TASK');

            $table->unsignedBigInteger('ID_PUBLICATION')->nullable(); 
            $table->foreign('ID_PUBLICATION')->references('ID')->on('TB_PUBLICATION')->nullable();

            $table->unsignedBigInteger('ID_RECURRENCE')->nullable(); 
            $table->foreign('ID_RECURRENCE')->references('ID')->on('TB_RECURRENCE')->nullable();
            
            $table->unsignedBigInteger('ID_STATUT')->nullable(); 
            $table->foreign('ID_STATUT')->references('ID')->on('TB_STATUT')->nullable();
            
            $table->unsignedBigInteger('ID_AVANCEMENT')->nullable(); 
            $table->foreign('ID_AVANCEMENT')->references('ID')->on('TB_AVANCEMENT')->nullable();
            
            $table->date('DATE_BUTEE')->comment('Date limite de cloture');
            $table->date('DATE_FERMETURE');
            $table->date('RAPPEL_DATE_BUTEE')->comment('1 j avant .3j 7 j etc etc');

            $table->unsignedBigInteger('RESPONSABLE_PROPRIETAIRE')->nullable();
            $table->foreign('RESPONSABLE_PROPRIETAIRE')->references('id')->on('utilisateurs')->nullable();

            $table->longText('COMMENTAIRES_OBSERVATIONS');
            $table->longText('FICHIERS_IMAGES');

            $table->unsignedBigInteger('ID_TAG_ETIQUETTE')->nullable(); 
            $table->foreign('ID_TAG_ETIQUETTE')->references('ID')->on('TB_TAG_ETIQUETTE')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_TASKS_SAFETY');
    }
};
