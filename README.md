## Stack Tecnológico
- **Backend:** Laravel Package, PestPHP (Tests).
- **Frontend:** Vue 3 (Composition API), TypeScript.
- **Infraestructura:** Docker (Laravel host + Package link). 

## Arquitectura inicial
```
quote/
├── composer.json                     
├── README.md                         
├── docker-compose.yml                # Levanta Laravel fresco + este paquete en :8080
├── docker/
│   └── Dockerfile                    # Imagen PHP 8.2+ con extensiones necesarias
├── config/
│   └── quotes.php                    # Límites de tasa, TTL de caché, paginación
├── src/
│   ├── QuotesServiceProvider.php     # Registra servicios, rutas, comandos y publica assets
│   ├── QuotesManager.php             # Búsqueda binaria manual O(log n) + caché ordenado
│   ├── Facades/
│   │   └── Quotes.php                # Acceso estático al Manager vía contenedor
│   ├── Services/
│   │   ├── QuoteApiClient.php        # Cliente HTTP a DummyJSON (Http::get nativo)
│   │   └── RateLimiterService.php    # Rate limiter no bloqueante con excepción personalizada
│   ├── Exceptions/
│   │   └── RateLimitExceededException.php  # 429 cuando se excede el límite
│   ├── Commands/
│   │   └── BatchImportCommand.php    # CLI resiliente: espera y reintenta al llegar al límite
│   └── Http/
│       └── Controllers/
│           └── QuoteController.php   # Endpoints GET /api/quotes y /api/quotes/{id}
├── routes/
│   └── api.php                       # Carga de rutas desde el Service Provider
├── resources/
│   └── js/
│       ├── app.ts                    # Entry point de Vue 3
│       ├── Components/
│       │   └── QuoteSearch.vue       # Paginación + búsqueda por ID con Composition API
│       └── shims-vue.d.ts            # Tipos TypeScript para archivos .vue
├── vite.config.ts                    # Compila assets del paquete a public/vendor/quotes
├── package.json                      # Dependencias: vue, vite, typescript
├── tsconfig.json                     # Config TypeScript para Vue 3
├── phpunit.xml                       # Configuración de PestPHP
└── tests/
    ├── Pest.php                       # Helpers y traits de Pest
    ├── Unit/
    │   └── QuotesManagerTest.php      # Prueba aislada de binarySearch() con arrays ordenados
    └── Feature/
        ├── QuotesApiTest.php          # Prueba endpoints con Http::fake()
        └── BatchImportTest.php        # Prueba comando simulando rate limit con mock
```

