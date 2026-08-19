<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad_laborals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('valor_actividad_id')->constrained('valor_actividades');
            $table->foreignId('lote_id')->constrained('lotes');
            $table->foreignId('trabajador_id')->constrained('trabajadores');
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha');
            $table->integer('cantidad')->default(1);
            $table->integer('numero_pasada')->default(1);
            $table->text('observacion')->nullable();
            $table->string('estado_confirmacion', 50)->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_laborals');
    }
};
