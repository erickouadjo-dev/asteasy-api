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

        Schema::create('TB_DOCUMENTATION', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            $table->string('INTITULE',500);
            $table->string('EDITION',500);
            $table->string('REF_DOCUMENTATION',500);
            $table->string('REVISION',500);
            $table->date('DATE_EDITION');
            $table->date('DATE_REVISION');
            $table->enum('STATUT',['DRAFT','ACEPTE','APPROVE'])->nullable()->comment('DRAFT // ACEPTE // APPROVE');
            $table->date('DATE_STATUT');
            $table->string('PUBLIE')->comment('Publié vers quel PROFIL');
          
            $table->unsignedBigInteger('PROFIL_EMPLOYE_ID');
            $table->foreign('PROFIL_EMPLOYE_ID')->references('ID')->on('TB_PROFIL_EMPLOYE')->nullable();
            
            $table->longText('FICHIERS_IMAGES');
           
            $table->unsignedBigInteger('BASE_ID');
            $table->foreign('BASE_ID')->references('ID')->on('TB_BASE');
            
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_DOCUMENTATION');
    }
};
