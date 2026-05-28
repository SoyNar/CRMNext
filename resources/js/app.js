import {createApp}   from "vue";
import  {createPinia} from "pinia";
import ClientsList from "./components/clients/ClientsList.vue";

const pinia = createPinia();


const mounts = [
    {selector: '#clients-app', component:ClientsList},
]

mounts.forEach(({selector,component}) => {
    const el = document.querySelector(selector)
    if(el) createApp(component).use(pinia).mount(el)
})
