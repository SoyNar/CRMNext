import ClientsList from "../../ClientsShow/components/clients/ClientsList.vue";
import {createApp} from "vue";

const el = document.querySelector('#clients-app')
createApp(ClientsList).use(pinia).mount(el)
