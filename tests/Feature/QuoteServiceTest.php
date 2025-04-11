<?php

namespace Vendor\MyQuotesPackage\Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Mockery;
use Tests\TestCase;
use Vendor\MyQuotesPackage\Services\QuoteService;

class QuoteServiceTest extends TestCase
{
    #[Test]
    public function api_returns_quotes_list()
    {
        $mockResponse = [
            'quotes' => [
                ['id' => 1, 'quote' => 'Test Quote', 'author' => 'Test Author']
            ],
            'total' => 1,
            'skip' => 0,
            'limit' => 1
        ];
    
        $mock = Mockery::mock(QuoteService::class);
        $mock->shouldReceive('getAllQuotes')
            ->andReturn($mockResponse);
    
        $this->app->instance('quotes', $mock);
    
        $response = $this->get('/api/quotes');
        
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

    #[Test]
    public function ui_route_returns_correct_view()
    {
        $response = $this->get('/quotes-ui');
        $response->assertStatus(200)
            ->assertViewIs('quotes::quotes-ui');
    }

    #[Test]
    public function random_quote_returns_valid_structure()
    {
        $mockResponse = [
            'id' => 99,
            'quote' => 'Random Quote',
            'author' => 'Test Author'
        ];
    
        $mock = Mockery::mock(QuoteService::class);
        $mock->shouldReceive('getRandomQuote')
            ->andReturn($mockResponse);
        
        $this->app->instance(QuoteService::class, $mock);
    
        $response = $this->get('/api/quotes/random');
        
        $response->assertStatus(200)
            ->assertExactJson($mockResponse); // Solo si la respuesta no incluye metadatos adicionales
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}