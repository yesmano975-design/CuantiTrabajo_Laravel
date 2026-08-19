<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_id')->constrained('cargos');
            $table->string('nombre', 100);
            $table->string('apellido', 100)->default('');
            $table->string('documento', 50)->unique();
            $table->string('correo', 150)->default('');
            $table->string('telefono', 20)->default('');
            $table->string('estado', 50)->default('activo');
            $table->string('huella', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
