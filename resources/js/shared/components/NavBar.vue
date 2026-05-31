<script setup>
import { useAuthStore } from "../../stores/auth.js"
const auth = useAuthStore()

function getInitials(name) {
    return name?.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase() ?? '?'
}
</script>

<template>
    <header class="bg-white border-b border-gray-200 px-6 h-14 flex items-center justify-between">

        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                </svg>
            </div>
            <span class="text-[15px] font-medium text-gray-900 tracking-tight">Mini CRM</span>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-[30px] h-[30px] rounded-full bg-indigo-50 flex items-center justify-center text-xs font-medium text-indigo-600 shrink-0">
                {{ getInitials(auth.user?.name) }}
            </div>
            <span class="text-sm text-gray-500">{{ auth.user?.name }}</span>
            <div class="w-px h-4 bg-gray-200"></div>
            <button
                @click="auth.logout"
                class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-red-600 hover:bg-red-50 px-2 py-1 rounded-md transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                Cerrar sesión
            </button>
        </div>
    </header>
</template>
