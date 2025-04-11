<?php

/**
 * Configuración para el paquete de citas (Quotes).
 * Permite personalizar el comportamiento mediante variables de entorno.
 */
return [
    /**
     * URL base para las solicitudes a la API de citas.
     * 
     * Valor predeterminado: 'https://dummyjson.com'.
     */
    'base_url' => env('QUOTES_API_BASE_URL', 'https://dummyjson.com'),

    /**
     * Límite de solicitudes permitidas por unidad de tiempo.
     * 
     * Valor predeterminado: 60 solicitudes por ventana de tiempo.
     */
    'rate_limit' => env('QUOTES_API_RATE_LIMIT', 60),

    /**
     * Duración de la ventana de tiempo en segundos para el control de tasa.
     * 
     * Valor predeterminado: 60 segundos.
     */
    'time_window' => env('QUOTES_API_TIME_WINDOW', 60),
];
