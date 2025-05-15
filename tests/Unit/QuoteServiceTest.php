<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Vendor\MyQuotesPackage\Services\QuoteService;

/**
 * Pruebas unitarias para el servicio QuoteService
 * 
 * Verifica:
 * - Integración con API externa
 * - Manejo de rate limiting
 * - Funcionamiento del sistema de caché
 */
class QuoteServiceTest extends TestCase
{
    protected $service;

    /**
     * Configuración inicial para cada prueba
     * - Crea nueva instancia del servicio
     * - Limpia caché
     * - Establece configuración básica
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuoteService();
        Cache::flush(); // Limpiar caché antes de cada prueba
        
        // Configurar valores para las pruebas
        Config::set('quotes', [
            'base_url' => 'https://dummyjson.com',
            'rate_limit' => 3, // 3 solicitudes permitidas
            'time_window' => 60 // Ventana de 60 segundos
        ]);
    }

    /**
     * Limpieza después de cada prueba
     * - Cierra instancias de Mockery
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Prueba obtención exitosa de citas
     * - Verifica estructura de respuesta
     * - Comprueba almacenamiento en caché
     */
    public function test_get_all_quotes_success()
    {
        // Configurar respuesta HTTP falsa
        Http::fake([
            '*/quotes?skip=0&limit=30' => Http::response([
                'quotes' => [['id' => 1], ['id' => 2]],
                'total' => 100
            ], 200)
        ]);

        $result = $this->service->getAllQuotes();
        
        // Validaciones principales
        $this->assertCount(2, $result['quotes']); // 2 citas en respuesta
        $this->assertArrayHasKey('total', $result); // Campo total presente
        $this->assertNotEmpty(Cache::get(QuoteService::CACHE_KEY)); // Caché no vacío
    }

    /**
     * Prueba de límite de tasa (rate limiting)
     * - Permite 3 solicitudes exitosas
     * - Bloquea la 4ta con error 429
     */
    public function test_rate_limiting_enforcement()
    {
        // Configurar respuesta genérica
        Http::fake([
            '*/quotes*' => Http::response([
                'quotes' => [['id' => 1]],
                'total' => 1
            ], 200)
        ]);
        
        // Ejecutar 3 solicitudes permitidas
        for ($i = 0; $i < 3; $i++) {
            $result = $this->service->getAllQuotes();
            $this->assertIsArray($result); // Verificar tipo de respuesta
        }
        
        // Verificar bloqueo en 4to intento
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessageMatches('/Demasiadas solicitudes/');
        
        try {
            $this->service->getAllQuotes();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            // Validar código de estado HTTP 429
            $this->assertEquals(429, $e->getStatusCode());
            throw $e;
        }
    }

    /**
     * Prueba de integración con caché
     * - Verifica que se actualiza el caché
     * - Comprueba estructura de datos almacenada
     */
    public function test_cache_integration()
    {
        Http::fake([
            '*/quotes*' => Http::response([
                'quotes' => [['id' => 1]]
            ])
        ]);
        
        // Validar caché vacío inicial
        $this->assertEmpty(Cache::get(QuoteService::CACHE_KEY));
        
        // Ejecutar método que actualiza caché
        $this->service->getAllQuotes();
        
        // Verificar contenido del caché
        $cached = Cache::get(QuoteService::CACHE_KEY);
        $this->assertCount(1, $cached); // 1 elemento en caché
        $this->assertEquals(1, $cached[0]['id']); // ID correcto
    }
}