import { ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useContactStore } from '@/stores/contact.js'

export function useContacts(clientId = null) {
    const store = useContactStore()
    store.clientId = clientId

    const { contacts, total, currentPage, lastPage, loading, error } = storeToRefs(store)

    const search         = ref('')
    const isPrimary      = ref('')
    const confirmDeleteId = ref(null)

    // ── Filtros ───────────────────────────────────────────────────────────────
    function clearFilters() {
        search.value    = ''
        isPrimary.value = ''
    }

    let searchTimer = null
    watch(search, (val) => {
        clearTimeout(searchTimer)
        searchTimer = setTimeout(() => {
            store.currentPage = 1
            store.fetchContacts({ search: val, isPrimary: isPrimary.value })
        }, 350)
    })

    watch(isPrimary, (val) => {
        store.currentPage = 1
        store.fetchContacts({ search: search.value, isPrimary: val })
    })

    // ── Delete con modal ──────────────────────────────────────────────────────
    function requestDelete(id) {
        confirmDeleteId.value = id        // abre el modal
    }

    function confirmDelete() {
        store.deleteContact(confirmDeleteId.value)
        confirmDeleteId.value = null      // cierra el modal
    }

    function cancelDelete() {
        confirmDeleteId.value = null      // cierra sin borrar
    }

    // ── Paginación / refresco ─────────────────────────────────────────────────
    function refresh() {
        store.fetchContacts({ search: search.value, isPrimary: isPrimary.value })
    }

    function goToPage(page) {
        store.currentPage = page
        store.fetchContacts({ search: search.value, isPrimary: isPrimary.value })
    }

    return {
        contacts, total, currentPage, lastPage, loading, error,
        search, isPrimary, clearFilters,
        confirmDeleteId, requestDelete, confirmDelete, cancelDelete,
        goToPage, refresh,
    }
}
