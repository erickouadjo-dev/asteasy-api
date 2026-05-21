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

        Schema::create('TB_ROLE_PERMISSION', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id

            $table->unsignedBigInteger('ROLE_ID')->nullable();
            $table->foreign('ROLE_ID')->references('ID')->on('TB_ROLE')->nullable();
 
            $table->unsignedBigInteger('PERMISSION_ID')->nullable();
            $table->foreign('PERMISSION_ID')->references('ID')->on('TB_PERMISSION')->nullable();

            $table->unsignedBigInteger('UTILISATEUR_ID')->nullable();
            $table->foreign('UTILISATEUR_ID')->references('id')->on('utilisateurs')->nullable();

        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_ROLE_PERMISSION');
    }
};
