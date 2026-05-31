import { request } from '@/lib/api'

export const contactService = {
        getAll({ clientId = null, page = 1, perPage = 15, search = '', isPrimary = '' } = {}) {
            const params = new URLSearchParams({ page, per_page: perPage })
            if (search)    params.set('search', search)
            if (isPrimary !== '') params.set('is_primary', isPrimary)

            const url = clientId
                ? `/contacts/${clientId}/client?${params}`
                : `/contacts?${params}`

            return request(url)
        },
    create(clientId, data) {
        return request(`/contacts/${clientId}`, {
            method: 'POST',
            body: JSON.stringify(data),
        })
    },
    update(clientId, id, data) {
        return request(`/contacts/${clientId}/client/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        })
    },
    remove(clientId, id) {
        return request(`/contacts/${clientId}/client/${id}`, {
            method: 'DELETE',
        })
    },
}
