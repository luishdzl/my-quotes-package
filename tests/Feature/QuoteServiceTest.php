<?php

namespace Vendor\MyQuotesPackage\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Mockery;
use Tests\TestCase;
use Vendor\MyQuotesPackage\Services\QuoteService;

/**
 * Clase para realizar pruebas funcionales del servicio de citas (QuoteService).
 * Verifica que las rutas y respuestas de la API funcionen como se espera.
 */
class QuoteServiceTest extends TestCase
{
    /**
     * Prueba que la API devuelve una lista de citas correctamente.
     */
    #[Test]
    public function api_returns_quotes_list()
    {
        // Respuesta simulada que representa una lista de citas.
        $mockResponse = [
            'quotes' => [
                ['id' => 1, 'quote' => 'Test Quote', 'author' => 'Test Author']
            ],
            'total' => 1,
            'skip' => 0,
            'limit' => 1
        ];

        // Simula el servicio de citas.
        $mock = Mockery::mock(QuoteService::class);
        $mock->shouldReceive('getAllQuotes')->andReturn($mockResponse);

        // Registra el servicio simulado en el contenedor de la aplicación.
        $this->app->instance('quotes', $mock);

        // Realiza una solicitud GET a la ruta de la API.
        $response = $this->get('/api/quotes');

        // Verifica que la respuesta tenga un estado 200 y la estructura JSON esperada.
        $response->assertStatus(200)
            ->assertJsonStructure([
                'quotes' => [
                    '*' => ['id', 'quote', 'author']
                ],
                'total',
                'skip',
                'limit'
            ]);
    }

    /**
     * Prueba que la ruta de la interfaz de usuario devuelve la vista correcta.
     */
    #[Test]
    public function ui_route_returns_correct_view()
    {
        // Realiza una solicitud GET a la ruta de la interfaz de usuario.
        $response = $this->get('/quotes-ui');

        // Verifica que la vista retornada sea la esperada.
        $response->assertStatus(200)
            ->assertViewIs('quotes::quotes-ui');
    }

    /**
     * Prueba que la API devuelve una cita aleatoria con la estructura esperada.
     */
    #[Test]
    public function random_quote_returns_valid_structure()
    {
        // Respuesta simulada para una cita aleatoria.
        $mockResponse = [
            'id' => 99,
            'quote' => 'Random Quote',
            'author' => 'Test Author'
        ];

        // Simula el servicio de citas.
        $mock = Mockery::mock(QuoteService::class);
        $mock->shouldReceive('getRandomQuote')->andReturn($mockResponse);

        // Registra el servicio simulado.
        $this->app->instance(QuoteService::class, $mock);

        // Realiza una solicitud GET a la ruta de cita aleatoria.
        $response = $this->get('/api/quotes/random');

        // Verifica que la respuesta tenga un estado 200 y coincida exactamente con la simulada.
        $response->assertStatus(200)
            ->assertExactJson($mockResponse);
    }

    /**
     * Limpia los mocks y recursos después de cada prueba.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
