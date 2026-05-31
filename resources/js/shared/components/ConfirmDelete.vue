<!-- shared/components/ConfirmModal.vue -->
<script setup>
defineProps({
    message: { type: String, default: '¿Estás seguro de eliminar este registro?' }
})
const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="emit('cancel')" />
            <div class="relative bg-white rounded-xl shadow-xl border border-gray-200 p-6 max-w-sm w-full">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Confirmar eliminación</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ message }}</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button @click="emit('cancel')" class="btn-secondary">Cancelar</button>
                    <button @click="emit('confirm')" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
