<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('Formacion', function (Blueprint $table) {
        $table->string('RutaArchivo', 500)->nullable()->after('TituloObtenido');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Formacion', function (Blueprint $table) {
            //
        });
    }
};
