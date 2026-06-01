# Mini CRM — Prueba Técnica

Aplicación web de gestión de clientes y contactos construida con **Laravel 11** en el backend y **Vue 3** en el frontend, integrados en un mismo proyecto mediante Vite.

---

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 11, Sanctum, Form Requests, API Resources, Service Layer |
| Frontend | Vue 3 (Composition API), Pinia, Vite |
| Base de datos | MySQL 8 (Docker) |
| Estilos | Tailwind CSS |

---

## Requisitos

- Docker
- Docker Compose

---

## Instalación y ejecución

**1. Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/tu-repo.git
cd tu-repo
```

**2. Levantar la aplicación**
```bash
docker compose up --build
```

Al iniciar, el contenedor ejecuta automáticamente las migraciones y carga los datos de prueba. Espera a ver en los logs:s


**3. Abrir en el navegador**

http://localhost:10000

## Credenciales de prueba

| Campo | Valor |
|---|---|
| Email | demo@crm.test |
| Password | password |

---

## Arquitectura

### Backend

#### Separación de rutas

Las rutas están divididas en dos grupos con responsabilidades claras:

- `routes/web.php` — rutas Blade que sirven las vistas HTML donde se montan los componentes Vue.
- `routes/api.php` — rutas REST consumidas por Vue a través de `fetch`, protegidas con Sanctum.

web.php   →  /clients     →  ClientController@index  (retorna vista Blade)
api.php   →  /api/clients →  ClientController@list   (retorna JSON)


#### Capa de servicios e inyección de dependencia

La lógica de negocio está desacoplada del controlador mediante una capa de servicios. Cada dominio define una interfaz que el controlador recibe por inyección de dependencia, y el `AppServiceProvider` vincula la implementación concreta:

```php
// AppServiceProvider.php
$this->app->bind(IClientService::class, ClientsServiceImpl::class);
$this->app->bind(IContactService::class, ContactServiceImpl::class);
```

El controlador queda reducido a orquestar la petición HTTP: valida, delega al servicio y transforma la respuesta con un Resource.

```php
public function __construct(private IClientService $clientService) {}

public function list(Request $request)
{
    $validated = $request->validate([...]);
    $response  = $this->clientService->all($validated);
    return new ClientCollectionResource($response);
}
```

#### Validación con Form Requests

Toda la validación está encapsulada en clases `FormRequest`, manteniendo los controladores limpios:

```php
// CreateClientRequest.php
'name'  => ['required', 'string', 'max:255'],
'nit'   => ['nullable', 'string'],
'phone' => ['nullable', 'string'],
```

#### API Resources

Las respuestas JSON tienen estructura consistente mediante Resources y Collections:

- `ClientResource` — transforma un modelo Client a JSON.
- `ClientCollectionResource` — extiende `ResourceCollection` e incluye metadata de paginación (`current_page`, `last_page`, `total`).
- `ContactResource` — transforma un modelo Contact a JSON.
- `ClientDetailsResource` — vista detallada del cliente con sus contactos.

#### Eager Loading

Las relaciones se cargan con eager loading para evitar el problema N+1:

```php
Client::query()
    ->with('creator')
    ->withCount('contacts')
    ->paginate(15);
```

---

### Frontend

#### Single Entry Point

Toda la aplicación Vue se inicializa desde un único `app.js`:

1. Obtiene el CSRF cookie de Sanctum.
2. Verifica si el usuario está autenticado.
3. Redirige a `/login` si no hay sesión activa.
4. Monta cada componente Vue en su selector del DOM.

```js
const mounts = [
    { selector: '#clients-app',  component: ClientsList },
    { selector: '#contacts-app', component: ContactsView },
]

mounts.forEach(({ selector, component }) => {
    const el = document.querySelector(selector)
    if (el) createApp(component).use(pinia).mount(el)
})
```

Esto permite tener múltiples "islas" Vue dentro de un proyecto Laravel tradicional sin necesidad de un SPA completo.

#### Estructura de carpetas


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


#### Componentes reutilizables

- `DataTable.vue` — tabla genérica con búsqueda con debounce, filtro, paginación server-side y slots para filas y acciones.
- `FormModal.vue` — modal de formulario dinámico que construye los campos desde un array de configuración.
- `ConfirmDelete.vue` — modal de confirmación antes de eliminar un registro.
- `Layout.vue` — layout compartido con navbar y contenedor principal.

#### Gestión de estado

Cada dominio tiene su propio store en Pinia y su composable:

- El **store** maneja las llamadas a la API y el estado reactivo (lista, paginación, loading, errores).
- El **composable** expone la lógica de la vista: filtros con debounce, paginación, y flujo de eliminación con modal de confirmación.

---

## Funcionalidades

- Autenticación con Laravel Sanctum (sesión + CSRF).
- CRUD completo de clientes con filtro por estado y búsqueda.
- CRUD completo de contactos por cliente con filtro por tipo (principal / general).
- Vista de detalle de cliente con sus contactos.
- Eliminación con modal de confirmación.
- Validación en backend con mensajes de error mostrados inline en el formulario.
- Paginación server-side.
