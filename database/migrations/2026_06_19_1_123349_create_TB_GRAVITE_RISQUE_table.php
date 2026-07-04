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

        Schema::create('TB_GRAVITE_RISQUE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID');
            $table->string('INTITULE');
            $table->text('DESCRIPTION');
            $table->string('VALEUR')->comment('Chiffre ou lettre');

            $table->unsignedBigInteger('CREATED_BY')->nullable();
            $table->foreign('CREATED_BY')->references('id')->on('utilisateurs')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_GRAVITE_RISQUE');
    }
};
