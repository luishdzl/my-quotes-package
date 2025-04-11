<?php

namespace Vendor\MyQuotesPackage\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

/**
 * Clase que gestiona las operaciones relacionadas con las citas (quotes).
 * Incluye funcionalidad para obtener todas las citas, una cita aleatoria o una cita específica por ID,
 * manejando cache local y control de tasa de solicitudes.
 */
class QuoteService
{
    /**
     * Cache local para almacenar las citas. 
     * Debe mantenerse ordenado por el ID de las citas para facilitar búsquedas eficientes.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Contador para rastrear el número de solicitudes realizadas en la ventana de tiempo actual.
     *
     * @var int
     */
    protected $requestCount = 0;

    /**
     * Marca de tiempo para el inicio de la ventana de tiempo actual.
     *
     * @var int
     */
    protected $windowStart;

    /**
     * Constructor de la clase. Inicializa el inicio de la ventana de tiempo.
     */
    public function __construct()
    {
        $this->windowStart = time();
    }

    /**
     * Maneja el control de tasa de solicitudes según los límites configurados.
     * Si se excede el límite, espera hasta que se reinicie la ventana de tiempo.
     */
    protected function handleRateLimiting()
    {
        $max = Config::get('quotes.rate_limit', 60); // Límite máximo de solicitudes por ventana.
        $window = Config::get('quotes.time_window', 60); // Duración de la ventana en segundos.

        if (($this->requestCount >= $max) && ((time() - $this->windowStart) < $window)) {
            $sleepTime = $window - (time() - $this->windowStart);
            sleep($sleepTime); // Pausa el proceso hasta que se reinicie la ventana.
            $this->windowStart = time(); // Reinicia la ventana.
            $this->requestCount = 0; // Reinicia el contador de solicitudes.
        }

        $this->requestCount++;
    }

    /**
     * Obtiene todas las citas desde la API y actualiza el cache local.
     *
     * @return array|null Datos de las citas o `null` si la solicitud falla.
     */
    public function getAllQuotes()
    {
        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes';
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (!isset($data['quotes']) || !is_array($data['quotes'])) {
                return null; // Validación de estructura esperada.
            }

            $this->cache = $data['quotes'];
            usort($this->cache, fn($a, $b) => $a['id'] <=> $b['id']); // Ordena por ID.
            return $data;
        }

        // Estructura de respuesta predeterminada si la solicitud falla.
        return [
            'quotes' => [],
            'total' => 0,
            'skip' => 0,
            'limit' => 30
        ];
    }

    /**
     * Obtiene una cita aleatoria desde la API y la almacena en el cache local.
     *
     * @return array|null Datos de la cita aleatoria o `null` si la solicitud falla.
     */
    public function getRandomQuote()
    {
        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes/random';
        $response = Http::get($url);

        if ($response->successful()) {
            $quote = $response->json();
            $this->insertQuoteSorted($quote); // Agrega la cita al cache manteniendo el orden.
            return $quote;
        }

        return null;
    }

    /**
     * Obtiene una cita específica por ID, buscando primero en el cache.
     *
     * @param int $id ID de la cita a buscar.
     * @return array|null Datos de la cita o `null` si no se encuentra.
     */
    public function getQuote(int $id)
    {
        if ($quote = $this->binarySearch($id)) {
            return $quote; // Devuelve desde el cache si está disponible.
        }

        $this->handleRateLimiting();

        $url = Config::get('quotes.base_url') . '/quotes/' . $id;
        $response = Http::get($url);

        if ($response->successful()) {
            $quote = $response->json();
            $this->insertQuoteSorted($quote); // Agrega la cita al cache.
            return $quote;
        }

        return null;
    }

    /**
     * Realiza una búsqueda binaria en el cache para encontrar una cita por ID.
     *
     * @param int $id ID de la cita a buscar.
     * @return array|false Datos de la cita si se encuentra, o `false` si no.
     */
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

    /**
     * Inserta una nueva cita en el cache manteniendo el orden por ID.
     *
     * @param array $quote Datos de la cita a insertar.
     */
    protected function insertQuoteSorted(array $quote)
    {
        $id = $quote['id'];

        if (empty($this->cache)) {
            $this->cache[] = $quote; // Si el cache está vacío, inserta directamente.
            return;
        }

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

        array_splice($this->cache, $low, 0, [$quote]); // Inserta en la posición correcta.
    }
}
