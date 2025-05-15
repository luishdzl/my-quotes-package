<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Vendor\MyQuotesPackage\Providers\QuoteServiceProvider;

/**
 * Pruebas de integración para las rutas API del paquete de citas
 * 
 * Verifica:
 * - Registro correcto de las rutas
 * - Estructura de respuestas válidas
 * - Comportamiento básico de los endpoints
 */
class ApiRoutesTest extends TestCase
{
    /**
     * Configuración inicial para cada prueba
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Registrar el proveedor de servicios que contiene las rutas
        $this->app->register(QuoteServiceProvider::class);
    }

    /**
     * Prueba que verifica el registro correcto de las rutas en el sistema
     * 
     * Comprueba que las siguientes rutas estén registradas:
     * - GET /api/quotes (listado paginado)
     * - GET /api/quotes/random (cita aleatoria)
     * - GET /api/quotes/{id} (cita por ID)
     */
    public function test_api_routes_are_registered()
    {
        $this->assertTrue(
            $this->routeUriExists('GET', 'api/quotes'),
            'La ruta api/quotes no está registrada'
        );
        
        $this->assertTrue(
            $this->routeUriExists('GET', 'api/quotes/random'),
            'La ruta api/quotes/random no está registrada'
        );
        
        $this->assertTrue(
            $this->routeUriExists('GET', 'api/quotes/{id}'),
            'La ruta api/quotes/{id} no está registrada'
        );
    }
    
    /**
     * Método helper para verificar la existencia de una ruta
     * 
     * @param string $method Método HTTP (GET, POST, etc.)
     * @param string $uri URI a verificar
     * @return bool True si la ruta existe y responde al método
     */
    private function routeUriExists(string $method, string $uri): bool
    {
        $routes = Route::getRoutes()->getRoutes();
    
        foreach ($routes as $route) {
            if (in_array(strtoupper($method), $route->methods()) 
                && $route->uri() === $uri) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prueba del endpoint /api/quotes
     * 
     * Verifica:
     * - Código de estado 200
     * - Estructura JSON correcta
     * - Presencia de campos esenciales
     */
    public function test_quotes_route_returns_valid_response()
    {
        $response = $this->get('/api/quotes');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'quotes',    // Listado de citas
                'total',     // Total de registros
                'skip',       // Registros omitidos
                'limit'       // Límite de resultados
            ]);
    }

    /**
     * Prueba del endpoint /api/quotes/random
     * 
     * Verifica:
     * - Código de estado 200
     * - Estructura básica de una cita
     */
    public function test_random_quote_route_responds_correctly()
    {
        $response = $this->get('/api/quotes/random');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',     // Identificador único
                'quote'   // Texto de la cita
            ]);
    }
}