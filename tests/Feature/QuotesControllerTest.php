<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Mockery\MockInterface;
use Vendor\MyQuotesPackage\Services\QuoteService;
use Vendor\MyQuotesPackage\Controllers\QuotesController;

/**
 * Pruebas unitarias para el controlador de citas (QuotesController)
 * 
 * Verifica:
 * - Estructuras de respuesta correctas
 * - Manejo de diferentes escenarios
 * - Integración con el servicio de citas
 */
class QuotesControllerTest extends TestCase
{
    /**
     * Prueba del método index()
     * 
     * Verifica que:
     * 1. La respuesta tenga la estructura esperada
     * 2. Los tipos de datos sean correctos
     * 3. Se integre correctamente con el servicio
     */
    public function test_index_returns_correct_structure()
    {
        // Configurar mock del servicio con datos de prueba
        $mockService = $this->mock(QuoteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAllQuotes')
                ->andReturn([  // Datos simulados del servicio
                    'quotes' => [['id' => 1], ['id' => 2]],
                    'total' => 100,
                    'skip' => 0,
                    'limit' => 30,
                ]);
        });

        // Crear instancia del controlador con el mock
        $controller = new QuotesController($mockService);
        $response = $controller->index(new Request());
        $data = $response->getData(true);

        // Validaciones principales
        $this->assertArrayHasKey('quotes', $data); // Debe contener listado de citas
        $this->assertArrayHasKey('total', $data);  // Debe mostrar el total disponible
        $this->assertArrayHasKey('skip', $data);   // Debe indicar registros omitidos
        $this->assertArrayHasKey('limit', $data);  // Debe mostrar límite de resultados
        
        // Validaciones de tipos de datos
        $this->assertIsArray($data['quotes']);  // Las citas deben ser array
        $this->assertIsInt($data['total']);     // El total debe ser numérico
    }

    /**
     * Prueba del método random()
     * 
     * Verifica que:
     * 1. Devuelva código de estado 200
     * 2. Contenga los campos requeridos
     * 3. Los valores coincidan con el mock
     */
    public function test_random_quote_returns_valid_response()
    {
        // Configurar mock con cita específica
        $mockService = $this->mock(QuoteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getRandomQuote')
                ->andReturn([  // Cita aleatoria simulada
                    'id' => 5, 
                    'quote' => 'Sample quote'
                ]);
        });

        // Ejecutar método del controlador
        $controller = new QuotesController($mockService);
        $response = $controller->random();

        // Validaciones de respuesta
        $this->assertEquals(200, $response->status());  // Código HTTP correcto
        $responseData = (array) $response->getData();
        $this->assertArrayHasKey('id', $responseData);    // Campo ID presente
        $this->assertArrayHasKey('quote', $responseData); // Campo quote presente
        $this->assertEquals(5, $responseData['id']);      // ID coincide con mock
    }

    /**
     * Prueba del método show() para cita no encontrada
     * 
     * Verifica que:
     * 1. Devuelva código 404 cuando no existe la cita
     * 2. Retorne estructura de error adecuada
     * 3. Maneje correctamente IDs inválidos
     */
    public function test_show_handles_not_found()
    {
        // Configurar mock para retorno nulo
        $mockService = $this->mock(QuoteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getQuote')
                ->with(999)  // Forzar búsqueda de ID inexistente
                ->andReturnNull();
        });
    
        // Ejecutar búsqueda con ID inválido
        $controller = new QuotesController($mockService);
        $response = $controller->show(999);  // ID de prueba: 999
    
        // Validar respuesta de error
        $this->assertEquals(404, $response->status());  // Código de error correcto
        $this->assertEquals(
            ['error' => 'Quote not found'],  // Mensaje esperado
            $response->getData(true)         // Datos de la respuesta
        );
    }
}