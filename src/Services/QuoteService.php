<?php

namespace Vendor\MyQuotesPackage\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Vendor\MyQuotesPackage\Traits\HandlesCache;

class QuoteService
{
    use HandlesCache;

    /**
     * Clave para almacenar el caché de citas.
     */
    const CACHE_KEY = 'quotes_cache';

    /**
     * Obtiene citas desde la caché persistente.
     */
    protected function getCachedQuotes(): array
    {
        return Cache::get(self::CACHE_KEY, []);
    }

    /**
     * Guarda citas en la caché persistente.
     */
    protected function saveCachedQuotes(array $quotes): void
    {
        Cache::put(self::CACHE_KEY, $quotes, now()->addHours(1));
    }

    /**
     * Maneja el rate limiting sin usar sleep().
     */
    protected function handleRateLimiting(): void
    {
        $max = Config::get('quotes.rate_limit', 60);
        $window = Config::get('quotes.time_window', 60);
        $cacheKey = 'quote_service_rate_limit';

        $currentState = Cache::get($cacheKey, ['count' => 0, 'window_start' => time()]);
        $currentTime = time();
        $elapsed = $currentTime - $currentState['window_start'];

        // Reinicia la ventana si ha expirado
        if ($elapsed > $window) {
            $currentState = ['count' => 0, 'window_start' => $currentTime];
        }

        // Verifica si se excedió el límite
        if ($currentState['count'] >= $max) {
            abort(429, "Demasiadas solicitudes. Espere " . ($window - $elapsed) . " segundos.");
        }

        $currentState['count']++;
        Cache::put($cacheKey, $currentState, $window);
    }

    public function getAllQuotes(int $skip = 0, int $limit = 30): array
    {
        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . "/quotes?skip={$skip}&limit={$limit}";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            $cachedQuotes = $this->getCachedQuotes();

            if (isset($data['quotes']) && is_array($data['quotes'])) {
                foreach ($data['quotes'] as $quote) {
                    $this->insertQuoteSorted($cachedQuotes, $quote);
                }
                $this->saveCachedQuotes($cachedQuotes);
            }

            return $data;
        }

        return [
            'quotes' => [],
            'total' => 0,
            'skip' => $skip,
            'limit' => $limit,
        ];
    }

    public function getRandomQuote(): ?array
    {
        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes/random';
        $response = Http::get($url);

        if ($response->successful()) {
            $quote = $response->json();
            $cachedQuotes = $this->getCachedQuotes();
            $this->insertQuoteSorted($cachedQuotes, $quote);
            $this->saveCachedQuotes($cachedQuotes);
            return $quote;
        }

        return null;
    }

    public function getQuote(int $id): ?array
    {
        $cachedQuotes = $this->getCachedQuotes();
        $quote = $this->binarySearch($cachedQuotes, $id);

        if ($quote) {
            return $quote;
        }

        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes/' . $id;
        $response = Http::get($url);

        if ($response->successful()) {
            $quote = $response->json();
            $this->insertQuoteSorted($cachedQuotes, $quote);
            $this->saveCachedQuotes($cachedQuotes);
            return $quote;
        }

        return null;
    }
}