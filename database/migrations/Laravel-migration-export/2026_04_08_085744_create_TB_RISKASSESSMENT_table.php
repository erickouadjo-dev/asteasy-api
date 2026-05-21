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
            $table->string('RISK_INTITULE');
            $table->longText('RISK_DESCRIPTION');

            $table->unsignedBigInteger('PROFIL_EMPLOYE_ID');
            $table->foreign('PROFIL_EMPLOYE_ID')->references('ID')->on('TB_PROFIL_EMPLOYE');
            
            $table->unsignedBigInteger('RISK_LEVEL');
            $table->dateTime('DATE_MODIFICATION');
            $table->bigInteger('MODIFICATION');

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

