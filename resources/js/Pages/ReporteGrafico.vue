<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    proyectos: Array
});

const chartContainer = ref(null);

const datosGraficos = computed(() => {
    return props.proyectos.map(proyecto => {
        const piezasPendientes = proyecto.bloques.reduce((total, bloque) => {
            return total + bloque.piezas.filter(pieza => pieza.estado === 'Pendiente').length;
        }, 0);
        
        const piezasFabricadas = proyecto.bloques.reduce((total, bloque) => {
            return total + bloque.piezas.filter(pieza => pieza.estado === 'Fabricado').length;
        }, 0);
        
        const pesoTeorico = proyecto.bloques.reduce((total, bloque) => {
            return total + bloque.piezas.reduce((subtotal, pieza) => {
                return subtotal + (parseFloat(pieza.peso_teorico) || 0);
            }, 0);
        }, 0);
        
        const pesoReal = proyecto.bloques.reduce((total, bloque) => {
            return total + bloque.piezas.reduce((subtotal, pieza) => {
                return subtotal + (parseFloat(pieza.peso_real) || 0);
            }, 0);
        }, 0);
        
        const total = piezasPendientes + piezasFabricadas;
        
        return {
            id: proyecto.id,
            nombre: proyecto.nombre,
            pendientes: piezasPendientes,
            fabricadas: piezasFabricadas,
            total: total,
            pesoTeorico: pesoTeorico,
            pesoReal: pesoReal,
            diferenciaPeso: pesoTeorico - pesoReal,
            eficiencia: pesoTeorico > 0 ? ((pesoReal / pesoTeorico) * 100) : 0,
            porcentajeFabricado: total > 0 ? Math.round((piezasFabricadas / total) * 100) : 0,
            bloques: proyecto.bloques.length
        };
    }).filter(proyecto => proyecto.total > 0);
});

const totalesGenerales = computed(() => {
    const totalPendientes = datosGraficos.value.reduce((sum, p) => sum + p.pendientes, 0);
    const totalFabricadas = datosGraficos.value.reduce((sum, p) => sum + p.fabricadas, 0);
    const totalPesoTeorico = datosGraficos.value.reduce((sum, p) => sum + p.pesoTeorico, 0);
    const totalPesoReal = datosGraficos.value.reduce((sum, p) => sum + p.pesoReal, 0);
    const total = totalPendientes + totalFabricadas;
    
    return {
        pendientes: totalPendientes,
        fabricadas: totalFabricadas,
        total: total,
        pesoTeorico: totalPesoTeorico,
        pesoReal: totalPesoReal,
        diferenciaPeso: totalPesoTeorico - totalPesoReal,
        eficienciaGeneral: totalPesoTeorico > 0 ? ((totalPesoReal / totalPesoTeorico) * 100) : 0,
        porcentajeFabricado: total > 0 ? Math.round((totalFabricadas / total) * 100) : 0
    };
});

const distribucionBloques = computed(() => {
    const bloqueData = [];
    datosGraficos.value.forEach(proyecto => {
        bloqueData.push({
            proyecto: proyecto.nombre,
            bloques: proyecto.bloques,
            piezasPorBloque: proyecto.total / proyecto.bloques
        });
    });
    return bloqueData.sort((a, b) => b.bloques - a.bloques);
});

const rankingEficiencia = computed(() => {
    return [...datosGraficos.value]
        .sort((a, b) => b.eficiencia - a.eficiencia)
        .slice(0, 5);
});

const analisisPeso = computed(() => {
    return datosGraficos.value.map(proyecto => ({
        ...proyecto,
        variacionPorcentaje: proyecto.pesoTeorico > 0 ? 
            (((proyecto.pesoReal - proyecto.pesoTeorico) / proyecto.pesoTeorico) * 100) : 0
    })).sort((a, b) => Math.abs(b.variacionPorcentaje) - Math.abs(a.variacionPorcentaje));
});

const getProgressColor = (porcentaje) => {
    if (porcentaje >= 80) return 'bg-green-500';
    if (porcentaje >= 50) return 'bg-yellow-500';
    return 'bg-red-500';
};

const getProgressColorLight = (porcentaje) => {
    if (porcentaje >= 80) return 'bg-green-100';
    if (porcentaje >= 50) return 'bg-yellow-100';
    return 'bg-red-100';
};

const getEficienciaColor = (eficiencia) => {
    if (eficiencia >= 95) return 'text-green-600';
    if (eficiencia >= 85) return 'text-yellow-600';
    return 'text-red-600';
};

const getVariacionColor = (variacion) => {
    if (Math.abs(variacion) <= 5) return 'text-green-600';
    if (Math.abs(variacion) <= 15) return 'text-yellow-600';
    return 'text-red-600';
};
</script>

<template>
    <AppLayout title="Reporte Gráfico">
        <template #header>
            <h2 class="font-semibold text-md text-gray-800 leading-tight">
                Dashboard de Análisis de Producción
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Resumen General -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Resumen Ejecutivo</h3>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-3xl font-bold text-blue-600">{{ datosGraficos.length }}</div>
                                <div class="text-sm text-gray-600">Proyectos Activos</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-3xl font-bold text-green-600">{{ totalesGenerales.fabricadas }}</div>
                                <div class="text-sm text-gray-600">Piezas Fabricadas</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-3xl font-bold text-red-600">{{ totalesGenerales.pendientes }}</div>
                                <div class="text-sm text-gray-600">Piezas Pendientes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de 6 Gráficos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Gráfico 1: Estado de Fabricación por Proyecto -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Estado de Fabricación por Proyecto</h4>
                            <div class="space-y-4">
                                <div v-for="proyecto in datosGraficos.slice(0, 5)" :key="`estado-${proyecto.id}`" class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="font-medium">{{ proyecto.nombre }}</span>
                                        <span>{{ proyecto.fabricadas }}/{{ proyecto.total }}</span>
                                    </div>
                                    <div class="flex h-4 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="bg-green-500 transition-all duration-500" 
                                             :style="{ width: proyecto.porcentajeFabricado + '%' }"></div>
                                        <div class="bg-red-400 transition-all duration-500" 
                                             :style="{ width: (100 - proyecto.porcentajeFabricado) + '%' }"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>{{ proyecto.porcentajeFabricado }}% Completado</span>
                                        <span>{{ 100 - proyecto.porcentajeFabricado }}% Pendiente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 2: Análisis de Peso (Teórico vs Real) -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Análisis de Peso: Teórico vs Real</h4>
                            <div class="space-y-4">
                                <div v-for="proyecto in datosGraficos.slice(0, 5)" :key="`peso-${proyecto.id}`" class="border-l-4 border-blue-500 pl-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-sm">{{ proyecto.nombre }}</span>
                                        <span :class="getEficienciaColor(proyecto.eficiencia)" class="text-sm font-semibold">
                                            {{ proyecto.eficiencia.toFixed(1) }}%
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="bg-blue-50 p-2 rounded">
                                            <div class="text-blue-600 font-semibold">{{ proyecto.pesoTeorico.toFixed(2) }} kg</div>
                                            <div class="text-gray-600">Teórico</div>
                                        </div>
                                        <div class="bg-green-50 p-2 rounded">
                                            <div class="text-green-600 font-semibold">{{ proyecto.pesoReal.toFixed(2) }} kg</div>
                                            <div class="text-gray-600">Real</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 3: Distribución de Bloques por Proyecto -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Bloques</h4>
                            <div class="space-y-3">
                                <div v-for="item in distribucionBloques.slice(0, 6)" :key="`bloques-${item.proyecto}`" 
                                     class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="font-medium text-sm">{{ item.proyecto }}</div>
                                        <div class="text-xs text-gray-600">{{ item.piezasPorBloque.toFixed(1) }} piezas/bloque</div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-2xl font-bold text-blue-600">{{ item.bloques }}</div>
                                        <div class="text-xs text-gray-500">bloques</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 4: Ranking de Eficiencia -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Top 5 - Eficiencia de Peso</h4>
                            <div class="space-y-3">
                                <div v-for="(proyecto, index) in rankingEficiencia" :key="`ranking-${proyecto.id}`" 
                                     class="flex items-center space-x-3 p-3 rounded-lg"
                                     :class="index === 0 ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50'">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm"
                                         :class="index === 0 ? 'bg-yellow-500 text-white' : 
                                                 index === 1 ? 'bg-gray-400 text-white' : 
                                                 index === 2 ? 'bg-orange-400 text-white' : 'bg-blue-500 text-white'">
                                        {{ index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-sm">{{ proyecto.nombre }}</div>
                                        <div class="text-xs text-gray-600">{{ proyecto.pesoReal.toFixed(2) }}kg de {{ proyecto.pesoTeorico.toFixed(2) }}kg</div>
                                    </div>
                                    <div :class="getEficienciaColor(proyecto.eficiencia)" class="font-bold text-lg">
                                        {{ proyecto.eficiencia.toFixed(1) }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 5: Variaciones de Peso por Proyecto -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Variaciones de Peso</h4>
                            <div class="space-y-3">
                                <div v-for="proyecto in analisisPeso.slice(0, 5)" :key="`variacion-${proyecto.id}`" 
                                     class="border rounded-lg p-3">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-sm">{{ proyecto.nombre }}</span>
                                        <span :class="getVariacionColor(proyecto.variacionPorcentaje)" class="font-semibold text-sm">
                                            {{ proyecto.variacionPorcentaje > 0 ? '+' : '' }}{{ proyecto.variacionPorcentaje.toFixed(1) }}%
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-500"
                                                 :class="proyecto.variacionPorcentaje >= 0 ? 'bg-blue-500' : 'bg-red-500'"
                                                 :style="{ width: Math.min(Math.abs(proyecto.variacionPorcentaje), 100) + '%' }">
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            {{ Math.abs(proyecto.diferenciaPeso).toFixed(2) }}kg
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico 6: Resumen de Producción Global -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Resumen Global</h4>
                            <div class="space-y-4">
                                <!-- Progreso Total -->
                                <div class="text-center mb-6">
                                    <div class="relative w-32 h-32 mx-auto mb-4">
                                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                            <path class="text-gray-300" stroke="currentColor" stroke-width="3" fill="none" 
                                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                            <path class="text-blue-600" stroke="currentColor" stroke-width="3" fill="none" 
                                                  :stroke-dasharray="`${totalesGenerales.porcentajeFabricado}, 100`"
                                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-2xl font-bold text-gray-700">{{ totalesGenerales.porcentajeFabricado }}%</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600">Progreso Total de Fabricación</p>
                                </div>

                                <!-- Métricas Clave -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-blue-50 p-3 rounded-lg text-center">
                                        <div class="text-xl font-bold text-blue-600">{{ totalesGenerales.total }}</div>
                                        <div class="text-xs text-gray-600">Total Piezas</div>
                                    </div>
                                    <div class="bg-green-50 p-3 rounded-lg text-center">
                                        <div class="text-xl font-bold text-green-600">{{ totalesGenerales.pesoReal.toFixed(0) }}</div>
                                        <div class="text-xs text-gray-600">Kg Fabricados</div>
                                    </div>
                                    <div class="bg-yellow-50 p-3 rounded-lg text-center">
                                        <div class="text-xl font-bold text-yellow-600">{{ Math.abs(totalesGenerales.diferenciaPeso).toFixed(0) }}</div>
                                        <div class="text-xs text-gray-600">Kg Diferencia</div>
                                    </div>
                                    <div class="bg-purple-50 p-3 rounded-lg text-center">
                                        <div class="text-xl font-bold text-purple-600">{{ datosGraficos.reduce((sum, p) => sum + p.bloques, 0) }}</div>
                                        <div class="text-xs text-gray-600">Total Bloques</div>
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