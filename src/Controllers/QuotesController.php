<?php

namespace Vendor\MyQuotesPackage\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\MyQuotesPackage\Services\QuoteService;

class QuotesController extends Controller
{
    protected $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    public function index(Request $request)
    {
        $skip = $request->input('skip', 0);
        $limit = $request->input('limit', 30);

        $response = $this->quoteService->getAllQuotes($skip, $limit);

        return response()->json([
            'quotes' => $response['quotes'] ?? [],
            'total' => $response['total'] ?? 0,
            'skip' => (int)$skip,
            'limit' => (int)$limit,
        ]);
    }

    public function random()
    {
        $quote = $this->quoteService->getRandomQuote();
        return response()->json($quote);
    }

    public function show($id)
    {
        $quote = $this->quoteService->getQuote((int)$id);
        return response()->json($quote);
    }
}