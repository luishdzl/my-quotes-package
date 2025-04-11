<?php

namespace Vendor\MyQuotesPackage\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class QuoteService
{
    // Array local para cachear las quotes (asegúrate de mantenerlo ordenado por ID)
    protected $cache = [];

    // Variables para el control de tasa
    protected $requestCount = 0;
    protected $windowStart;

    public function __construct()
    {
        $this->windowStart = time();
    }

    // Maneja el rate limiting: Si se excede el número máximo, espera hasta el reinicio de la ventana.
    protected function handleRateLimiting()
    {
        $max = Config::get('quotes.rate_limit', 60);
        $window = Config::get('quotes.time_window', 60);

        if (($this->requestCount >= $max) && ((time() - $this->windowStart) < $window)) {
            $sleepTime = $window - (time() - $this->windowStart);
            sleep($sleepTime);
            // Reinicia la ventana
            $this->windowStart = time();
            $this->requestCount = 0;
        }

        $this->requestCount++;
    }

    public function getAllQuotes()
    {
        $this->handleRateLimiting();
    
        $url = Config::get('quotes.base_url') . '/quotes';
        $response = Http::get($url);
    
        if ($response->successful()) {
            $data = $response->json();
            
            // Añadir verificación de estructura
            if (!isset($data['quotes']) || !is_array($data['quotes'])) {
                return null;
            }
            
            $this->cache = $data['quotes'];
            usort($this->cache, fn($a, $b) => $a['id'] <=> $b['id']);
            return $data;
        }
        return [
            'quotes' => $data['quotes'] ?? [],
            'total' => $data['total'] ?? 0,
            'skip' => $data['skip'] ?? 0,
            'limit' => $data['limit'] ?? 30
        ];
    }

    public function getRandomQuote()
    {
        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes/random';
        $response = Http::get($url);

        if ($response->successful()) {
            $quote = $response->json();
            // Opcional: Agrega la cita al cache manteniendo el orden
            $this->insertQuoteSorted($quote);
            return $quote;
        }
        return null;
    }

    public function getQuote(int $id)
    {
        // Búsqueda en el cache usando búsqueda binaria
        if ($quote = $this->binarySearch($id)) {
            return $quote;
        }
        
        // Si no se encuentra en el cache, realiza la solicitud a la API
        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes/' . $id;
        $response = Http::get($url);

        if ($response->successful()) {
            $quote = $response->json();
            $this->insertQuoteSorted($quote);
            return $quote;
        }
        return null;
    }

    // Búsqueda binaria para encontrar una quote por ID dentro del cache (suponiendo que esté ordenado)
    protected function binarySearch(int $id)
    {
        $low = 0;
        $high = count($this->cache) - 1;

        while ($low <= $high) {
            $mid = intdiv(($low + $high), 2);
            if ($this->cache[$mid]['id'] == $id) {
                return $this->cache[$mid];
            }
            if ($this->cache[$mid]['id'] < $id) {
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }
        return false;
    }

    // Inserta una nueva quote en el array cache manteniendo el orden
    protected function insertQuoteSorted(array $quote)
    {
        $id = $quote['id'];
        // Si el cache está vacío, agrega la quote directamente
        if (empty($this->cache)) {
            $this->cache[] = $quote;
            return;
        }

        // Encontrar el lugar de inserción
        $low = 0;
        $high = count($this->cache);
        while ($low < $high) {
            $mid = intdiv(($low + $high), 2);
            if ($this->cache[$mid]['id'] < $id) {
                $low = $mid + 1;
            } else {
                $high = $mid;
            }
        }

        // Inserta en la posición encontrada
        array_splice($this->cache, $low, 0, [$quote]);
    }
}