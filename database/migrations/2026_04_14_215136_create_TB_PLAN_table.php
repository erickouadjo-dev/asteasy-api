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

        Schema::create('TB_PLAN', function (Blueprint $table) {
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('IS_DELETE')->default(false);
            $table->bigIncrements('ID'); //id
            
            // nom (Basic, Pro, Premium)
            $table->string('LIBELLE', 50)->nullable(); // Basic, Pro, Premium
            $table->decimal('PRIX', 8, 2);
            $table->string('DUREE')->nullable(); // mensuel, annuel
            $table->integer('LIMITE_UTILISATEURS');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TB_PLAN');
    }
};
