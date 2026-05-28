import axios from 'axios'

export async function getClients() {

    const response = await axios.get('/api/clients')

    return response.data
}
