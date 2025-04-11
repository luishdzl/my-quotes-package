<?php

namespace Vendor\MyQuotesPackage\Controllers;

use Illuminate\Routing\Controller;
use Vendor\MyQuotesPackage\Services\QuoteService;

class QuotesController extends Controller
{
    protected $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    // Devuelve todas las quotes
    public function index()
    {
        $response = $this->quoteService->getAllQuotes();
        
        // Asegurar estructura consistente
        return response()->json([
            'quotes' => $response['quotes'] ?? [],
            'total' => $response['total'] ?? 0,
            'skip' => $response['skip'] ?? 0,
            'limit' => $response['limit'] ?? 30
        ]);
    }

    // Devuelve una quote aleatoria
    public function random()
    {
        $quote = $this->quoteService->getRandomQuote();
        return response()->json($quote);
    }

    // Devuelve una quote por ID
    public function show($id)
    {
        $quote = $this->quoteService->getQuote((int)$id);
        return response()->json($quote);
    }
}
