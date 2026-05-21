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

        Schema::create('TB_ENTREPRISE', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            $table->string('NON_SOCIETE',500);
            $table->unsignedBigInteger('AGREMENT_ID');
            $table->foreign('AGREMENT_ID')->references('ID')->on('TB_AGREMENT')->nullable();
            
            $table->unsignedBigInteger('BASE_ID');
            $table->foreign('BASE_ID')->references('ID')->on('TB_BASE')->nullable();
            
            $table->string('SITE_WEB',500);
            $table->string('TELEPHONE',500);
            $table->longText('FICHIER_LOGO');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_ENTREPRISE');
    }
};
