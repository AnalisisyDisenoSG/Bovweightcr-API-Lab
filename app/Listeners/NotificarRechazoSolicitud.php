<?php

namespace App\Listeners;

use App\Events\SolicitudRechazada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * PATRÓN OBSERVER — ConcreteObserver
 *
 * Reacciona al evento SolicitudRechazada notificando al solicitante
 * el motivo del rechazo. Agregar más observers (SMS, webhook) no
 * requiere modificar ni el evento ni este listener (OCP).
 */
class NotificarRechazoSolicitud implements ShouldQueue
{
    public function handle(SolicitudRechazada $event): void
    {
        $solicitud = $event->solicitud;

        Log::info('Solicitud rechazada: notificando al solicitante.', [
            'correo' => $solicitud->correo,
            'motivo' => $event->motivoRechazo,
        ]);

        // Mail::to($solicitud->correo)->send(new RechazoMail($event->motivoRechazo));
    }
}
