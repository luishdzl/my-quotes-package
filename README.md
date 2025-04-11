# My Quotes Package

Paquete para obtener citas con **rate limiting**, **cacheo** y una **interfaz de usuario (UI)** desarrollada en **Vue.js**.

---

## Descripción

Este paquete implementa:

- **API RESTful:**  
  Proporciona endpoints para obtener todas las citas, una cita aleatoria y citas por ID.
- **Manejo de Rate Limiting y Caché:**  
  Controla la cantidad de peticiones a la API y cachea localmente las citas para optimizar el rendimiento.
- **UI en Vue.js:**  
  Una interfaz de usuario moderna para visualizar las citas. Incluye pasos de compilación y publicación.

---

## Requisitos Previos

- **PHP** versión 8.2 o superior.
- **Composer** para la instalación del paquete.
- **Node.js** y **npm** para compilar la UI en Vue.js.
- **Framework Laravel** para integrar el paquete.

---


# 1. Agregar el repositorio en composer.json
Antes de instalar el paquete, debes indicarle a Composer dónde encontrar el repositorio. Para ello, abre el archivo composer.json en tu proyecto Laravel y agrega la siguiente sección (o modifica la existente):

json

"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/luishdzl/my-quotes-package"
    }
]
Este bloque le dice a Composer que el paquete se encuentra en ese repositorio de Git. Una vez añadido, guarda el archivo y desde la terminal ejecuta:

en la terminal:

```bash
composer require vendor/my-quotes-package
```

Nota: Reemplaza vendor/my-quotes-package por el namespace y nombre real de tu paquete si difiere.

# 2. Instalación y Compilación de Assets de la UI (Vue.js)
Dentro de tu paquete se incluye una interfaz de usuario desarrollada con Vue.js. Sigue estos pasos para compilarla:

Acceder a la carpeta del paquete
La carpeta donde están los archivos de la UI se ubica en el directorio del paquete, por ejemplo:
vendor/vendor/my-quotes-package

Instalar dependencias de Node.js:
Desde esa carpeta, ejecuta:

en la terminal:
```bash
npm install
```
o
```bash
npm i
```
Esto instalará todas las dependencias definidas en el package.json.

Compilar (build) la UI:
Luego, ejecuta:

en la terminal:
```bash
npm run build
```

Este comando genera los archivos finales (JS/CSS) que se publicarán en el proyecto Laravel.

# 3. Publicación de Assets y Configuración
Una vez realizados el build de los assets, debes publicarlos para que Laravel pueda servirlos. Desde la raíz de tu proyecto Laravel, ejecuta los siguientes comandos:

Publicar los assets de la UI:

en la terminal:

Publicar el archivo del assets:
```bash
php artisan vendor:publish --tag=public
```

en la terminal:

Publicar el archivo de configuración:
```bash
php artisan vendor:publish --tag=config
```

Estos comandos copiarán los archivos necesarios (assets y configuración) a la estructura de tu proyecto, permitiéndote personalizarlos si es necesario.

# 4. Estructura del Paquete y Código de Ejemplo
A continuación se presenta un resumen de la estructura y del código que tienes en el paquete, el cual incluye:

Controladores (QuotesController):
Con métodos para obtener todas las citas, una cita aleatoria y una cita por ID, garantizando respuestas en formato JSON con una estructura consistente.

ServiceProvider (QuoteServiceProvider):
Encargado de fusionar la configuración del paquete con la del usuario, registrar el servicio y publicar tanto el archivo de configuración como los assets de la UI. Además, carga rutas y vistas.

Rutas:
Se definen rutas en dos archivos:

API: rutas prefijadas con /api para obtener las citas.

Web: ruta para acceder a la interfaz de usuario con la vista quotes-ui.

Servicio (QuoteService):
Maneja la lógica para consumir la API de citas, controlando el rate limiting, caché (cache), y empleando técnicas como búsqueda binaria para optimizar la búsqueda de citas por ID.

Asegúrate de que todo el código esté correctamente ubicado en los directorios establecidos (por ejemplo, en src/, config/, public/, resources/views/), para que el ServiceProvider pueda registrarlo y cargarlo sin inconvenientes


Ejemplo de Uso en Código
La siguiente es una breve demostración de cómo se puede invocar el servicio en un controlador de Laravel:

```bash
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
        return response()->json([
            'quotes' => $response['quotes'] ?? [],
            'total'  => $response['total'] ?? 0,
            'skip'   => $response['skip'] ?? 0,
            'limit'  => $response['limit'] ?? 30
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
```

## Configuración Adicional
El paquete permite modificar la configuración copiando el archivo config/quotes.php en tu directorio de configuración. Esto se realiza mediante el comando de publicación mencionado anteriormente. En este archivo podrás ajustar parámetros importantes como:

base_url: La URL base de la API de citas.

rate_limit: Número máximo de peticiones permitidas por ventana de tiempo.

time_window: Duración de la ventana de tiempo para el rate limiting.

Notas Finales
Sigue cada paso de instalación y compilación para asegurar el correcto funcionamiento.

Verifica la publicación de assets para confirmar que la UI se encuentra accesible.

Puedes extender o modificar la configuración según las necesidades de tu aplicación.