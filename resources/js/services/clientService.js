// src/services/clientService.js
import { request } from '@/lib/api'

export const clientService = {
    getAll({ page = 1, search = '', status = '' } = {}) {
        const params = new URLSearchParams({ page })
        if (search) params.set('search', search)
        if (status) params.set('status', status)
        return request(`/clients?${params}`)
    },
    async getDetails(id) {
        const response = await request(`/clients/${id}`)
        return response.data
    },
    create(data) {
        return request('/clients', {
            method: 'POST',
            body: JSON.stringify(data),
        })
    },
    update(id, data) {
        return request(`/clients/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        })
    },
    remove(id) {
        return request(`/clients/${id}`, {
            method: 'DELETE',
        })
    },
}
