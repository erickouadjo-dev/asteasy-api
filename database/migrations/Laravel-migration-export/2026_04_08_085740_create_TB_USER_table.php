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

        Schema::create('TB_USER', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); //id
            $table->string('NAME');
            $table->string('SURNAME');
            $table->string('E-MAIL');
            $table->longText('PASSWORD');
            $table->string('PHONE1');
            $table->string('IDENTIFIANT')->comment('Par défaut NOM.prenom');
            
            $table->unsignedBigInteger('USER_TYPE_ID');
            $table->foreign('USER_TYPE_ID')->references('ID')->on('TB_USER_TYPE');

        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_USER');
    }
};

