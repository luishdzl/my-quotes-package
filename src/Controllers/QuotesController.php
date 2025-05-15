<?php

namespace Vendor\MyQuotesPackage\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\MyQuotesPackage\Services\QuoteService;

/**
 * Controlador para manejar las operaciones relacionadas con citas.
 * 
 * Responsabilidades principales:
 * - Gestionar las solicitudes HTTP
 * - Coordinar con el servicio de citas (QuoteService)
 * - Formatear respuestas JSON
 */
class QuotesController extends Controller
{
    /**
     * Instancia del servicio de citas
     * @var QuoteService
     */
    protected $quoteService;

    /**
     * Constructor: Inyección de dependencias
     * @param QuoteService $quoteService - Servicio inyectado automáticamente
     */
    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    /**
     * Obtiene listado paginado de citas
     * @param Request $request - Objeto de solicitud HTTP
     * @return \Illuminate\Http\JsonResponse
     * 
     * Parámetros:
     * - skip: Número de registros a omitir (paginación)
     * - limit: Máximo de registros a devolver (por defecto 30)
     */
    public function index(Request $request)
    {
        // Obtiene parámetros de paginación
        $skip = $request->input('skip', 0);
        $limit = $request->input('limit', 30);

        // Obtiene datos del servicio
        $response = $this->quoteService->getAllQuotes($skip, $limit);

        // Construye respuesta JSON estructurada
        return response()->json([
            'quotes' => $response['quotes'] ?? [], // Listado de citas
            'total' => $response['total'] ?? 0,    // Total disponible
            'skip' => (int)$skip,                   // Offset aplicado
            'limit' => (int)$limit,                 // Límite utilizado
        ]);
    }

    /**
     * Obtiene una cita aleatoria
     * @return \Illuminate\Http\JsonResponse
     */
    public function random()
    {
        // Obtiene cita aleatoria del servicio
        $quote = $this->quoteService->getRandomQuote();
        
        // Devuelve respuesta JSON con la cita
        return response()->json($quote);
    }

    /**
     * Obtiene una cita específica por ID
     * @param mixed $id - Identificador de la cita
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Obtiene cita del servicio (con conversión a entero)
        $quote = $this->quoteService->getQuote((int)$id);

        if (!$quote) {
            return response()->json([
                'error' => 'Quote not found'
            ], 404);
        }
        
        // Devuelve respuesta JSON con la cita
        return response()->json($quote);
    }
}