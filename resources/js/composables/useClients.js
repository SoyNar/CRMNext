import { ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useClientStore } from "../stores/client.js";

export function useClients() {
    const store = useClientStore()
    const { clients, clientDetail ,loadingDetail,total, currentPage, lastPage, loading, error } = storeToRefs(store)

    // ── Filtros locales ───────────────────────────────────────────────────────
    const search = ref('')
    const status = ref('')

    function clearFilters() {
        search.value = ''
        status.value = ''
    }

    // ── Búsqueda con debounce ─────────────────────────────────────────────────
    let searchTimer = null

    watch(search, (val) => {
        clearTimeout(searchTimer)
        searchTimer = setTimeout(() => {
            store.currentPage = 1
            store.fetchClients({ search: val, status: status.value })
        }, 350)
    })

    // ── Filtro de estado ──────────────────────────────────────────────────────
    watch(status, (val) => {
        store.currentPage = 1
        store.fetchClients({ search: search.value, status: val })
    })

    // ── Confirm delete ────────────────────────────────────────────────────────
    const confirmDeleteId = ref(null)

    function requestDelete(id) {
        confirmDeleteId.value = id
    }

    function confirmDelete() {
        store.deleteClient(confirmDeleteId.value)
        confirmDeleteId.value = null
    }

    function cancelDelete() {
        confirmDeleteId.value = null
    }

    // ── Refresh manual ────────────────────────────────────────────────────────
    function refresh() {
        store.fetchClients({ search: search.value, status: status.value })
    }

    async function loadClientDetail(id) {
        await store.fetchClientDetail(id)
    }

    // ── Paginación ────────────────────────────────────────────────────────────
    function goToPage(page) {
        store.currentPage = page
        store.fetchClients({ search: search.value, status: status.value })
    }

    return {
        clients,
        total,
        currentPage,
        lastPage,
        loading,
        confirmDelete,
        cancelDelete,
        clientDetail,
        loadingDetail,
        loadClientDetail,

        error,
        // Filtros
        search,
        status,
        clearFilters,
        // Delete con confirmación
        confirmDeleteId,
        requestDelete,
        // Navegación
        goToPage,
        refresh,
    }
}
