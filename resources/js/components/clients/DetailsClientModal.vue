<script setup>
const props = defineProps({
    client: {
        type: Object,
        required: true,
    }
})

const emit = defineEmits(['close'])

const STATUS_MAP = {
    active:   { label: 'Activo',    cls: 'badge badge-success' },
    inactive: { label: 'Inactivo',  cls: 'badge badge-danger' },
    prospect: { label: 'Prospecto', cls: 'badge badge-warning' },
}

const AVATAR_COLORS = [
    'bg-blue-100 text-blue-800',
    'bg-violet-100 text-violet-800',
    'bg-amber-100 text-amber-800',
    'bg-teal-100 text-teal-800',
    'bg-rose-100 text-rose-800',
]

function initials(name = '') {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
}

function avatarColor(id) {
    return AVATAR_COLORS[id % AVATAR_COLORS.length]
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('es-CO', {
        day: '2-digit', month: 'short', year: 'numeric'
    })
}
</script>

<template>
    <Teleport to="body">
        <div class="modal-overlay">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40" @click="emit('close')" />

            <!-- Panel -->
            <div class="modal-panel">

                <!-- ── Header ── -->
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div :class="['avatar-lg', avatarColor(client.id)]">
                            {{ initials(client.name) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-medium text-gray-900">{{ client.name }}</h2>
                                <span :class="STATUS_MAP[client.status]?.cls">
                                    {{ STATUS_MAP[client.status]?.label }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Creado por {{ client.creator?.name ?? '—' }} · {{ formatDate(client.created_at) }}
                            </p>
                        </div>
                    </div>

                    <button @click="emit('close')" class="icon-btn" aria-label="Cerrar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- ── Info fields ── -->
                <div class="modal-section grid grid-cols-3 gap-2">
                    <div class="field-card">
                        <p class="field-label">NIT</p>
                        <p class="field-value">{{ client.nit || '—' }}</p>
                    </div>
                    <div class="field-card">
                        <p class="field-label">Teléfono</p>
                        <p class="field-value">{{ client.phone || '—' }}</p>
                    </div>
                    <div class="field-card">
                        <p class="field-label">Sitio web</p>
                        <p class="field-value truncate text-blue-600">{{ client.url_page || '—' }}</p>
                    </div>
                </div>

                <!-- ── Contactos ── -->
                <div class="modal-section">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-gray-700">
                            Contactos
                            <span class="text-gray-400 font-normal">
                                {{ client.contacts?.length > 3
                                ? `3 de ${client.contacts.length}`
                                : client.contacts?.length ?? 0 }}
                            </span>
                        </p>
                        <a
                            v-if="client.contacts?.length > 3"
                            :href="`/clients/${client.id}/contacts`"
                            class="text-xs text-blue-600 hover:text-blue-700 flex items-center gap-1"
                        >
                            Ver todos
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>

                    <!-- Sin contactos -->
                    <p v-if="!client.contacts?.length" class="text-sm text-gray-400">
                        Este cliente no tiene contactos registrados.
                    </p>

                    <!-- Lista (máx 3) -->
                    <ul v-else class="space-y-2">
                        <li
                            v-for="contact in client.contacts.slice(0, 3)"
                            :key="contact.id"
                            class="contact-row"
                        >
                            <div class="flex items-center gap-2.5">
                                <div :class="['avatar-sm', avatarColor(contact.id)]">
                                    {{ initials(`${contact.first_name} ${contact.last_name}`) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 leading-tight">
                                        {{ contact.first_name }} {{ contact.last_name }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ contact.email || '—' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <span v-if="contact.is_primary" class="badge badge-info">Primario</span>
                                <span class="text-xs text-gray-400">{{ contact.position || '—' }}</span>
                            </div>
                        </li>
                    </ul>

                    <p v-if="client.contacts?.length > 3" class="text-xs text-gray-400 mt-2 pl-0.5">
                        y {{ client.contacts.length - 3 }}
                        contacto{{ client.contacts.length - 3 !== 1 ? 's' : '' }} más...
                    </p>
                </div>

                <!-- ── Footer ── -->
                <div class="modal-footer">
                    <button @click="emit('close')" class="btn-secondary">
                        Cerrar
                    </button>
                    <a :href="`/clients/${client.id}/contacts`" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Gestionar contactos
                    </a>
                </div>

            </div>
        </div>
    </Teleport>
</template>
