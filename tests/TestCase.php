<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Vendor\MyQuotesPackage\Providers\QuoteServiceProvider;

/**
 * Clase base para las pruebas.
 * Extiende de Orchestra Testbench, una herramienta que simplifica las pruebas en aplicaciones Laravel y paquetes.
 * Permite configurar el entorno necesario para ejecutar pruebas unitarias o funcionales en paquetes de Laravel.
 */
abstract class TestCase extends OrchestraTestCase
{
    /**
     * Especifica los proveedores de servicios que deben cargarse en el entorno de prueba.
     *
     * @param  \Illuminate\Foundation\Application  $app  La instancia de la aplicación de Laravel utilizada en las pruebas.
     * @return array  Lista de proveedores de servicios.
     */
    protected function getPackageProviders($app)
    {
        return [
            // Registra el proveedor de servicios de la librería QuoteService.
            QuoteServiceProvider::class,
        ];
    }
}
