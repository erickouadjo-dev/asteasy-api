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

        Schema::create('TB_RISK_SUBCATEGORY', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);

            $table->bigIncrements('ID'); 
            $table->string('INTITULE');
            $table->text('DESCRIPTION');
            $table->unsignedBigInteger('ID_RISK_CATEGORY')->nullable(); 
            $table->foreign('ID_RISK_CATEGORY')->references('ID')->on('TB_RISK_CATEGORY')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_RISK_SUBCATEGORY');
    }
};
