<template>
    <AppLayout title="Gestión de Proyectos">
        <template #header>
            <h2 class="font-semibold text-md text-gray-800 leading-tight">
                Gestión de Proyectos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Formulario de Creación -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Crear Nuevo Proyecto
                        </h3>

                        <form
                            @submit.prevent="enviarProyecto"
                            class="grid grid-cols-1 md:grid-cols-3 gap-4"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >ID del Proyecto</label
                                >
                                <input
                                    v-model="id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required
                                    placeholder="Ej: 130"
                                />
                                <span
                                    v-if="errores.id"
                                    class="text-red-500 text-sm"
                                    >{{ errores.id }}</span
                                >
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Nombre del Proyecto</label
                                >
                                <input
                                    v-model="nombre"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required
                                    placeholder="Nombre del proyecto"
                                />
                                <span
                                    v-if="errores.nombre"
                                    class="text-red-500 text-sm"
                                    >{{ errores.nombre }}</span
                                >
                            </div>

                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    :disabled="procesando"
                                    class="w-full bg-[#003782] hover:bg-[#0047ab] text-white font-medium py-2 px-4 rounded-lg transition-colors disabled:opacity-50"
                                >
                                    {{
                                        procesando
                                            ? "Creando..."
                                            : "Crear Proyecto"
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            Total Proyectos
                                        </dt>
                                        <dd
                                            class="text-3xl font-semibold text-gray-900"
                                        >
                                            {{ proyectos.length }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-green-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            Total Bloques
                                        </dt>
                                        <dd
                                            class="text-3xl font-semibold text-gray-900"
                                        >
                                            {{ totalBloques }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-purple-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            Total Piezas
                                        </dt>
                                        <dd
                                            class="text-3xl font-semibold text-gray-900"
                                        >
                                            {{ totalPiezas }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg
                                        class="h-6 w-6 text-yellow-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt
                                            class="text-sm font-medium text-gray-500 truncate"
                                        >
                                            Progreso Promedio
                                        </dt>
                                        <dd
                                            class="text-3xl font-semibold text-gray-900"
                                        >
                                            {{ progresoPromedio.toFixed(0) }}%
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="flex-1 min-w-64">
                            <input
                                v-model="filtroNombre"
                                type="text"
                                placeholder="Buscar proyectos..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <select
                                v-model="filtroEstado"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Todos los estados</option>
                                <option value="activo">
                                    Proyectos activos
                                </option>
                                <option value="completado">
                                    Proyectos completados
                                </option>
                                <option value="sin-bloques">Sin bloques</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Nombre
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Bloques
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Piezas
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Progreso
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Fecha
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="proyecto in proyectosFiltrados"
                                    :key="proyecto.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    >
                                        {{ proyecto.id }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold"
                                    >
                                        {{ proyecto.nombre }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                        >
                                            {{ proyecto.bloques_count || 0 }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                        >
                                            {{ proyecto.piezas_count || 0 }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        <div class="flex items-center">
                                            <div
                                                class="flex-1 bg-gray-200 rounded-full h-2 mr-2"
                                            >
                                                <div
                                                    :class="
                                                        getProgressColor(
                                                            getProgresoProyecto(
                                                                proyecto
                                                            )
                                                        )
                                                    "
                                                    class="h-2 rounded-full transition-all duration-500"
                                                    :style="{
                                                        width:
                                                            getProgresoProyecto(
                                                                proyecto
                                                            ) + '%',
                                                    }"
                                                ></div>
                                            </div>
                                            <span class="text-xs font-medium"
                                                >{{
                                                    getProgresoProyecto(
                                                        proyecto
                                                    ).toFixed(0)
                                                }}%</span
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="getEstadoBadge(proyecto)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            {{ getEstadoTexto(proyecto) }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            formatearFecha(proyecto.created_at)
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <div class="flex justify-end space-x-2">
                                            <button
                                                @click="
                                                    abrirModalEditar(proyecto)
                                                "
                                                class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md transition-colors"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                    />
                                                </svg>
                                            </button>
                                            <button
                                                @click="
                                                    confirmarEliminar(proyecto)
                                                "
                                                :disabled="
                                                    (proyecto.bloques_count ||
                                                        0) > 0
                                                "
                                                :class="
                                                    (proyecto.bloques_count ||
                                                        0) > 0
                                                        ? 'opacity-50 cursor-not-allowed'
                                                        : ''
                                                "
                                                class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md transition-colors"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="proyectosFiltrados.length === 0"
                        class="text-center py-12"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">
                            No hay proyectos
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Comienza creando un nuevo proyecto.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Edición -->
        <Modal
            :show="modalEditar"
            @close="cerrarModalEditar"
            max-width="md"
            icon="edit"
            variant="default"
            title="Editar Proyecto"
            subtitle="Modifica la información del proyecto"
        >
            <form @submit.prevent="actualizarProyecto" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ID del Proyecto
                    </label>
                    <input
                        disabled
                        v-model="proyectoEditar.id"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Proyecto
                    </label>
                    <input
                        v-model="proyectoEditar.nombre"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    />
                </div>
            </form>

            <template #footer>
                <button
                    type="button"
                    @click="cerrarModalEditar"
                    class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors font-medium"
                >
                    Cancelar
                </button>
                <button
                    @click="actualizarProyecto"
                    :disabled="procesandoModal"
                    class="px-4 py-2 bg-[#003782] hover:bg-[#0047ab] text-white rounded-lg transition-colors disabled:opacity-50 font-medium"
                >
                    {{ procesandoModal ? "Guardando..." : "Guardar Cambios" }}
                </button>
            </template>
        </Modal>

        <!-- Modal de Confirmación de Eliminación -->
        <Modal
            :show="modalEliminar"
            @close="cerrarModalEliminar"
            max-width="md"
            icon="trash"
            variant="danger"
            title="Confirmar eliminación"
            subtitle="Esta acción no se puede deshacer"
        >
            <div class="text-gray-700">
                <p class="mb-2">¿Estás seguro de que deseas eliminar el proyecto?</p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-4">
                    <p class="font-semibold text-red-900">{{ proyectoEliminar?.nombre }}</p>
                    <p class="text-sm text-red-700 mt-1">ID: {{ proyectoEliminar?.id }}</p>
                </div>

                <div
                    v-if="(proyectoEliminar?.bloques_count || 0) > 0"
                    class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg"
                >
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-900">Advertencia</p>
                            <p class="text-sm text-amber-700 mt-1">
                                Este proyecto tiene {{ proyectoEliminar.bloques_count }} bloque(s) asociado(s).
                                No se puede eliminar mientras tenga bloques.
                            </p>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-gray-500 mt-4">
                    Todos los datos asociados a este proyecto se eliminarán permanentemente.
                </p>
            </div>

            <template #footer>
                <button
                    @click="cerrarModalEliminar"
                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors font-medium"
                >
                    Cancelar
                </button>
                <button
                    @click="eliminarProyecto"
                    :disabled="procesandoModal || (proyectoEliminar?.bloques_count || 0) > 0"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors disabled:opacity-50 font-medium"
                >
                    {{ procesandoModal ? "Eliminando..." : "Sí, Eliminar" }}
                </button>
            </template>
        </Modal>

        <!-- Loader -->
        <Loader 
            :show="loaderState.show" 
            :type="loaderState.type"
            :message="loaderState.message"
            :submessage="loaderState.submessage"
        />

        <!-- Alertas -->
        <div
            v-if="alerta.mostrar"
            :class="getAlertaClass()"
            class="fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm"
        >
            <div class="flex items-center">
                <svg
                    v-if="alerta.tipo === 'success'"
                    class="w-5 h-5 mr-2"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                    />
                </svg>
                <svg
                    v-else
                    class="w-5 h-5 mr-2"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                <span class="font-medium">{{ alerta.mensaje }}</span>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Modal from "@/Components/Modal.vue";
import Loader from "@/Components/Loader.vue";


const props = defineProps({
    proyectos: Array,
});


const id = ref("");
const nombre = ref("");
const errores = reactive({});
const procesando = ref(false);


const filtroNombre = ref("");
const filtroEstado = ref("");

const modalEditar = ref(false);
const modalEliminar = ref(false);
const proyectoEditar = reactive({ id: "", nombre: "" });
const proyectoEliminar = ref(null);
const procesandoModal = ref(false);

// Estado del loader
const loaderState = reactive({
    show: false,
    type: 'spinner',
    message: '',
    submessage: ''
});

const alerta = reactive({
    mostrar: false,
    tipo: "success",
    mensaje: "",
});

const proyectosFiltrados = computed(() => {
    return props.proyectos.filter((proyecto) => {
        const coincideNombre =
            proyecto.nombre
                .toLowerCase()
                .includes(filtroNombre.value.toLowerCase()) ||
            proyecto.id
                .toLowerCase()
                .includes(filtroNombre.value.toLowerCase());

        let coincideEstado = true;
        if (filtroEstado.value === "activo") {
            coincideEstado =
                (proyecto.bloques_count || 0) > 0 &&
                getProgresoProyecto(proyecto) < 100;
        } else if (filtroEstado.value === "completado") {
            coincideEstado = getProgresoProyecto(proyecto) === 100;
        } else if (filtroEstado.value === "sin-bloques") {
            coincideEstado = (proyecto.bloques_count || 0) === 0;
        }

        return coincideNombre && coincideEstado;
    });
});

const totalBloques = computed(() => {
    return props.proyectos.reduce(
        (total, proyecto) => total + (proyecto.bloques_count || 0),
        0
    );
});

const totalPiezas = computed(() => {
    return props.proyectos.reduce(
        (total, proyecto) => total + (proyecto.piezas_count || 0),
        0
    );
});

const progresoPromedio = computed(() => {
    if (props.proyectos.length === 0) return 0;
    const progresoTotal = props.proyectos.reduce(
        (total, proyecto) => total + getProgresoProyecto(proyecto),
        0
    );
    return progresoTotal / props.proyectos.length;
});

const enviarProyecto = () => {
    Object.keys(errores).forEach((k) => delete errores[k]);

    if (!id.value) errores.id = "Ingrese el ID";
    if (!nombre.value) errores.nombre = "Ingrese el nombre";
    if (Object.keys(errores).length) return;

    procesando.value = true;
    
    // Activar loader
    loaderState.show = true;
    loaderState.type = 'circle';
    loaderState.message = 'Creando proyecto...';
    loaderState.submessage = 'Por favor espere';

    router.post(
        "/proyectos",
        {
            id: id.value,
            nombre: nombre.value,
        },
        {
            onSuccess: () => {
                mostrarAlerta("success", "Proyecto creado correctamente");
                id.value = "";
                nombre.value = "";
            },
            onError: (err) => {
                Object.assign(errores, err);
                mostrarAlerta("error", "Error al crear el proyecto");
            },
            onFinish: () => {
                procesando.value = false;
                loaderState.show = false;
            },
        }
    );
};

const formatearFecha = (fecha) => {
    return new Date(fecha).toLocaleDateString("es-ES", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};

const getProgresoProyecto = (proyecto) => {
    if (!proyecto.piezas_count || proyecto.piezas_count === 0) return 0;

    const baseProgress = Math.min((proyecto.bloques_count || 0) * 20, 100);
    return baseProgress;
};

const getProgressColor = (progreso) => {
    if (progreso >= 80) return "bg-green-500";
    if (progreso >= 50) return "bg-yellow-500";
    return "bg-red-500";
};

const getEstadoBadge = (proyecto) => {
    const progreso = getProgresoProyecto(proyecto);
    if (progreso === 100) return "bg-green-100 text-green-800";
    if ((proyecto.bloques_count || 0) === 0) return "bg-gray-100 text-gray-800";
    return "bg-blue-100 text-blue-800";
};

const getEstadoTexto = (proyecto) => {
    const progreso = getProgresoProyecto(proyecto);
    if (progreso === 100) return "Completado";
    if ((proyecto.bloques_count || 0) === 0) return "Sin bloques";
    return "Activo";
};

const abrirModalEditar = (proyecto) => {
    proyectoEditar.id = proyecto.id;
    proyectoEditar.nombre = proyecto.nombre;
    modalEditar.value = true;
};

const cerrarModalEditar = () => {
    modalEditar.value = false;
    proyectoEditar.id = "";
    proyectoEditar.nombre = "";
};

const actualizarProyecto = () => {
    procesandoModal.value = true;
    
    // Activar loader
    loaderState.show = true;
    loaderState.type = 'spinner';
    loaderState.message = 'Actualizando proyecto...';
    loaderState.submessage = proyectoEditar.nombre;

    router.put(
        `/proyectos/${proyectoEditar.id}`,
        {
            id: proyectoEditar.id,
            nombre: proyectoEditar.nombre,
        },
        {
            onSuccess: () => {
                mostrarAlerta("success", "Proyecto actualizado exitosamente");
                cerrarModalEditar();
            },
            onError: () => {
                mostrarAlerta("error", "Error al actualizar el proyecto");
            },
            onFinish: () => {
                procesandoModal.value = false;
                loaderState.show = false;
            },
        }
    );
};

const confirmarEliminar = (proyecto) => {
    if ((proyecto.bloques_count || 0) > 0) {
        mostrarAlerta(
            "error",
            "No se puede eliminar un proyecto que tiene bloques asociados"
        );
        return;
    }

    proyectoEliminar.value = proyecto;
    modalEliminar.value = true;
};

const cerrarModalEliminar = () => {
    modalEliminar.value = false;
    proyectoEliminar.value = null;
};

const eliminarProyecto = () => {
    procesandoModal.value = true;
    
    // Activar loader
    loaderState.show = true;
    loaderState.type = 'pulse';
    loaderState.message = 'Eliminando proyecto...';
    loaderState.submessage = proyectoEliminar.value.nombre;

    router.delete(`/proyectos/${proyectoEliminar.value.id}`, {
        onSuccess: () => {
            mostrarAlerta("success", "Proyecto eliminado exitosamente");
            cerrarModalEliminar();
        },
        onError: () => {
            mostrarAlerta("error", "Error al eliminar el proyecto");
        },
        onFinish: () => {
            procesandoModal.value = false;
            loaderState.show = false;
        },
    });
};

const mostrarAlerta = (tipo, mensaje) => {
    alerta.tipo = tipo;
    alerta.mensaje = mensaje;
    alerta.mostrar = true;

    setTimeout(() => {
        alerta.mostrar = false;
    }, 5000);
};

const getAlertaClass = () => {
    return alerta.tipo === "success"
        ? "bg-green-100 text-green-800 border border-green-200"
        : "bg-red-100 text-red-800 border border-red-200";
};

onMounted(() => {
    if (usePage().props.success) {
        mostrarAlerta("success", usePage().props.success);
    }
    if (usePage().props.error) {
        mostrarAlerta("error", usePage().props.error);
    }
});
</script>
