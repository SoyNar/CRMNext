const API_URL = '/api'

const JSON_HEADERS = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
}

const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]').content

const AUTH_HEADERS = () => ({
    'X-CSRF-TOKEN': getCsrfToken(),
})

async function request(url, options = {}) {
    const res = await fetch(`${API_URL}${url}`, {
        method: options.method || 'GET',
        credentials: 'include',
        headers: {
            ...JSON_HEADERS,
            ...AUTH_HEADERS(),
            ...(options.headers || {}),
        },
        body: options.body || null,
    })

    if (!res.ok) {
        let error = 'Error en la petición'
        try {
            error = await res.json()
        } catch (e) {}

        throw error
    }

    return res.json()
}

export const clientService = {

    // LISTAR CLIENTES
    getAll({ page = 1, search = '', status = '' } = {}) {
        const params = new URLSearchParams({ page })

        if (search) params.set('search', search)
        if (status) params.set('status', status)

        return request(`/clients?${params}`)
    },

    // DETALLE CLIENTE + CONTACTOS
    getDetails(id) {
        return request(`/clients/${id}`)
    },

    // CREAR
    create(data) {
        return request('/clients', {
            method: 'POST',
            body: JSON.stringify(data),
        })
    },

    // ACTUALIZAR
    update(id, data) {
        return request(`/clients/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data),
        })
    },

    // ELIMINAR
    remove(id) {
        return request(`/clients/${id}`, {
            method: 'DELETE',
        })
    },
}
