<script setup>
import { onMounted, ref } from 'vue'
import { useClients } from '@/composables/useClients'
import {useClientStore} from "../stores/client.js";
import DataTable from "../shared/components/DataTable.vue"
import FormModal from '../shared/components/FormModal.vue'
import DetailsClientModal from '../components/clients/DetailsClientModal.vue'
import Layout from "../shared/components/Layout.vue"
import ConfirmDelete from "../shared/components/ConfirmDelete.vue";

const {
    clients, total, currentPage, lastPage, loading, error,
     confirmDelete, cancelDelete,
    search, status, clearFilters,
    clientDetail, loadClientDetail,
    confirmDeleteId, requestDelete,
    goToPage, refresh,
} = useClients()

const store = useClientStore()

// ── Campos del formulario ─────────────────────────────────────────────────────
const clientFields = [
    { key: 'name',     label: 'Nombre',    required: true,  placeholder: 'Acme Corp' },
    { key: 'nit',      label: 'Nit',                        placeholder: '900.000.000-0, 120.125.123-8,109391367-6' },
    { key: 'phone',    label: 'Teléfono',                   placeholder: '+57 300 000 0000' },
    { key: 'url_page', label: 'Sitio web',                  placeholder: 'https://empresa.com' },
    { key: 'status',   label: 'Estado',    required: true,  type: 'select', default: 'prospect',
        options: [
            { value: 'prospect', label: 'Prospecto' },
            { value: 'active',   label: 'Activo' },
            { value: 'inactive', label: 'Inactivo' },
        ]
    },
]

// ── Modal ─────────────────────────────────────────────────────────────────────
const modalOpen     = ref(false)
const editingClient = ref(null)
const saving        = ref(false)
const formErrors    = ref({})

function openCreate() { editingClient.value = null; modalOpen.value = true }
function openEdit(c)  { editingClient.value = c;    modalOpen.value = true }
function onModalClose() {
    modalOpen.value = false
    formErrors.value = {}
    refresh()
}

async function handleSubmit(data) {
    saving.value = true
    formErrors.value = {}
    try {
        const payload = editingClient.value ? { ...data, id: editingClient.value.id } : data
        await store.save(payload)
        onModalClose()
    } catch (e) {
        formErrors.value = e?.errors ?? { _general: 'Error al guardar.' }
    } finally {
        saving.value = false
    }
}

// ── Detalle ───────────────────────────────────────────────────────────────────
const detailsOpen = ref(false)
async function openDetails(client) {
    await loadClientDetail(client.id)
    detailsOpen.value = true
}
function closeDetails() { detailsOpen.value = false }

onMounted(refresh)
</script>

<template>
    <Layout>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Clientes</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ total }} cliente{{ total !== 1 ? 's' : '' }} registrado{{ total !== 1 ? 's' : '' }}
                </p>
            </div>
            <button @click="openCreate" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo cliente
            </button>
        </div>

        <DataTable
            :items="clients"
            :columns="['Cliente', 'Nit', 'Teléfono', 'Estado', 'Creado Por', 'Fecha Creación']"
            :total="total"
            :current-page="currentPage"
            :last-page="lastPage"
            :loading="loading"
            :error="error"
            :search="search"
            :filter-value="status"
            :filter-options="[
                { value: 'active',   label: 'Activo' },
                { value: 'inactive', label: 'Inactivo' },
                { value: 'prospect', label: 'Prospecto' },

            ]"
            filter-placeholder="Todos los estados"
            search-placeholder="Buscar cliente..."
            empty-message="No hay clientes"
            empty-hint="Crea tu primer cliente con el botón de arriba"
            @update:search="search = $event"
            @update:filter-value="status = $event"
            @clear-filters="clearFilters"
            @go-to-page="goToPage"
        >
            <template #row="{ item }">
                <td class="px-4 py-3 font-medium text-gray-900">{{ item.name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ item.nit }}</td>
                <td class="px-4 py-3 text-gray-500">{{ item.phone ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span :class="[
                    'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                    item.status === 'active'   ? 'bg-green-100 text-green-700'  :
                    item.status === 'prospect' ? 'bg-blue-100 text-blue-700'    :
                                                 'bg-gray-100 text-gray-500'
                ]">
                    {{ item.status === 'active' ? 'Activo' : item.status === 'prospect' ? 'Prospecto' : 'Inactivo' }}
                </span>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ item.creator?.name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ item.created_at ?? '—' }}</td>
            </template>

            <template #actions="{ item }">
                <button @click="openDetails(item)" class="icon-btn hover:text-blue-600 hover:bg-blue-50" title="Ver detalle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <button @click="openEdit(item)" class="icon-btn hover:text-amber-600 hover:bg-amber-50" title="Editar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </button>
                <button
                    @click="requestDelete(item.id)"
                    :class="[
                        'w-7 h-7 flex items-center justify-center rounded-md transition-colors',
                        confirmDeleteId === item.id
                            ? 'text-white bg-red-500 hover:bg-red-600'
                            : 'text-gray-400 hover:text-red-600 hover:bg-red-50'
                    ]"
                    :title="confirmDeleteId === item.id ? 'Clic para confirmar' : 'Eliminar'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
            </template>
        </DataTable>

        <FormModal
            v-if="modalOpen"
            title="cliente"
            :fields="clientFields"
            :item="editingClient"
            :saving="saving"
            :errors="formErrors"
            @close="onModalClose"
            @submit="handleSubmit"
        />

        <DetailsClientModal
            v-if="detailsOpen && clientDetail"
            :client="clientDetail"
            @close="closeDetails"
        />
        <ConfirmDelete
            v-if="confirmDeleteId !== null"
            message="¿Estás seguro de eliminar este cliente?"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />

    </Layout>
</template>
