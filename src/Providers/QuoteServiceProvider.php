<?php

namespace Vendor\MyQuotesPackage\Providers;

use Illuminate\Support\ServiceProvider;

class QuoteServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Fusiona la configuración del paquete con la del usuario
        $this->mergeConfigFrom(__DIR__.'/../../config/quotes.php', 'quotes');
        
        // Registra el servicio para manejar la comunicación con la API
        $this->app->singleton('quotes', function ($app) {
            return new \Vendor\MyQuotesPackage\Services\QuoteService;
        });
    }

    public function boot()
    {
        // Publica el archivo de configuración para que pueda ser modificado
        $this->publishes([
            __DIR__.'/../../config/quotes.php' => config_path('quotes.php'),
        ], 'config');

        // Publica los assets de la UI de Vue.js (se publicarán en public/vendor/my-quotes-package)
        $this->publishes([
            __DIR__.'/../../resources/dist' => public_path('vendor/my-quotes-package'),
        ], 'public');

        // Carga las rutas del paquete: API y Web
        $this->loadRoutesFrom(__DIR__.'/../../src/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../../src/routes/web.php');

         // Carga las vistas del paquete con el alias "quotes"
         $this->loadViewsFrom(__DIR__.'/../../resources/views', 'quotes');
    }
}