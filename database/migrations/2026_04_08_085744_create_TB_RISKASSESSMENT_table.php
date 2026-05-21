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

        Schema::create('TB_RISK_ASSESSMENT', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            $table->string('RISK_INTITULE')->nullable();
            $table->longText('RISK_DESCRIPTION')->nullable();
            $table->unsignedBigInteger('PROFIL_EMPLOYE_ID')->nullable();
            $table->foreign('PROFIL_EMPLOYE_ID')->references('ID')->on('TB_PROFIL_EMPLOYE')->nullable();
            $table->bigInteger('RISK_LEVEL')->nullable();
            $table->dateTime('DATE_MODIFICATION')->nullable();
            $table->bigInteger('MODIFICATION')->nullable();

        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_RISK_ASSESSMENT');
    }
};

