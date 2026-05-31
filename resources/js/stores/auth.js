import { defineStore } from 'pinia'
import { ref } from 'vue'
import { request } from '@/lib/api'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)

    async function fetchUser() {
        user.value = await request('/user')
    }

    async function logout() {
        await fetch('/logout', { method: 'POST', credentials: 'include' })
        window.location.replace('/login')    }

    return { user, fetchUser, logout }
})
