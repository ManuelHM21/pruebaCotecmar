<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    proyectos: Array
});

const proyectosConPendientes = computed(() => {
    return props.proyectos.filter(proyecto => {
        return proyecto.bloques.some(bloque => bloque.piezas.length > 0);
    });
});

const totalPiezasPendientes = computed(() => {
    return proyectosConPendientes.value.reduce((total, proyecto) => {
        return total + proyecto.bloques.reduce((subtotal, bloque) => {
            return subtotal + bloque.piezas.length;
        }, 0);
    }, 0);
});
</script>

<template>
    <AppLayout title="Reporte de Piezas Pendientes">
        <template #header>
            <h2 class="font-semibold text-md text-gray-800 leading-tight">
                Reporte de Piezas Pendientes por Proyecto
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Resumen -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-blue-800 mb-2">Resumen General</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ proyectosConPendientes.length }}</div>
                                    <div class="text-sm text-blue-700">Proyectos con Pendientes</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600">{{ totalPiezasPendientes }}</div>
                                    <div class="text-sm text-orange-700">Total Piezas Pendientes</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">
                                        {{ Math.round((totalPiezasPendientes / (totalPiezasPendientes || 1)) * 100) }}%
                                    </div>
                                    <div class="text-sm text-red-700">Pendiente de Fabricación</div>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte por Proyecto -->
                        <div v-if="proyectosConPendientes.length === 0" class="text-center py-8 text-gray-500">
                            <div class="text-6xl mb-4">🎉</div>
                            <h3 class="text-xl font-semibold mb-2">¡Excelente trabajo!</h3>
                            <p>No hay piezas pendientes en ningún proyecto.</p>
                        </div>

                        <div v-else class="space-y-6">
                            <div v-for="proyecto in proyectosConPendientes" :key="proyecto.id" 
                                 class="border border-gray-200 rounded-lg overflow-hidden">
                                
                                <!-- Header del Proyecto -->
                                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ proyecto.nombre }}
                                            </h3>
                                            <p class="text-sm text-gray-600">ID: {{ proyecto.id }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-red-600">
                                                {{ proyecto.bloques.reduce((total, bloque) => total + bloque.piezas.length, 0) }}
                                            </div>
                                            <div class="text-sm text-red-700">Piezas Pendientes</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bloques del Proyecto -->
                                <div class="divide-y divide-gray-200">
                                    <div v-for="bloque in proyecto.bloques" :key="bloque.id" 
                                         v-show="bloque.piezas.length > 0"
                                         class="px-6 py-4">
                                        
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium text-gray-800">
                                                Bloque: {{ bloque.nombre }}
                                            </h4>
                                            <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                                {{ bloque.piezas.length }} pendientes
                                            </span>
                                        </div>

                                        <!-- Tabla de Piezas -->
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            ID Pieza
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Nombre
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Peso Teórico
                                                        </th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Estado
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    <tr v-for="pieza in bloque.piezas" :key="pieza.id" class="hover:bg-gray-50">
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ pieza.id }}
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                            {{ pieza.nombre }}
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                                            {{ pieza.peso_teorico }} kg
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap">
                                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                                                {{ pieza.estado }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>