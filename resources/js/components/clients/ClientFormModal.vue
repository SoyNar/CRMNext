<script setup>
import { ref, watch, computed } from 'vue'
import {useClientStore} from "../../stores/client.js";

const props = defineProps({
    client: { type: Object, default: null },
})
const emit = defineEmits(['close'])

const isEdit = computed(() => !!props.client)
const saving = ref(false)
const errors = ref({})
const store = useClientStore();

const form = ref({
    name:     '',
    nit:    '',
    phone:    '',
    url_page: '',
    status:   'prospect',
})

watch(() => props.client, (c) => {
    if (c) {
        form.value = {
            name:     c.name     ?? '',
            nit:    c.nit    ?? '',
            phone:    c.phone    ?? '',
            url_page: c.url_page ?? '',
            status:   c.status   ?? 'prospect',
        }
    } else {
        resetForm()
    }
}, { immediate: true })

function resetForm() {
    form.value = { name: '', nit: '', phone: '', url_page: '', status: 'prospect' }
    errors.value = {}
}

function close() {
    resetForm()
    emit('close')
}

async function submit() {
    saving.value = true
    errors.value = {}
    try {
       const payload = isEdit.value ? {...form.value, id :props.client.id} : form.value
        console.log("Datos enviados desde el formulario:",form.value)
        await  store.save(payload);
       close();
    } catch (e) {
        errors.value = { _general: 'Error de conexión, intenta de nuevo.' }
    } finally {
        saving.value = false
    }
}
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="close" />
            <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ isEdit ? 'Editar cliente' : 'Nuevo cliente' }}
                    </h2>
                    <button @click="close" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
                    <p v-if="errors._general" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                        {{ errors._general }}
                    </p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text" placeholder="Acme Corp"
                               :class="['w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500', errors.name ? 'border-red-400' : 'border-gray-300']" />
                        <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nit</label>
                        <input v-model="form.nit" type="text" placeholder="nit-empresa"
                               :class="['w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500', errors.nit ? 'border-red-400' : 'border-gray-300']" />
                        <p v-if="errors.nit" class="mt-1 text-xs text-red-500">{{ errors.nit[0] }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input v-model="form.phone" type="text" placeholder="+57 300 000 0000"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <p v-if="errors.phone" class="mt-1 text-xs text-red-500">{{ errors.phone[0] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sitio web</label>
                            <input v-model="form.url_page" type="text" placeholder="https://empresa.com"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <p v-if="errors.url_page" class="mt-1 text-xs text-red-500">{{ errors.url_page[0] }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado <span class="text-red-500">*</span></label>
                        <select v-model="form.status"
                                :class="['w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500', errors.status ? 'border-red-400' : 'border-gray-300']">
                            <option value="prospect">Prospecto</option>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                        <p v-if="errors.status" class="mt-1 text-xs text-red-500">{{ errors.status[0] }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button @click="close" type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button @click="submit" :disabled="saving" type="button"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors flex items-center gap-2">
                        <svg v-if="saving" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        {{ saving ? 'Guardando...' : (isEdit ? 'Guardar cambios' : 'Crear cliente') }}
                    </button>
                </div>

            </div>
        </div>
    </Teleport>
</template>
