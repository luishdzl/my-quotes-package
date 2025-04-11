<?php

namespace Vendor\MyQuotesPackage\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Clase QuoteServiceProvider.
 * Proveedor de servicios que configura y registra el paquete MyQuotesPackage dentro de una aplicación Laravel.
 */
class QuoteServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios y configuraciones del paquete.
     */
    public function register()
    {
        // Fusiona la configuración del paquete con la configuración del usuario.
        // Permite que las configuraciones predeterminadas del paquete sean sobrescritas por el usuario.
        $this->mergeConfigFrom(__DIR__.'/../../config/quotes.php', 'quotes');

        // Registra el servicio como un singleton dentro del contenedor de la aplicación.
        // Asegura que solo exista una instancia de QuoteService durante el ciclo de vida de la aplicación.
        $this->app->singleton('quotes', function ($app) {
            return new \Vendor\MyQuotesPackage\Services\QuoteService;
        });
    }

    /**
     * Ejecuta tareas de inicialización y publica recursos al momento de cargar el paquete.
     */
    public function boot()
    {
        // Publica el archivo de configuración para que el usuario pueda modificarlo.
        $this->publishes([
            __DIR__.'/../../config/quotes.php' => config_path('quotes.php'),
        ], 'config');

        // Publica los assets de la interfaz de usuario (como archivos JS y CSS) en el directorio público del proyecto.
        $this->publishes([
            __DIR__.'/../../public/vendor/my-quotes-package' => public_path('vendor/my-quotes-package'),
        ], 'public');

        // Carga las rutas específicas del paquete, tanto para la API como para la Web.
        $this->loadRoutesFrom(__DIR__.'/../../src/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../../src/routes/web.php');

        // Carga las vistas del paquete y las asocia con un alias ("quotes") para que puedan ser referenciadas fácilmente.
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'quotes');
    }
}
