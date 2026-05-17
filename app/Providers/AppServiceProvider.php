<?php

namespace App\Providers;

use App\Contracts\ISolicitudRegistroRepository;
use App\Contracts\IUserFactory;
use App\Contracts\IUserRepository;
use App\Events\SolicitudAprobada;
use App\Events\SolicitudRechazada;
use App\Events\UsuarioCreado;
use App\Factories\UserFactory;
use App\Listeners\NotificarAprobacionSolicitud;
use App\Listeners\NotificarBienvenidaUsuario;
use App\Listeners\NotificarRechazoSolicitud;
use App\Repositories\EloquentSolicitudRegistroRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra los bindings del Service Container.
     *
     * PATRÓN REPOSITORY (DIP):
     *   Las interfaces se ligan a sus implementaciones Eloquent.
     *   Para tests se puede hacer ->bind() a InMemory* sin tocar ningún servicio.
     *
     * PATRÓN FACTORY (singleton):
     *   UserFactory se registra como singleton para reutilizar la misma instancia.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(IUserRepository::class, EloquentUserRepository::class);
        $this->app->bind(ISolicitudRegistroRepository::class, EloquentSolicitudRegistroRepository::class);

        // Factory binding (singleton: se crea una sola instancia)
        $this->app->singleton(IUserFactory::class, UserFactory::class);
    }

    /**
     * PATRÓN OBSERVER:
     *   Registra los Listeners a sus Events correspondientes.
     *   Agregar un nuevo observer solo requiere añadir una línea aquí (OCP).
     */
    public function boot(): void
    {
        Event::listen(SolicitudAprobada::class, NotificarAprobacionSolicitud::class);
        Event::listen(SolicitudRechazada::class, NotificarRechazoSolicitud::class);
        Event::listen(UsuarioCreado::class, NotificarBienvenidaUsuario::class);
    }
}
