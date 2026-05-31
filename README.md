# Mini CRM — Prueba Técnica

Aplicación web de gestión de clientes y contactos construida con **Laravel 11** en el backend y **Vue 3** en el frontend, integrados en un mismo proyecto mediante Vite.

---

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 11, Sanctum, Form Requests, API Resources, Service Layer |
| Frontend | Vue 3 (Composition API), Pinia, Vite |
| Base de datos | MySQL (local) / PostgreSQL (producción en Render) |
| Estilos | Tailwind CSS |

---

## Arquitectura

### Backend

#### Separación de rutas

Las rutas están divididas en dos grupos con responsabilidades claras:

- `routes/web.php` — rutas de Blade que sirven las vistas HTML donde se montan los componentes Vue.
- `routes/api.php` — rutas REST consumidas por Vue a través de `fetch`, protegidas con Sanctum.

```
web.php   →  /clients         →  ClientController@index  (retorna vista Blade)
api.php   →  /api/clients     →  ClientController@index  (retorna JSON)
```

#### Capa de servicios e inyección de dependencia

La lógica de negocio está desacoplada del controlador mediante una capa de servicios. Cada dominio define una interfaz que el controlador recibe por inyección de dependencia, y el `AppServiceProvider` se encarga de vincular la implementación concreta:

```php
// App/Contracts/ClientServiceInterface.php
interface ClientServiceInterface
{
    public function paginate(array $filters): LengthAwarePaginator;
    public function create(array $data): Client;
    public function update(Client $client, array $data): Client;
    public function delete(Client $client): void;
}
```

```php
// App/Services/ClientService.php
class ClientService implements ClientServiceInterface
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Client::with(['creator', 'contacts'])
            ->when($filters['search'] ?? null, fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->paginate(15);
    }
    // ...
}
```

```php
// App/Providers/AppServiceProvider.php
$this->app->bind(ClientServiceInterface::class, ClientService::class);
```

```php
// App/Http/Controllers/Api/ClientController.php
public function __construct(private ClientServiceInterface $service) {}

public function index(Request $request): AnonymousResourceCollection
{
    return ClientResource::collection(
        $this->service->paginate($request->only(['search', 'status']))
    );
}
```

El controlador queda reducido a orquestar la petición HTTP: delega la lógica al servicio, transforma la respuesta con el Resource y retorna. Esto también facilita testear el servicio de forma aislada sin depender del contexto HTTP.

#### Validación con Form Requests

Toda la validación de entrada está encapsulada en clases `FormRequest`, manteniendo los controladores limpios:

```php
// App/Http/Requests/StoreClientRequest.php
'nit'   => ['nullable', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d$/', Rule::unique('clients')->ignore($this->route('client'))],
'phone' => ['nullable', 'regex:/^\+?[\d\s\-]{7,15}$/'],
```

#### API Resources

Las respuestas JSON tienen una estructura consistente mediante Resources, evitando exponer columnas internas y garantizando el mismo contrato en todos los endpoints:

```php
// App/Http/Resources/ClientResource.php
public function toArray($request): array
{
    return [
        'id'         => $this->id,
        'name'       => $this->name,
        'nit'        => $this->nit,
        'phone'      => $this->phone,
        'status'     => $this->status,
        'creator'    => new UserResource($this->whenLoaded('creator')),
        'contacts'   => ContactResource::collection($this->whenLoaded('contacts')),
        'created_at' => $this->created_at->format('Y-m-d'),
    ];
}
```

#### Relaciones y Eager Loading

Las relaciones entre modelos están definidas explícitamente y se cargan con eager loading para evitar el problema N+1:

```php
// En el controlador
Client::with(['creator', 'contacts'])->paginate(15);
```

---

### Frontend

#### Single Entry Point

Toda la aplicación Vue se inicializa desde un único archivo `app.js`. El punto de entrada:

1. Obtiene el CSRF cookie de Sanctum.
2. Verifica si el usuario está autenticado.
3. Redirige a `/login` si no hay sesión activa.
4. Monta cada componente Vue en el selector del DOM que le corresponde.

```js
const mounts = [
    { selector: '#clients-app', component: ClientsList },
    { selector: '#contacts-app', component: ContactsView },
]

mounts.forEach(({ selector, component }) => {
    const el = document.querySelector(selector)
    if (el) createApp(component).use(pinia).mount(el)
})
```

Esto permite tener múltiples "islas" Vue dentro de un proyecto Laravel tradicional sin necesidad de un SPA completo.

#### Estructura de carpetas

```
resources/js/
├── app.js                  # Entry point único
├── lib/
│   └── api.js              # Wrapper de fetch con CSRF y manejo de errores
├── stores/                 # Estado global con Pinia
│   ├── auth.js
│   ├── client.js
│   └── contact.js
├── composables/            # Lógica reutilizable por vista
│   ├── useClients.js
│   └── useContacts.js
├── pages/                  # Componentes raíz montados por app.js
│   ├── ClientsList.vue
│   └── ContactsView.vue
├── components/             # Componentes específicos por dominio
│   └── clients/
│       └── DetailsClientModal.vue
└── shared/
    └── components/         # Componentes genéricos reutilizables
        ├── DataTable.vue
        ├── FormModal.vue
        ├── ConfirmDelete.vue
        └── Layout.vue
```

#### Componentes reutilizables

- `DataTable.vue` — tabla genérica con búsqueda, filtro, paginación y slots para filas y acciones.
- `FormModal.vue` — modal de formulario dinámico que construye los campos desde un array de configuración.
- `ConfirmDelete.vue` — modal de confirmación de eliminación.
- `Layout.vue` — layout compartido con navbar y contenedor principal.

#### Gestión de estado

Cada dominio tiene su propio store en Pinia y su composable:

- El **store** maneja las llamadas a la API y el estado reactivo.
- El **composable** expone la lógica de la vista (filtros con debounce, paginación, flujo de eliminación con modal).

---

## Funcionalidades

- Autenticación con Laravel Sanctum (sesión + CSRF).
- CRUD completo de clientes con filtro por estado y búsqueda.
- CRUD completo de contactos por cliente con filtro por tipo (principal / general).
- Vista de detalle de cliente con sus contactos.
- Eliminación con modal de confirmación.
- Validación en backend con mensajes de error mostrados inline en el formulario.
- Paginación del lado del servidor.

---

## Instalación local

```bash
git clone https://github.com/tu-usuario/tu-repo.git
cd tu-repo

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configura tu base de datos en .env
php artisan migrate --seed

npm run dev
php artisan serve
```

---

## Despliegue en Render

La aplicación está desplegada en [Render](https://render.com) usando PostgreSQL como base de datos en producción.

**Variables de entorno requeridas:**

```
APP_KEY=
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-app.onrender.com

DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_DRIVER=database
```

**Build command:**
```bash
composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan migrate --force && php artisan config:cache && php artisan route:cache
```

**Start command:**
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```
