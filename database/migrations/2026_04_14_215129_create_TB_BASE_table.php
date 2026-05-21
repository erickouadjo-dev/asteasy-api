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

        Schema::create('TB_BASE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            $table->string('INTITULE',255);
            $table->string('ADRESSE_1',255)->nullable();
            $table->string('ADRESSE_2',255)->nullable();
            $table->string('ADRESSE_3',255)->nullable();
            $table->string('CODE_POSTAL', 255)->nullable();
            $table->string('VILLE', 255)->nullable();
            $table->string('PAYS', 255)->nullable();
            $table->string('TELEPHONE', 255)->nullable();
            $table->string('COURRIEL', 255)->nullable();
            $table->longText('FICHIERS_IMAGES')->nullable();
            $table->enum('TYPE_BASE', ['PRINCIPALE', 'SCONDAIRE', 'SITE_EN_LIGNE'])->comment('BASE PRINCIPALE // BASE SCONDAIRE // SITE EN LIGNE')->nullable();
            $table->string('ACTIVITES')->nullable();

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
        Schema::dropIfExists('TB_BASE');
    }
};
