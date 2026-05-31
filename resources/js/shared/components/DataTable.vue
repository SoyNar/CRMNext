<script setup>
/**
 * DataTable
 *
 * Tabla genérica reutilizable. El padre define:
 *  - columns: cabeceras de la tabla
 *  - slot "row": cómo se renderiza cada fila, recibe { item }
 *  - slot "actions": botones de acción por fila, recibe { item }
 *
 * Uso:
 *  <DataTable :items="clients" :columns="['Cliente','Estado',...]" ...>
 *    <template #row="{ item }">
 *      <td>{{ item.name }}</td>
 *    </template>
 *    <template #actions="{ item }">
 *      <button @click="openEdit(item)">...</button>
 *    </template>
 *  </DataTable>
 */

defineProps({
    items:       { type: Array,   required: true },
    columns:     { type: Array,   required: true }, // strings con los títulos
    total:       { type: Number,  default: 0 },
    currentPage: { type: Number,  default: 1 },
    lastPage:    { type: Number,  default: 1 },
    loading:     { type: Boolean, default: false },
    error:       { type: String,  default: null },
    search:      { type: String,  default: '' },
    // filtro extra opcional (estado, is_primary, etc.)
    filterValue:   { type: String,  default: '' },
    filterOptions: { type: Array,   default: () => [] }, // [{ value, label }]
    filterPlaceholder: { type: String, default: 'Filtrar...' },
    emptyMessage:  { type: String, default: 'No hay registros' },
    emptyHint:     { type: String, default: '' },
    searchPlaceholder: { type: String, default: 'Buscar...' },
})

const emit = defineEmits([
    'update:search',
    'update:filterValue',
    'clear-filters',
    'go-to-page',
])
</script>

<template>
    <div>
        <!-- Filtros -->
        <div class="flex flex-wrap gap-2.5 mb-4">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>                <input
                    :value="search"
                    @input="emit('update:search', $event.target.value)"
                    type="text"
                    :placeholder="searchPlaceholder"
                    class="pl-8 pr-3 h-9 w-64 text-[13px] border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
            </div>
            <select
                v-if="filterOptions.length"
                :value="filterValue"
                @change="emit('update:filterValue', $event.target.value)"
                class="h-9 pl-3 pr-7 text-[13px] border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none"
            >
                <option value="">{{ filterPlaceholder }}</option>
                <option v-for="opt in filterOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
            <button
                v-if="search || filterValue"
                @click="emit('clear-filters')"
                class="h-9 px-3 text-[13px] border border-gray-200 rounded-lg bg-white text-gray-500 hover:bg-gray-50 transition-colors"
            >
                Limpiar
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl border border-gray-200/80 overflow-hidden">
            <!-- thead -->
            <table v-if="!loading && !error && items.length" class="w-full text-[13px]">
                <thead>
                <tr class="border-b border-gray-100 bg-gray-50/80">
                    <th v-for="col in columns" :key="col"
                        class="text-left text-[11px] font-medium text-gray-400 uppercase tracking-wider px-4 py-2.5">
                        {{ col }}
                    </th>
                    <th class="px-4 py-2.5 w-24"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/80">
                <tr v-for="item in items" :key="item.id"
                    class="hover:bg-gray-50/60 transition-colors group">
                    <slot name="row" :item="item" />
                    <td class="px-4 py-2.5">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <slot name="actions" :item="item" />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <!-- paginación -->
            <div v-if="lastPage > 1" class="flex items-center justify-between px-4 py-2.5 border-t border-gray-100 bg-gray-50/80">
                <p class="text-[12px] text-gray-400">
                    Página {{ currentPage }} de {{ lastPage }} · {{ total }} registros
                </p>
                <div class="flex items-center gap-1">
                    <button @click="emit('go-to-page', currentPage - 1)" :disabled="currentPage <= 1"
                            class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-30 text-xs">←</button>
                    <button v-for="p in lastPage" :key="p" @click="emit('go-to-page', p)"
                            :class="['w-7 h-7 flex items-center justify-center rounded-md border text-xs transition-colors',
                            p === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50']">
                        {{ p }}
                    </button>
                    <button @click="emit('go-to-page', currentPage + 1)" :disabled="currentPage >= lastPage"
                            class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-30 text-xs">→</button>
                </div>
            </div>
        </div>
    </div>
</template>
