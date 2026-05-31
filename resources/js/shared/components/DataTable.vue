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
        <!-- ── Filtros ── -->
        <div class="flex flex-wrap gap-3 mb-4">

            <!-- Búsqueda -->
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input
                    :value="search"
                    @input="emit('update:search', $event.target.value)"
                    type="text"
                    :placeholder="searchPlaceholder"
                    class="pl-9 pr-4 h-9 w-72 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>

            <!-- Filtro extra (opcional) -->
            <select
                v-if="filterOptions.length"
                :value="filterValue"
                @change="emit('update:filterValue', $event.target.value)"
                class="h-9 pl-3 pr-8 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none"
            >
                <option value="">{{ filterPlaceholder }}</option>
                <option
                    v-for="opt in filterOptions"
                    :key="opt.value"
                    :value="opt.value"
                >{{ opt.label }}</option>
            </select>

            <!-- Limpiar -->
            <button
                v-if="search || filterValue"
                @click="emit('clear-filters')"
                class="btn-secondary h-9"
            >
                Limpiar
            </button>

        </div>

        <!-- ── Tabla ── -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            <!-- Loading -->
            <div v-if="loading" class="py-16 flex items-center justify-center text-gray-400 gap-2">
                <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <span class="text-sm">Cargando...</span>
            </div>

            <!-- Error -->
            <div v-else-if="error" class="py-12 text-center text-sm text-red-500">
                {{ error }}
            </div>

            <!-- Vacío -->
            <div v-else-if="items.length === 0" class="py-16 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                </svg>
                <p class="text-gray-500 text-sm font-medium">{{ emptyMessage }}</p>
                <p v-if="emptyHint" class="text-gray-400 text-xs mt-1">{{ emptyHint }}</p>
            </div>

            <!-- Datos -->
            <table v-else class="w-full text-sm">
                <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th
                        v-for="col in columns"
                        :key="col"
                        class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide px-4 py-3"
                    >
                        {{ col }}
                    </th>
                    <!-- columna vacía para acciones -->
                    <th class="px-4 py-3 w-28"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                <tr
                    v-for="item in items"
                    :key="item.id"
                    class="hover:bg-gray-50 transition-colors group"
                >
                    <!-- El padre decide cómo renderiza cada fila -->
                    <slot name="row" :item="item" />

                    <!-- Acciones -->
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <slot name="actions" :item="item" />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <!-- Paginación -->
            <div v-if="lastPage > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-gray-50">
                <p class="text-xs text-gray-500">
                    Página {{ currentPage }} de {{ lastPage }} · {{ total }} registros
                </p>
                <div class="flex items-center gap-1">
                    <button
                        @click="emit('go-to-page', currentPage - 1)"
                        :disabled="currentPage <= 1"
                        class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs"
                    >←</button>

                    <button
                        v-for="p in lastPage"
                        :key="p"
                        @click="emit('go-to-page', p)"
                        :class="[
                            'w-7 h-7 flex items-center justify-center rounded-md border text-xs transition-colors',
                            p === currentPage
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50'
                        ]"
                    >{{ p }}</button>

                    <button
                        @click="emit('go-to-page', currentPage + 1)"
                        :disabled="currentPage >= lastPage"
                        class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed text-xs"
                    >→</button>
                </div>
            </div>

        </div>
    </div>
</template>
