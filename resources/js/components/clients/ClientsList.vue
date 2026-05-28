<script setup>
import { ref, onMounted, watch } from 'vue'
import  ClientFormModal from "./ClientFormModal.vue";

// ── Estado ──────────────────────────────────────────────────────────────────
const clients    = ref([])
const loading    = ref(false)
const error      = ref(null)

// Filtros
const search     = ref('')
const status     = ref('')

// Paginación
const currentPage = ref(1)
const lastPage    = ref(1)
const total       = ref(0)

// Modal
const modalOpen    = ref(false)
const editingClient = ref(null)
// Confirm delete
const confirmDeleteId = ref(null)

// ── API ─────────────────────────────────────────────────────────────────────
async function fetchClients() {
    loading.value = true
    error.value   = null
    try {
        const params = new URLSearchParams({ page: currentPage.value })
        if (search.value) params.set('search', search.value)
        if (status.value) params.set('status', status.value)

        const res  = await fetch(`/api/clients?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            credentials: 'same-origin',
        })
        const data = await res.json()
        clients.value     = data.data
        currentPage.value = data.current_page
        lastPage.value    = data.last_page
        total.value       = data.total
    } catch (e) {
        error.value = 'No se pudo cargar la lista de clientes.'
    } finally {
        loading.value = false
    }
}

async function deleteClient(id) {
    if (confirmDeleteId.value !== id) {
        confirmDeleteId.value = id
        setTimeout(() => { confirmDeleteId.value = null }, 3000)
        return
    }
    await fetch(`/api/clients/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        credentials: 'same-origin',
    })
    confirmDeleteId.value = null
    fetchClients()
}

// ── Watchers ─────────────────────────────────────────────────────────────────
let searchTimer = null
watch(search, () => {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => {
        currentPage.value = 1
        fetchClients()
    }, 350)
})

watch(status, () => {
    currentPage.value = 1
    fetchClients()
})

function goToPage(page) {
    currentPage.value = page
    fetchClients()
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function openCreate() {
    editingClient.value = null
    modalOpen.value     = true
}

function openEdit(client) {
    editingClient.value = client
    modalOpen.value     = true
}

function onModalClose() {
    modalOpen.value = false
    fetchClients()
}

function initials(name) {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
}

const AVATAR_COLORS = [
    'bg-blue-100 text-blue-700',
    'bg-violet-100 text-violet-700',
    'bg-amber-100 text-amber-700',
    'bg-rose-100 text-rose-700',
    'bg-teal-100 text-teal-700',
]
function avatarColor(id) {
    return AVATAR_COLORS[id % AVATAR_COLORS.length]
}

const STATUS_MAP = {
    active:   { label: 'Activo',    cls: 'bg-green-50 text-green-700 ring-green-600/20' },
    inactive: { label: 'Inactivo',  cls: 'bg-red-50 text-red-700 ring-red-600/20' },
    prospect: { label: 'Prospecto', cls: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20' },
}

onMounted(() => fetchClients())
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Topbar -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-900">Mini CRM</h1>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">Clientes</span>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-6 py-8">

            <!-- Encabezado de página -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Clientes</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ total }} cliente{{ total !== 1 ? 's' : '' }} registrado{{ total !== 1 ? 's' : '' }}</p>
                </div>
                <button
                    @click="openCreate"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo cliente
                </button>
            </div>

            <!-- Filtros -->
            <div class="flex flex-wrap gap-3 mb-4">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre o email..."
                        class="pl-9 pr-4 h-9 w-72 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>

                <select
                    v-model="status"
                    class="h-9 pl-3 pr-8 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none"
                >
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                    <option value="prospect">Prospecto</option>
                </select>

                <button
                    v-if="search || status"
                    @click="search = ''; status = ''"
                    class="h-9 px-3 text-sm text-gray-500 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 transition-colors"
                >
                    Limpiar
                </button>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

                <!-- Loading -->
                <div v-if="loading" class="py-16 flex items-center justify-center text-gray-400 gap-2">
                    <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span class="text-sm">Cargando clientes...</span>
                </div>

                <!-- Error -->
                <div v-else-if="error" class="py-12 text-center text-sm text-red-500">
                    {{ error }}
                </div>

                <!-- Vacío -->
                <div v-else-if="clients.length === 0" class="py-16 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    <p class="text-gray-500 text-sm font-medium">No hay clientes</p>
                    <p class="text-gray-400 text-xs mt-1">Crea el primero haciendo clic en "Nuevo cliente"</p>
                </div>

                <!-- Datos -->
                <table v-else class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide px-4 py-3">Cliente</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide px-4 py-3">Estado</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide px-4 py-3">Contactos</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide px-4 py-3">Creado por</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide px-4 py-3">Fecha</th>
                        <th class="px-4 py-3 w-28"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="client in clients"
                        :key="client.id"
                        class="hover:bg-gray-50 transition-colors group"
                    >
                        <!-- Cliente -->
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold shrink-0', avatarColor(client.id)]">
                                    {{ initials(client.name) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ client.name }}</p>
                                    <p class="text-xs text-gray-400">{{ client.email }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Estado -->
                        <td class="px-4 py-3">
                                <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset', STATUS_MAP[client.status]?.cls ?? 'bg-gray-100 text-gray-600']">
                                    {{ STATUS_MAP[client.status]?.label ?? client.status }}
                                </span>
                        </td>

                        <!-- Contactos -->
                        <td class="px-4 py-3">
                            <a :href="`/clients/${client.id}`" class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                {{ client.contacts_count ?? 0 }}
                            </a>
                        </td>

                        <!-- Creado por -->
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ client.creator?.name ?? '—' }}
                        </td>

                        <!-- Fecha -->
                        <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                            {{ new Date(client.created_at).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                        </td>

                        <!-- Acciones -->
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a
                                    :href="`/clients/${client.id}`"
                                    class="w-7 h-7 flex items-center justify-center rounded-md text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                    title="Ver detalle"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                                <button
                                    @click="openEdit(client)"
                                    class="w-7 h-7 flex items-center justify-center rounded-md text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                                    title="Editar"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <button
                                    @click="deleteClient(client.id)"
                                    :class="['w-7 h-7 flex items-center justify-center rounded-md transition-colors',
                                                  confirmDeleteId === client.id
                                                    ? 'text-white bg-red-500 hover:bg-red-600'
                                                    : 'text-gray-400 hover:text-red-600 hover:bg-red-50']"
                                    :title="confirmDeleteId === client.id ? 'Clic para confirmar' : 'Eliminar'"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!-- Paginación -->
                <div v-if="lastPage > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-gray-50">
                    <p class="text-xs text-gray-500">
                        Página {{ currentPage }} de {{ lastPage }} · {{ total }} registros
                    </p>
                    <div class="flex items-center gap-1">
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage <= 1"
                            class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs"
                        >←</button>

                        <button
                            v-for="p in lastPage"
                            :key="p"
                            @click="goToPage(p)"
                            :class="['w-7 h-7 flex items-center justify-center rounded-md border text-xs transition-colors',
                                      p === currentPage
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50']"
                        >{{ p }}</button>

                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage >= lastPage"
                            class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs"
                        >→</button>
                    </div>
                </div>
            </div>

        </main>

        <!-- Modal crear / editar -->
        <ClientFormModal
            v-if="modalOpen"
            :client="editingClient"
            @close="onModalClose"
        />
    </div>
</template>
