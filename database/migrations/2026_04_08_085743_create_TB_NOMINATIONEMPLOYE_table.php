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

        Schema::create('TB_NOMINATION_EMPLOYE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            $table->unsignedBigInteger('EMPLOYE_ID')->nullable();
            $table->foreign('EMPLOYE_ID')->references('ID')->on('TB_EMPLOYE')->nullable();

            $table->string('INTITULE_POSTE')->nullable();
            $table->string('DESCRIPTION_POSTE', 500)->nullable();
            $table->string('AGREMENT_CONCERNE')->nullable();
            $table->dateTime('DATE_ACCEPTATION')->nullable();
            $table->longText('FICHIERS')->nullable();
            $table->dateTime('DATE_PRISE_DE_FONCTION')->nullable();
            $table->dateTime('DATE_FIN')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_NOMINATION_EMPLOYE');
    }
};

