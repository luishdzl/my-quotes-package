<?php

use Illuminate\Support\Facades\Route;
use Vendor\MyQuotesPackage\Controllers\QuotesController;

Route::prefix('api')->group(function () {
    Route::get('quotes', [QuotesController::class, 'index']);
    Route::get('quotes/random', [QuotesController::class, 'random']);
    Route::get('quotes/{id}', [QuotesController::class, 'show']);
});
