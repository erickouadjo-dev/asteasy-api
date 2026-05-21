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
            $table->string('INTITULE'255);
            $table->string('ADRESSE_1'255);
            $table->string('ADRESSE_2'255);
            $table->string('ADRESSE_3'255);
            $table->string('CODE_POSTAL', 255);
            $table->string('VILLE', 255);
            $table->string('PAYS', 255);
            $table->string('TELEPHONE', 255);
            $table->string('COURRIEL', 255);
            $table->longText('FICHIERS_IMAGES');
            $table->enum('TYPE_BASE', ['PRINCIPALE', 'SCONDAIRE', 'SITE_EN_LIGNE'])->comment('BASE PRINCIPALE // BASE SCONDAIRE // SITE EN LIGNE');
            $table->string('ACTIVITES');
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
