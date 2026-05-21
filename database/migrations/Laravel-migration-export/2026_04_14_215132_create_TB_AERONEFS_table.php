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

        Schema::create('TB_AERONEFS', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            $table->string('MARQUE',250);
            $table->string('TYPE_MODELE',500);
            $table->string('IMMATRICULATION',500);
            $table->string('SN',500);
            $table->date('DATE_MISE_EN_SERVICE');

            $table->unsignedBigInteger('DOCUMENT_ID');
            $table->foreign('DOCUMENT_ID')->references('ID')->on('TB_DOCUMENTS')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_AERONEFS');
    }
};
