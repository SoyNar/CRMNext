import { ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useContactStore } from '@/stores/contact.js'

export function useContacts(clientId = null) {
    const store = useContactStore()

    // Si viene clientId lo seteamos en el store
    store.clientId = clientId

    const { contacts, total, currentPage, lastPage, loading, error } = storeToRefs(store)

    const search    = ref('')
    const isPrimary = ref('')

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

    const confirmDeleteId = ref(null)
    function requestDelete(id) {
        if (confirmDeleteId.value !== id) {
            confirmDeleteId.value = id
            setTimeout(() => { confirmDeleteId.value = null }, 3000)
            return
        }
        confirmDeleteId.value = null
        store.deleteContact(id)
    }

    function refresh() {
        store.fetchContacts({ search: search.value, isPrimary: isPrimary.value })
    }

    function goToPage(page) {
        store.currentPage = page
        store.fetchContacts({ search: search.value, isPrimary: isPrimary.value })
    }

    return {
        contacts,
        total,
        currentPage,
        lastPage,
        loading,
        error,
        search,
        isPrimary,
        clearFilters,
        confirmDeleteId,
        requestDelete,
        goToPage,
        refresh,
    }
}
