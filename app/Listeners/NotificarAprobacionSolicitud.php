<?php

namespace App\Listeners;

use App\Events\SolicitudAprobada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * PATRÓN OBSERVER — ConcreteObserver
 *
 * Reacciona al evento SolicitudAprobada enviando al solicitante
 * un correo con sus credenciales de acceso.
 * El servicio que disparó el evento no conoce este listener (DIP).
 */
class NotificarAprobacionSolicitud implements ShouldQueue
{
    public function handle(SolicitudAprobada $event): void
    {
        $solicitud = $event->solicitud;
        $usuario = $event->usuario;

        /**
         * En producción aquí se enviaría un Mailable real.
         * Se usa Log::info para demostrar el flujo sin infraestructura de email.
         */
        Log::info('Solicitud aprobada: notificando al usuario.', [
            'correo' => $solicitud->correo,
            'usuario' => $usuario->nombre,
        ]);

        // Mail::to($solicitud->correo)->send(new AprobacionMail($usuario));
    }
}
