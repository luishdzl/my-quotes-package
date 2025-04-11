<?php

namespace Vendor\MyQuotesPackage\Controllers;

use Illuminate\Routing\Controller;
use Vendor\MyQuotesPackage\Services\QuoteService;

/**
 * Controlador para manejar las operaciones relacionadas con citas (quotes).
 */
class QuotesController extends Controller
{
    /**
     * Servicio para gestionar las citas.
     *
     * @var QuoteService
     */
    protected $quoteService;

    /**
     * Constructor de la clase.
     * 
     * @param QuoteService $quoteService Instancia del servicio para manejar citas.
     */
    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    /**
     * Obtiene y devuelve todas las citas almacenadas.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con la lista de citas.
     */
    public function index()
    {
        // Llama al servicio para obtener todas las citas.
        $response = $this->quoteService->getAllQuotes();
        
        // Devuelve las citas en un formato estándar con paginación.
        return response()->json([
            'quotes' => $response['quotes'] ?? [], // Lista de citas (vacía si no hay citas).
            'total' => $response['total'] ?? 0,    // Total de citas disponibles.
            'skip' => $response['skip'] ?? 0,      // Número de registros omitidos (paginación).
            'limit' => $response['limit'] ?? 30    // Límite de registros por página.
        ]);
    }

    /**
     * Obtiene y devuelve una cita aleatoria.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con una cita aleatoria.
     */
    public function random()
    {
        // Llama al servicio para obtener una cita aleatoria.
        $quote = $this->quoteService->getRandomQuote();

        // Devuelve la cita en formato JSON.
        return response()->json($quote);
    }

    /**
     * Obtiene y devuelve una cita específica según su ID.
     *
     * @param int $id Identificador único de la cita.
     * @return \Illuminate\Http\JsonResponse Respuesta en formato JSON con la cita solicitada.
     */
    public function show($id)
    {
        // Llama al servicio para obtener la cita con el ID especificado.
        $quote = $this->quoteService->getQuote((int)$id);

        // Devuelve la cita en formato JSON.
        return response()->json($quote);
    }
}
