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

        Schema::create('TB_HISTORIQUE_PROFIL_EMPLOYE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            $table->unsignedBigInteger('EMPLOYE_ID')->nullable();
            $table->foreign('EMPLOYE_ID')->references('ID')->on('TB_EMPLOYE')->nullable();

            $table->unsignedBigInteger('PROFIL_EMPLOYE_ID')->nullable();
            $table->foreign('PROFIL_EMPLOYE_ID')->references('ID')->on('TB_PROFIL_EMPLOYE')->nullable();

            $table->unsignedBigInteger('NOMINATION_EMPLOYE_ID')->nullable();
            $table->foreign('NOMINATION_EMPLOYE_ID')->references('ID')->on('TB_NOMINATION_EMPLOYE')->nullable();

            $table->dateTime('DATE_DEBUT')->nullable();
            $table->dateTime('DATE_FIN')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_HISTORIQUE_PROFIL_EMPLOYE');
    }
};

