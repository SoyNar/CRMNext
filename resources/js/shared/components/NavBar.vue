<script setup>
import { useAuthStore } from "../../stores/auth.js"

const auth = useAuthStore()
</script>

<template>
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900">Mini CRM</h1>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">{{ auth.user?.name }}</span>
            <button
                @click="auth.logout"
                class="text-sm text-gray-400 hover:text-gray-700 transition-colors"
            >
                Cerrar sesión
            </button>
        </div>
    </header>
</template>
