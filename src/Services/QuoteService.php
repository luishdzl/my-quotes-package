<?php

namespace Vendor\MyQuotesPackage\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Vendor\MyQuotesPackage\Traits\HandlesCache;

/**
 * Servicio para gestión de citas con integración API y caché persistente
 * 
 * Funcionalidades principales:
 * - Consumo de API externa con rate limiting
 * - Almacenamiento en caché ordenado
 * - Búsqueda eficiente
 * - Paginación
 */
class QuoteService
{
    use HandlesCache; // Reutiliza lógica de gestión de caché

    /**
     * Clave para almacenamiento en caché de citas
     */
    const CACHE_KEY = 'quotes_cache';

    /**
     * Obtiene citas almacenadas en caché
     * @return array Listado de citas ordenadas
     */
    protected function getCachedQuotes(): array
    {
        return Cache::get(self::CACHE_KEY, []);
    }

    /**
     * Guarda citas en caché con expiración de 1 hora
     * @param array $quotes Listado de citas a almacenar
     */
    protected function saveCachedQuotes(array $quotes): void
    {
        Cache::put(self::CACHE_KEY, $quotes, now()->addHours(1));
    }

    /**
     * Gestiona límite de solicitudes usando caché persistente
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function handleRateLimiting(): void
    {
        $max = Config::get('quotes.rate_limit', 60); // Límite máximo de peticiones
        $window = Config::get('quotes.time_window', 60); // Ventana en segundos
        $cacheKey = 'quote_service_rate_limit'; // Clave de caché para rate limiting

        $currentState = Cache::get($cacheKey, ['count' => 0, 'window_start' => time()]);
        $currentTime = time();
        $elapsed = $currentTime - $currentState['window_start'];

        // Reinicio de ventana temporal
        if ($elapsed > $window) {
            $currentState = ['count' => 0, 'window_start' => $currentTime];
        }

        // Control de exceso de peticiones
        if ($currentState['count'] >= $max) {
            abort(429, "Demasiadas solicitudes. Espere " . ($window - $elapsed) . " segundos.");
        }

        // Actualización del contador
        $currentState['count']++;
        Cache::put($cacheKey, $currentState, $window);
    }

    /**
     * Obtiene citas paginadas desde la API
     * @param int $skip Registros a omitir
     * @param int $limit Límite de resultados
     * @return array Estructura con citas y metadatos
     */
    public function getAllQuotes(int $skip = 0, int $limit = 30): array
    {
        $this->handleRateLimiting();

        // Construcción de URL con parámetros de paginación
        $url = Config::get('quotes.base_url') . "/quotes?skip={$skip}&limit={$limit}";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            $cachedQuotes = $this->getCachedQuotes();

            // Actualización de caché con nuevos resultados
            if (isset($data['quotes']) && is_array($data['quotes'])) {
                foreach ($data['quotes'] as $quote) {
                    $this->insertQuoteSorted($cachedQuotes, $quote);
                }
                $this->saveCachedQuotes($cachedQuotes);
            }

            return $data;
        }

        // Respuesta por defecto en caso de error
        return [
            'quotes' => [],
            'total' => 0,
            'skip' => $skip,
            'limit' => $limit,
        ];
    }

    /**
     * Obtiene una cita aleatoria de la API
     * @return array|null Cita aleatoria o null en caso de error
     */
    public function getRandomQuote(): ?array
    {
        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes/random';
        $response = Http::get($url);

        if ($response->successful()) {
            $quote = $response->json();
            // Actualización del caché
            $cachedQuotes = $this->getCachedQuotes();
            $this->insertQuoteSorted($cachedQuotes, $quote);
            $this->saveCachedQuotes($cachedQuotes);
            return $quote;
        }

        return null;
    }

    /**
     * Obtiene una cita específica por ID
     * @param int $id ID de la cita
     * @return array|null Cita encontrada o null
     */
    public function getQuote(int $id): ?array
    {
        // Primera búsqueda en caché
        $cachedQuotes = $this->getCachedQuotes();
        $quote = $this->binarySearch($cachedQuotes, $id);

        if ($quote) {
            return $quote;
        }

        // Búsqueda en API si no está en caché
        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes/' . $id;
        $response = Http::get($url);

        if ($response->successful()) {
            $quote = $response->json();
            // Actualización del caché
            $this->insertQuoteSorted($cachedQuotes, $quote);
            $this->saveCachedQuotes($cachedQuotes);
            return $quote;
        }

        return null;
    }
}