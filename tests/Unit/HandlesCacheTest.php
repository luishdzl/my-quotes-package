<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Vendor\MyQuotesPackage\Traits\HandlesCache;

/**
 * Pruebas unitarias para el trait HandlesCache
 * 
 * Verifica el correcto funcionamiento de:
 * - Búsqueda binaria en caché ordenado
 * - Inserción manteniendo orden
 * - Detección de posición de inserción
 */
class HandlesCacheTest extends TestCase
{
    use HandlesCache;

    /**
     * Configuración inicial para cada prueba
     * Inicializa el array de caché simulado
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = []; // Simulación de caché en memoria
    }

    /**
     * Prueba del método findInsertPosition()
     * 
     * Casos verificados:
     * 1. Inserción al principio del array
     * 2. Inserción en posición intermedia
     * 3. Inserción al final del array
     */
    public function test_find_insert_position()
    {
        $cache = [
            ['id' => 1],
            ['id' => 3],
            ['id' => 5]
        ];

        // ID menor al primero (0)
        $this->assertEquals(0, $this->findInsertPosition($cache, 0));
        
        // ID entre 1 y 3 (posición 1)
        $this->assertEquals(1, $this->findInsertPosition($cache, 2));
        
        // ID mayor al último (posición 3)
        $this->assertEquals(3, $this->findInsertPosition($cache, 6));
    }

    /**
     * Prueba del método binarySearch()
     * 
     * Escenarios probados:
     * 1. Búsqueda exitosa de elemento existente
     * 2. Búsqueda de elemento inexistente
     */
    public function test_binary_search()
    {
        $cache = [
            ['id' => 1, 'text' => 'A'],
            ['id' => 3, 'text' => 'B'],
            ['id' => 5, 'text' => 'C']
        ];

        // Elemento existente (ID 3)
        $this->assertEquals($cache[1], $this->binarySearch($cache, 3));
        
        // Elemento no existente (ID 99)
        $this->assertNull($this->binarySearch($cache, 99));
    }

    /**
     * Prueba del método insertQuoteSorted()
     * 
     * Verifica:
     * 1. Inserción manteniendo orden ascendente
     * 2. Prevención de duplicados
     */
    public function test_insert_quote_sorted()
    {
        $cache = [];
        
        // Inserciones en orden no secuencial
        $this->insertQuoteSorted($cache, ['id' => 2]);
        $this->insertQuoteSorted($cache, ['id' => 1]);
        $this->insertQuoteSorted($cache, ['id' => 3]);
        
        // Verificar orden resultante
        $this->assertEquals(
            [1, 2, 3], 
            array_column($cache, 'id'),
            'Los IDs deben estar ordenados ascendentemente'
        );
        
        // Intentar insertar duplicado
        $this->insertQuoteSorted($cache, ['id' => 2]);
        
        // Verificar que no se insertó duplicado
        $this->assertEquals(
            [1, 2, 3], 
            array_column($cache, 'id'),
            'No debe permitir IDs duplicados'
        );
    }
}