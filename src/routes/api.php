<?php

use Illuminate\Support\Facades\Route;
use Vendor\MyQuotesPackage\Controllers\QuotesController;

/**
 * Define un grupo de rutas prefijadas con `api` para manejar solicitudes relacionadas con citas.
 */
Route::prefix('api')->group(function () {
    /**
     * Ruta para obtener todas las citas.
     * GET /api/quotes
     */
    Route::get('quotes', [QuotesController::class, 'index']);

    /**
     * Ruta para obtener una cita aleatoria.
     * GET /api/quotes/random
     */
    Route::get('quotes/random', [QuotesController::class, 'random']);

    /**
     * Ruta para obtener una cita específica por ID.
     * GET /api/quotes/{id}
     */
    Route::get('quotes/{id}', [QuotesController::class, 'show']);
});
