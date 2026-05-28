import { defineStore } from 'pinia'
import { ref } from 'vue'

import { getClients } from '../services/clientService'

export const useClientsStore = defineStore('clients', () => {

    const clients = ref([])

    async function fetchClients() {

        clients.value = await getClients()
    }

    return {
        clients,
        fetchClients
    }
})
