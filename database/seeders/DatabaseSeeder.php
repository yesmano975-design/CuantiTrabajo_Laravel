<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        DB::table('roles')->insert([
            ['nombre' => 'administrador', 'descripcion' => 'Acceso total al sistema',    'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'secretaria',    'descripcion' => 'Acceso limitado al sistema', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Cargos
        DB::table('cargos')->insert([
            ['nombre' => 'Tractorista', 'descripcion' => 'Operador de maquinaria agricola',    'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Abonador',    'descripcion' => 'Aplicacion de abonos y fertilizantes','created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Fumigador',   'descripcion' => 'Aplicacion de pesticidas',            'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Regador',     'descripcion' => 'Gestion de sistemas de riego',        'created_at' => now(), 'updated_at' => now()],
        ]);

        // Usuario administrador inicial
        DB::table('users')->insert([
            'rol_id'     => 1,
            'nombre'     => 'Administrador',
            'apellido'   => '',
            'email'      => 'admin@cuantitrabajo.com',
            'password'   => Hash::make('admin123'),
            'telefono'   => '',
            'estado'     => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
