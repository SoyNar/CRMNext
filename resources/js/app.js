import { createApp } from "vue"
import { createPinia } from "pinia"
import { useAuthStore } from "./stores/auth.js"
import ClientsList from "./pages/ClientsList.vue"
import ContactsView from "./pages/ContactsView.vue"

const pinia = createPinia()

const publicRoutes = ['/login', '/register']

async function init() {
    const isPublicPage = publicRoutes.includes(window.location.pathname)

    if (isPublicPage) {
        // En páginas públicas solo monta si hay elementos
        const mounts = [
            { selector: '#clients-app', component: ClientsList },
            { selector: '#contacts-app', component: ContactsView },
        ]
        mounts.forEach(({ selector, component }) => {
            const el = document.querySelector(selector)
            if (el) createApp(component).use(pinia).mount(el)
        })
        return
    }

    await fetch('/sanctum/csrf-cookie', { credentials: 'include' })
    const auth = useAuthStore(pinia)
    await auth.fetchUser()

    if (!auth.user) {
        window.location.replace('/login')
        return
    }

    const mounts = [
        { selector: '#clients-app', component: ClientsList },
        { selector: '#contacts-app', component: ContactsView },
    ]
    mounts.forEach(({ selector, component }) => {
        const el = document.querySelector(selector)
        if (el) createApp(component).use(pinia).mount(el)
    })
}

init()
