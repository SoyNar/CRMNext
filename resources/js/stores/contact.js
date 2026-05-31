import { defineStore } from 'pinia'
import  {contactService} from "../services/contacService.js";

export const useContactStore = defineStore('contacts', {
    state: () => ({
        contacts:     [],
        total:        0,
        currentPage:  1,
        perPage:     15,
        lastPage:     1,
        loading:      false,
        error:        null,
        clientId:     null,
    }),
    actions: {
        async fetchContacts({ search = '', isPrimary = '' } = {}) {
            this.loading = true
            this.error   = null
            try {
                const data = await contactService.getAll({
                    clientId:  this.clientId,
                    page:      this.currentPage,
                    perPage:   this.perPage,
                    search,
                    isPrimary,
                })
                this.contacts    = data.data
                this.currentPage = data.current_page,

                this.lastPage    = data.last_page
                this.total       = data.total
            } catch {
                this.error = 'No se pudo cargar la lista de contactos.'
            } finally {
                this.loading = false
            }
        },
        async save(data) {
            try {
                if (data.id) {
                    await contactService.update(this.clientId, data.id, data)
                } else {
                    await contactService.create(this.clientId, data)
                }
                await this.fetchContacts()
            } catch {
                this.error = 'No se pudo guardar el contacto.'
            }
        },
        async deleteContact(id) {
            try {
                await contactService.remove(this.clientId, id)
                await this.fetchContacts()
            } catch {
                this.error = 'No se pudo eliminar el contacto.'
            }
        },
        goToPage(page) {
            this.currentPage = page
            this.fetchContacts()
        },
    },
})
