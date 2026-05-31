<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    title:  { type: String, required: true },
    fields: { type: Array,  required: true },
    item:   { type: Object, default: null },
    saving: { type: Boolean, default: false },
    errors: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['close', 'submit'])

const isEdit = computed(() => !!props.item)

// Construye el form dinámicamente desde fields
const form = ref({})

function buildForm() {
    form.value = Object.fromEntries(
        props.fields.map(f => [f.key, props.item?.[f.key] ?? f.default ?? ''])
    )
}

watch(() => props.item, buildForm, { immediate: true })

function close() {
    buildForm()
    emit('close')
}

function submit() {
    emit('submit', { ...form.value })
}
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="close" />
            <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ isEdit ? `Editar ${title}` : `Nuevo ${title}` }}
                    </h2>
                    <button @click="close" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
                    <p v-if="errors._general" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                        {{ errors._general }}
                    </p>

                    <template v-for="field in fields" :key="field.key">

                        <!-- checkbox -->
                        <div v-if="field.type === 'checkbox'" class="flex items-center gap-2">
                            <input
                                :id="field.key"
                                type="checkbox"
                                v-model="form[field.key]"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded"
                            />
                            <label :for="field.key" class="text-sm font-medium text-gray-700">
                                {{ field.label }}
                            </label>
                        </div>

                        <!-- select -->
                        <div v-else-if="field.type === 'select'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ field.label }}
                                <span v-if="field.required" class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form[field.key]"
                                :class="['w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500', errors[field.key] ? 'border-red-400' : 'border-gray-300']"
                            >
                                <option v-for="opt in field.options" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </option>
                            </select>
                            <p v-if="errors[field.key]" class="mt-1 text-xs text-red-500">{{ errors[field.key][0] }}</p>
                        </div>

                        <!-- input texto, email, tel -->
                        <div v-else>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ field.label }}
                                <span v-if="field.required" class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form[field.key]"
                                :type="field.type || 'text'"
                                :placeholder="field.placeholder ?? ''"
                                :class="['w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500', errors[field.key] ? 'border-red-400' : 'border-gray-300']"
                            />
                            <p v-if="errors[field.key]" class="mt-1 text-xs text-red-500">{{ errors[field.key][0] }}</p>
                        </div>

                    </template>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button @click="close" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button @click="submit" :disabled="saving" type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors flex items-center gap-2">
                        <svg v-if="saving" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        {{ saving ? 'Guardando...' : (isEdit ? 'Guardar cambios' : 'Crear') }}
                    </button>
                </div>

            </div>
        </div>
    </Teleport>
</template>
