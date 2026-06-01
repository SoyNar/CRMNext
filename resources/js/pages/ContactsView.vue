<script setup>
import { onMounted, ref } from 'vue'
import DataTable from "../shared/components/DataTable.vue"
import Layout from "../shared/components/Layout.vue"
import FormModal from "../shared/components/FormModal.vue"
import { useContacts } from "../composables/useContacts.js"
import { useContactStore } from "../stores/contact.js"
import ConfirmDelete from "../shared/components/ConfirmDelete.vue";

const el         = document.querySelector('#contacts-app')
const clientId   = el?.dataset.clientId   || null
const clientName = el?.dataset.clientName || null

const {
    contacts, total, currentPage, lastPage, loading, error,
    search, isPrimary, clearFilters,
    confirmDeleteId, requestDelete, confirmDelete, cancelDelete,
    goToPage, refresh,
}  = useContacts(clientId)

const store = useContactStore()

// ── Campos del formulario ─────────────────────────────────────────────────────
const contactFields = [
    { key: 'first_name', label: 'Nombre',             required: true, placeholder: 'Juan' },
    { key: 'last_name',  label: 'Apellido',            required: true, placeholder: 'Pérez' },
    { key: 'email',      label: 'Email',     type: 'email',            placeholder: 'juan@correo.com' },
    { key: 'phone',      label: 'Teléfono',                            placeholder: '+57 300 000 0000' },
    { key: 'position',      label: 'Cargo',                               placeholder: 'Gerente' },
    { key: 'is_primary', label: 'Contacto principal', type: 'checkbox', default: false },
]

// ── Modal ─────────────────────────────────────────────────────────────────────
const modalOpen      = ref(false)
const editingContact = ref(null)
const saving         = ref(false)
const formErrors     = ref({})

function openCreate() { editingContact.value = null; modalOpen.value = true }
function openEdit(c)  { console.log('openEdit recibe:', c); editingContact.value = c;    modalOpen.value = true }
function onModalClose() {
    modalOpen.value = false
    formErrors.value = {}
    refresh()
}

async function handleSubmit(data) {
    saving.value = true
    formErrors.value = {}
    try {
        const payload = editingContact.value
            ? { ...data, id: editingContact.value.id, client_id: editingContact.value.client_id }
            : { ...data, client_id: clientId }

        console.log('payload final:', payload)
        await store.save(payload)
        onModalClose()
    } catch (e) {
        formErrors.value = e?.errors ?? { _general: 'Error al guardar.' }
    } finally {
        saving.value = false
    }
}

onMounted(refresh)
</script>

<template>
    <Layout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2">

                    <a v-if="clientName"
                       href="/clients"
                       class="flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 transition-colors"
                       title="Volver a clientes"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Clientes
                    </a>

                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ clientName ? `Contactos de ${clientName}` : 'Contactos' }}
                    </h2>

                </div>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ total }} contacto{{ total !== 1 ? 's' : '' }} registrado{{ total !== 1 ? 's' : '' }}
                </p>
            </div>
            <button @click="openCreate" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo contacto
            </button>
        </div>

        <DataTable
            :items="contacts"
            :columns="['Nombre', 'Email', 'Teléfono', 'Cargo', 'Principal']"
            :total="total"
            :current-page="currentPage"
            :last-page="lastPage"
            :loading="loading"
            :error="error"
            :search="search"
            :filter-value="isPrimary"
            :filter-options="[
                { value: '1', label: 'Principal' },
            ]"
            filter-placeholder="Todos"
            search-placeholder="Buscar contacto..."
            empty-message="No hay contactos"
            empty-hint="Crea tu primer contacto con el botón de arriba"
            @update:search="search = $event"
            @update:filter-value="isPrimary = $event"
            @clear-filters="clearFilters"
            @go-to-page="goToPage"
        >
            <template #row="{ item }">
                <td class="px-4 py-3 font-medium text-gray-900">
                    {{ item.first_name }} {{ item.last_name }}
                </td>
                <td class="px-4 py-3 text-gray-500">{{ item.email ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ item.phone ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ item.position ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span :class="[
                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                        item.is_primary
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-gray-100 text-gray-500'
                    ]">
                        {{ item.is_primary ? 'Principal' : 'General' }}
                    </span>
                </td>
            </template>

            <template #actions="{ item }">
                <button
                    @click="openEdit(item)"
                    class="icon-btn hover:text-amber-600 hover:bg-amber-50"
                    title="Editar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </button>
                <button
                    @click="requestDelete(item.id)"
                    class="icon-btn hover:text-red-600 hover:bg-red-50"
                    title="Eliminar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
            </template>
        </DataTable>

        <FormModal
            v-if="modalOpen"
            title="contacto"
            :fields="contactFields"
            :item="editingContact"
            :saving="saving"
            :errors="formErrors"
            @close="onModalClose"
            @submit="handleSubmit"
        />
        <ConfirmDelete
            v-if="confirmDeleteId !== null"
            message="¿Estás seguro de eliminar este contacto?"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />

    </Layout>
</template>
