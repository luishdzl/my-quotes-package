<?php

namespace Vendor\MyQuotesPackage\Tests\Unit;
use PHPUnit\Framework\Attributes\Test;

use Illuminate\Support\Facades\Http;
use ReflectionClass;
use Vendor\MyQuotesPackage\Services\QuoteService;
use Tests\TestCase;

class QuoteServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app()->make(QuoteService::class);
        config([
            'quotes.base_url' => 'https://dummyjson.com/quotes',
            'quotes.rate_limit' => 5,
            'quotes.time_window' => 60
        ]);
    }

    #[Test]
    public function get_all_quotes_returns_cached_data()
    {
        Http::fake([
            'https://dummyjson.com/quotes/quotes' => Http::response([
                'quotes' => [
                    ['id' => 1, 'quote' => 'Test 1'],
                    ['id' => 2, 'quote' => 'Test 2']
                ]
            ])
        ]);

        $result = $this->service->getAllQuotes();
        
        $this->assertCount(2, $result['quotes']);
        $this->assertCacheContainsId(1);
        $this->assertCacheContainsId(2);
    }

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

    #[Test]
    public function rate_limiting_enforces_max_requests()
    {
        config(['quotes.rate_limit' => 2, 'quotes.time_window' => 60]);
        
        // Forzar estado inicial
        $this->setRequestCount(2);
        $this->setWindowStart(time());

        // Ejecutar solicitud
        $this->service->getAllQuotes();
        
        // Verificar reinicio del contador
        $this->assertEquals(1, $this->getRequestCount());
    }

    // Helpers
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