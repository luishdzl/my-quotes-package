<?php

namespace Vendor\MyQuotesPackage\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Http;
use ReflectionClass;
use Vendor\MyQuotesPackage\Services\QuoteService;
use Tests\TestCase;

/**
 * Clase para realizar pruebas unitarias del servicio de citas (QuoteService).
 * Verifica el comportamiento interno de los métodos del servicio.
 */
class QuoteServiceTest extends TestCase
{
    protected $service;

    /**
     * Configura el entorno y la instancia del servicio antes de cada prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app()->make(QuoteService::class);

        // Configuración personalizada para las pruebas.
        config([
            'quotes.base_url' => 'https://dummyjson.com/quotes',
            'quotes.rate_limit' => 5,
            'quotes.time_window' => 60
        ]);
    }

    /**
     * Prueba que `getAllQuotes` devuelve datos simulados correctamente y los almacena en caché.
     */
    #[Test]
    public function get_all_quotes_returns_cached_data()
    {
        // Simula una respuesta HTTP con citas.
        Http::fake([
            'https://dummyjson.com/quotes/quotes' => Http::response([
                'quotes' => [
                    ['id' => 1, 'quote' => 'Test 1'],
                    ['id' => 2, 'quote' => 'Test 2']
                ]
            ])
        ]);

        // Llama al método y verifica el resultado.
        $result = $this->service->getAllQuotes();
        $this->assertCount(2, $result['quotes']);
        $this->assertCacheContainsId(1);
        $this->assertCacheContainsId(2);
    }

    /**
     * Prueba que `getQuote` utiliza búsqueda binaria para obtener una cita específica.
     */
    #[Test]
    public function get_quote_uses_binary_search()
    {
        $this->setCache([
            ['id' => 3, 'quote' => 'Cached 3'],
            ['id' => 5, 'quote' => 'Cached 5'],
            ['id' => 8, 'quote' => 'Cached 8']
        ]);

        $quote = $this->service->getQuote(5);
        $this->assertEquals('Cached 5', $quote['quote']);
    }

    /**
     * Prueba que se respeta el límite de solicitudes configurado.
     */
    #[Test]
    public function rate_limiting_enforces_max_requests()
    {
        config(['quotes.rate_limit' => 2, 'quotes.time_window' => 60]);

        // Configura el estado inicial del contador de solicitudes.
        $this->setRequestCount(2);
        $this->setWindowStart(time());

        // Ejecuta el método y verifica el reinicio del contador.
        $this->service->getAllQuotes();
        $this->assertEquals(1, $this->getRequestCount());
    }

    // Métodos auxiliares para manipular y verificar el estado interno del servicio.

    private function setCache($data)
    {
        $reflection = new ReflectionClass($this->service);
        $cache = $reflection->getProperty('cache');
        $cache->setAccessible(true);
        $cache->setValue($this->service, $data);
    }

    private function assertCacheContainsId($id)
    {
        $cacheData = $this->getCache();
        $this->assertContains($id, array_column($cacheData, 'id'));
    }

    private function getCache()
    {
        $reflection = new ReflectionClass($this->service);
        $cache = $reflection->getProperty('cache');
        $cache->setAccessible(true);
        return $cache->getValue($this->service);
    }

    private function setRequestCount($count)
    {
        $reflection = new ReflectionClass($this->service);
        $prop = $reflection->getProperty('requestCount');
        $prop->setAccessible(true);
        $prop->setValue($this->service, $count);
    }

    private function getRequestCount()
    {
        $reflection = new ReflectionClass($this->service);
        $prop = $reflection->getProperty('requestCount');
        $prop->setAccessible(true);
        return $prop->getValue($this->service);
    }

    private function setWindowStart($time)
    {
        $reflection = new ReflectionClass($this->service);
        $prop = $reflection->getProperty('windowStart');
        $prop->setAccessible(true);
        $prop->setValue($this->service, $time);
    }
}
