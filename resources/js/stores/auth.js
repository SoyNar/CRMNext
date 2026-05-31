import { defineStore } from 'pinia'
import { ref } from 'vue'
import { request } from '@/lib/api'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)

    async function fetchUser() {
        try {
            user.value = await request('/user')
        } catch (e) {
            user.value = null
        }
    }

    async function logout() {
        await fetch('/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        window.location.replace('/login')
    }

    return { user, fetchUser, logout }
})
