import { defineStore } from 'pinia'
import { clientService } from '@/services/clientService'

export const useClientStore = defineStore('clients', {
    state: () => ({
        clients:     [],
        total:       0,
        currentPage: 1,
        clientDetail: null,
        lastPage:    1,
        loading:     false,
        loadingDetail:false,
        error:       null,
    }),

    actions: {
        async fetchClients({ search = '', status = '' } = {}) {
            this.loading = true
            this.error   = null
            try {
                const data = await clientService.getAll({
                    page: this.currentPage,
                    search,
                    status,
                })
                this.clients     = data.data
                this.currentPage = data.current_page
                this.lastPage    = data.last_page
                this.total       = data.total
            } catch {
                this.error = 'No se pudo cargar la lista de clientes.'
            } finally {
                this.loading = false
            }
        },
        async fetchClientDetail(id) {
            this.loadingDetail = true
            try {
                this.clientDetail= await clientService.getDetails(id)
            } catch {
                this.error = 'No se pudo cargar el detalle del cliente'
            } finally {
                this.loadingDetail = false
            }
        },

        //guardar o editar cliente
        async save(data){
            try {
                if(data.id){
                    await  clientService.update(data.id,data)
                }else {
                    await clientService.create(data);
                }
                await  this.fetchClients()

            }catch {
                this.error = 'No se pudo guardar el cliente';
            }
        },


        async deleteClient(id) {
            try {
                await clientService.remove(id)
                await this.fetchClients()
            } catch {
                this.error = 'No se pudo eliminar el cliente.'
            }
        },

        goToPage(page) {
            this.currentPage = page
            this.fetchClients()
        },
    },
})
