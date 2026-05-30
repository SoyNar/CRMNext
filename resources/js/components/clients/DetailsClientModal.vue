<script setup>
const props = defineProps({
    client: {
        type: Object,
        required: true,
    }
})

const emit = defineEmits(['close'])

const STATUS_MAP = {
    active: {
        label: 'Activo',
        cls: 'bg-green-50 text-green-700 ring-green-600/20'
    },
    inactive: {
        label: 'Inactivo',
        cls: 'bg-red-50 text-red-700 ring-red-600/20'
    },
    prospect: {
        label: 'Prospecto',
        cls: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20'
    },
}
</script>

<template>
    <Teleport to="body">

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/40"
                @click="emit('close')"
            />

            <!-- Modal -->
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

                <!-- Header -->
                <div class="flex items-start justify-between px-6 py-5 border-b border-gray-200">

                    <div>
                        <div class="flex items-center gap-3">

                            <h2 class="text-xl font-semibold text-gray-900">
                                {{ client.name }}
                            </h2>

                            <span
                                :class="[
                                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset',
                                    STATUS_MAP[client.status]?.cls
                                ]"
                            >
                                {{ STATUS_MAP[client.status]?.label }}
                            </span>

                        </div>

                        <p class="text-sm text-gray-400 mt-1">
                            Información del cliente
                        </p>
                    </div>

                    <button
                        @click="emit('close')"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        ✕
                    </button>

                </div>

                <!-- Body -->
                <div class="px-6 py-6 space-y-5">

                    <!-- Info cliente -->
                    <div class="grid grid-cols-2 gap-4">

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-xs text-gray-400">Nit</p>
                            <p class="text-sm font-medium">{{ client.nit || '—' }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-xs text-gray-400">Teléfono</p>
                            <p class="text-sm font-medium">{{ client.phone || '—' }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-xs text-gray-400">Sitio web</p>
                            <p class="text-sm font-medium text-blue-600">
                                {{ client.url_page || '—' }}
                            </p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-xs text-gray-400">Creado</p>
                            <p class="text-sm font-medium">
                                {{
                                    new Date(client.created_at)
                                        .toLocaleDateString('es-CO', {
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                        })
                                }}
                            </p>
                        </div>

                    </div>

                    <!-- Acción contactos -->
                    <div class="pt-4 flex items-center justify-between border-t border-gray-200">

                        <p class="text-sm text-gray-500">
                            Gestiona los contactos de este cliente en una vista dedicada
                        </p>

                        <a
                            :href="`/clients/${client.id}/contacts`"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            Gestionar contactos
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </Teleport>
</template>
