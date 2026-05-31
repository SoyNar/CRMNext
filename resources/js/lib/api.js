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

export async function request(url, options = {}) {
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

    // en request() — lib/api.js
    if (!res.ok) {
        let error = 'Error en la petición'
        try {
            error = await res.json()
            console.log('🔴 request lanza:', error)  // ¿llega aquí?
        } catch (e) {}
        throw error
    }

    return res.json()
}
