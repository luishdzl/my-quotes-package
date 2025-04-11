<?php
use Illuminate\Support\Facades\Route;

/**
 * Define una ruta para mostrar la interfaz de usuario de las citas.
 */
Route::get('quotes-ui', function () {
    // Renderiza la vista ubicada en el espacio de nombres 'quotes::quotes-ui'.
    return view('quotes::quotes-ui');
});
