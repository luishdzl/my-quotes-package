<?php

namespace Vendor\MyQuotesPackage\Traits;

trait HandlesCache
{
    /**
     * Encuentra la posición donde insertar una cita manteniendo el orden.
     */
    protected function findInsertPosition(array &$cache, int $id): int
    {
        $low = 0;
        $high = count($cache);

        while ($low < $high) {
            $mid = intdiv($low + $high, 2);
            if ($cache[$mid]['id'] < $id) {
                $low = $mid + 1;
            } else {
                $high = $mid;
            }
        }

        return $low;
    }

    /**
     * Búsqueda binaria en un array ordenado.
     */
    protected function binarySearch(array &$cache, int $id)
    {
        $pos = $this->findInsertPosition($cache, $id);
        return ($pos < count($cache) && $cache[$pos]['id'] === $id) ? $cache[$pos] : null;
    }

    /**
     * Inserta una cita manteniendo el orden por ID.
     */
    protected function insertQuoteSorted(array &$cache, array $quote): void
    {
        $id = $quote['id'];
        $pos = $this->findInsertPosition($cache, $id);

        if (!(isset($cache[$pos]) && $cache[$pos]['id'] === $id)) {
            array_splice($cache, $pos, 0, [$quote]);
        }
    }
}