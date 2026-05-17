<?php

namespace Database\Seeders;

use App\Models\EstadoComercialGanado;
use Illuminate\Database\Seeder;

class EstadoComercialGanadoSeeder extends Seeder
{
    public function run(): void
    {
        $estados = ['Vendido', 'En venta', 'En propiedad'];

        foreach ($estados as $estado) {
            EstadoComercialGanado::firstOrCreate(['nombre' => $estado]);
        }

        $this->command->info('  ✔ EstadoComercialGanado: Vendido, En venta, En propiedad');
    }
}
