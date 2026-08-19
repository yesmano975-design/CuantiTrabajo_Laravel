<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valor_actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_actividad_id')->constrained('tipo_actividades');
            $table->decimal('valor_unitario', 10, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('estado', 50)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valor_actividades');
    }
};
