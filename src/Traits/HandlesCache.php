<?php

namespace Vendor\MyQuotesPackage\Traits;

/**
 * Trait HandlesCache - Maneja operaciones de caché ordenado para citas
 * 
 * Proporciona funcionalidades clave para:
 * - Búsquedas eficientes
 * - Inserción ordenada
 * - Mantenimiento de estructura de datos
 */
trait HandlesCache
{
    /**
     * Encuentra la posición de inserción usando búsqueda binaria
     * 
     * @param array &$cache Referencia al array de caché (ordenado por ID)
     * @param int $id ID de la cita a buscar
     * @return int Posición de inserción
     * 
     * Lógica:
     * 1. Busca la posición donde debería ubicarse el ID
     * 2. Mantiene el orden ascendente del array
     * 3. Complejidad algorítmica: O(log n)
     */
    protected function findInsertPosition(array &$cache, int $id): int
    {
        $low = 0;
        $high = count($cache);

        while ($low < $high) {
            $mid = intdiv($low + $high, 2);
            if ($cache[$mid]['id'] < $id) {
                $low = $mid + 1; // Buscar en mitad superior
            } else {
                $high = $mid;    // Buscar en mitad inferior
            }
        }

        return $low;
    }

    /**
     * Búsqueda binaria optimizada para array ordenado
     * 
     * @param array &$cache Referencia al array de caché
     * @param int $id ID a buscar
     * @return mixed|null Cita encontrada o null
     * 
     * Características:
     * - Reutiliza findInsertPosition para eficiencia
     * - Verifica match exacto en posición calculada
     */
    protected function binarySearch(array &$cache, int $id)
    {
        $pos = $this->findInsertPosition($cache, $id);
        return ($pos < count($cache) && $cache[$pos]['id'] === $id) 
            ? $cache[$pos] 
            : null;
    }

    /**
     * Inserta una cita manteniendo el orden del array
     * 
     * @param array &$cache Referencia al array de caché
     * @param array $quote Cita a insertar
     * 
     * Funcionamiento:
     * 1. Calcula posición con findInsertPosition
     * 2. Verifica duplicados
     * 3. Inserta usando array_splice
     * 4. Mantiene integridad del orden
     */
    protected function insertQuoteSorted(array &$cache, array $quote): void
    {
        $id = $quote['id'];
        $pos = $this->findInsertPosition($cache, $id);

        // Evita duplicados
        if (!(isset($cache[$pos]) && $cache[$pos]['id'] === $id)) {
            array_splice($cache, $pos, 0, [$quote]);
        }
    }
}