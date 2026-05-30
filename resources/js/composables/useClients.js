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
            store.goToPage(1)
            store.fetchClients({ search: val, status: status.value })
        }, 350)
    })

    // ── Filtro de estado ──────────────────────────────────────────────────────
    watch(status, (val) => {
        store.goToPage(1)
        store.fetchClients({ search: search.value, status: val })
    })

    // ── Confirm delete ────────────────────────────────────────────────────────
    const confirmDeleteId = ref(null)

    function requestDelete(id) {
        if (confirmDeleteId.value !== id) {
            confirmDeleteId.value = id
            setTimeout(() => { confirmDeleteId.value = null }, 3000)
            return
        }
        confirmDeleteId.value = null
        store.deleteClient(id)
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
