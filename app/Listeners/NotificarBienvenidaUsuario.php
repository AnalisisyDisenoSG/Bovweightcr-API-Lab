<?php

namespace App\Listeners;

use App\Events\UsuarioCreado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * PATRÓN OBSERVER — ConcreteObserver
 *
 * Reacciona al evento UsuarioCreado (creación directa por admin, HU-01.4)
 * enviando las credenciales de bienvenida al nuevo usuario.
 */
class NotificarBienvenidaUsuario implements ShouldQueue
{
    public function handle(UsuarioCreado $event): void
    {
        $usuario = $event->usuario;

        Log::info('Usuario creado por administrador: enviando bienvenida.', [
            'correo' => $usuario->correo,
            'nombre' => $usuario->nombre,
        ]);

        // Mail::to($usuario->correo)->send(new BienvenidaMail($usuario, $event->contrasenaPlana));
    }
}
