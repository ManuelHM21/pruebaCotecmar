<template>
  <AppLayout title="Gestión de Piezas">
    <template #header>
      <h2 class="font-semibold text-md text-gray-800 leading-tight">
        Gestión de Piezas
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Formulario de Creación -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Crear Nueva Pieza</h3>

            <form @submit.prevent="enviarPieza" class="grid grid-cols-1 md:grid-cols-5 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID de la Pieza</label>
                <input v-model="id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required placeholder="Ej: 130-1110-01" />
                <span v-if="errores.id" class="text-red-500 text-sm">{{ errores.id }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input v-model="nombre" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required placeholder="Nombre de la pieza" />
                <span v-if="errores.nombre" class="text-red-500 text-sm">{{ errores.nombre }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Peso Teórico (kg)</label>
                <input v-model="peso_teorico" type="number" step="0.01" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required placeholder="0.00" />
                <span v-if="errores.peso_teorico" class="text-red-500 text-sm">{{ errores.peso_teorico }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bloque</label>
                <select v-model="bloque_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                  <option value="">Seleccione un bloque</option>
                  <optgroup v-for="proyecto in proyectosConBloques" :key="proyecto.id" :label="proyecto.nombre">
                    <option v-for="bloque in proyecto.bloques" :key="bloque.id" :value="bloque.id">
                      {{ bloque.nombre }}
                    </option>
                  </optgroup>
                </select>
                <span v-if="errores.bloque_id" class="text-red-500 text-sm">{{ errores.bloque_id }}</span>
              </div>

              <div class="flex items-end">
                <button type="submit" :disabled="procesando"
                        class="w-full bg-[#003782] hover:bg-[#0047ab] text-white font-medium py-2 px-4 rounded-lg transition-colors disabled:opacity-50">
                  {{ procesando ? 'Creando...' : 'Crear Pieza' }}
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
                  <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Piezas</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ piezas.length }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pendientes</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ piezasPendientes.length }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Fabricadas</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ piezasFabricadas.length }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l-3-9m3 9l3-9"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Peso Total (kg)</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ pesoTotalTeorico.toFixed(0) }}</dd>
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
                placeholder="Buscar piezas..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <select v-model="filtroEstado" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                <option value="Pendiente">Pendientes</option>
                <option value="Fabricado">Fabricadas</option>
              </select>
            </div>
            <div>
              <select v-model="filtroBloque" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los bloques</option>
                <option v-for="bloque in bloques" :key="bloque.id" :value="bloque.id">
                  {{ bloque.proyecto.nombre }} - {{ bloque.nombre }}
                </option>
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
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bloque</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso Teórico</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso Real</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="pieza in piezasFiltradas" :key="pieza.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ pieza.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ pieza.nombre }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ pieza.bloque.nombre }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                      {{ pieza.bloque.proyecto.nombre }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                    {{ pieza.peso_teorico }} kg
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                    {{ pieza.peso_real ? pieza.peso_real + ' kg' : '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getEstadoBadge(pieza.estado)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ pieza.estado }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatearFecha(pieza.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <button @click="abrirModalEditar(pieza)"
                              :disabled="pieza.estado === 'Fabricado'"
                              :class="pieza.estado === 'Fabricado' ? 'opacity-50 cursor-not-allowed' : ''"
                              class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button @click="confirmarEliminar(pieza)"
                              :disabled="pieza.estado === 'Fabricado'"
                              :class="pieza.estado === 'Fabricado' ? 'opacity-50 cursor-not-allowed' : ''"
                              class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="piezasFiltradas.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay piezas</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando una nueva pieza.</p>
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
      title="Editar Pieza"
      subtitle="Modifica la información de la pieza"
    >
      <form @submit.prevent="actualizarPieza" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">ID de la Pieza</label>
          <input
            disabled
            v-model="piezaEditar.id"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Pieza</label>
          <input
            v-model="piezaEditar.nombre"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Peso Teórico (kg)</label>
          <input
            v-model="piezaEditar.peso_teorico"
            type="number"
            step="0.01"
            min="0"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Bloque</label>
          <select
            v-model="piezaEditar.bloque_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          >
            <option value="">Seleccione un bloque</option>
            <optgroup v-for="proyecto in proyectosConBloques" :key="proyecto.id" :label="proyecto.nombre">
              <option v-for="bloque in proyecto.bloques" :key="bloque.id" :value="bloque.id">
                {{ bloque.nombre }}
              </option>
            </optgroup>
          </select>
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
          @click="actualizarPieza"
          :disabled="procesandoModal"
          class="px-4 py-2 bg-[#003782] hover:bg-[#0047ab] text-white rounded-lg transition-colors disabled:opacity-50 font-medium"
        >
          {{ procesandoModal ? 'Guardando...' : 'Guardar Cambios' }}
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
        <p class="mb-2">¿Estás seguro de que deseas eliminar la pieza?</p>
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-4">
          <p class="font-semibold text-red-900">{{ piezaEliminar?.nombre }}</p>
          <p class="text-sm text-red-700 mt-1">ID: {{ piezaEliminar?.id }}</p>
          <p class="text-sm text-red-700">Peso: {{ piezaEliminar?.peso_teorico }} kg</p>
        </div>
        <p class="text-sm text-gray-500 mt-4">
          Todos los datos asociados a esta pieza se eliminarán permanentemente.
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
          @click="eliminarPieza"
          :disabled="procesandoModal"
          class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors disabled:opacity-50 font-medium"
        >
          {{ procesandoModal ? 'Eliminando...' : 'Sí, Eliminar' }}
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
    <div v-if="alerta.mostrar" :class="getAlertaClass()" class="fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm">
      <div class="flex items-center">
        <svg v-if="alerta.tipo === 'success'" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        <svg v-else class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium">{{ alerta.mensaje }}</span>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import Loader from '@/Components/Loader.vue'

const props = defineProps({
  bloques: Array,
  piezas: Array
})

const id = ref('')
const nombre = ref('')
const peso_teorico = ref('')
const bloque_id = ref('')
const errores = reactive({})
const procesando = ref(false)

const filtroNombre = ref('')
const filtroEstado = ref('')
const filtroBloque = ref('')

const modalEditar = ref(false)
const modalEliminar = ref(false)
const piezaEditar = reactive({ id: '', nombre: '', peso_teorico: '', bloque_id: '' })
const piezaEliminar = ref(null)
const procesandoModal = ref(false)

// Estado del loader
const loaderState = reactive({
  show: false,
  type: 'spinner',
  message: '',
  submessage: ''
})

const alerta = reactive({
  mostrar: false,
  tipo: 'success',
  mensaje: ''
})

const proyectosConBloques = computed(() => {
  const proyectos = {}
  props.bloques.forEach(bloque => {
    if (!proyectos[bloque.proyecto.id]) {
      proyectos[bloque.proyecto.id] = {
        id: bloque.proyecto.id,
        nombre: bloque.proyecto.nombre,
        bloques: []
      }
    }
    proyectos[bloque.proyecto.id].bloques.push(bloque)
  })
  return Object.values(proyectos)
})

const piezasPendientes = computed(() => {
  return props.piezas.filter(pieza => pieza.estado === 'Pendiente')
})

const piezasFabricadas = computed(() => {
  return props.piezas.filter(pieza => pieza.estado === 'Fabricado')
})

const pesoTotalTeorico = computed(() => {
  return props.piezas.reduce((total, pieza) => total + parseFloat(pieza.peso_teorico || 0), 0)
})

const piezasFiltradas = computed(() => {
  return props.piezas.filter(pieza => {
    const coincideNombre = pieza.nombre.toLowerCase().includes(filtroNombre.value.toLowerCase()) ||
                          pieza.id.toLowerCase().includes(filtroNombre.value.toLowerCase())
    const coincideEstado = !filtroEstado.value || pieza.estado === filtroEstado.value
    const coincideBloque = !filtroBloque.value || pieza.bloque_id === filtroBloque.value

    return coincideNombre && coincideEstado && coincideBloque
  })
})

const enviarPieza = () => {
  Object.keys(errores).forEach(k => delete errores[k])

  if (!id.value) errores.id = 'Ingrese el ID'
  if (!nombre.value) errores.nombre = 'Ingrese el nombre'
  if (!peso_teorico.value) errores.peso_teorico = 'Ingrese el peso teórico'
  if (!bloque_id.value) errores.bloque_id = 'Seleccione un bloque'
  if (Object.keys(errores).length) return

  procesando.value = true

  // Activar loader
  loaderState.show = true
  loaderState.type = 'circle'
  loaderState.message = 'Creando pieza...'
  loaderState.submessage = nombre.value

  router.post('/piezas', {
    id: id.value,
    nombre: nombre.value,
    peso_teorico: peso_teorico.value,
    bloque_id: bloque_id.value
  }, {
    onSuccess: () => {
      mostrarAlerta('success', 'Pieza creada correctamente')
      id.value = ''
      nombre.value = ''
      peso_teorico.value = ''
      bloque_id.value = ''
    },
    onError: (err) => {
      Object.assign(errores, err)
      mostrarAlerta('error', 'Error al crear la pieza')
    },
    onFinish: () => {
      procesando.value = false
      loaderState.show = false
    }
  })
}

const formatearFecha = (fecha) => {
  return new Date(fecha).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const getEstadoBadge = (estado) => {
  return estado === 'Pendiente'
    ? 'bg-red-100 text-red-800'
    : 'bg-green-100 text-green-800'
}

const abrirModalEditar = (pieza) => {
  if (pieza.estado === 'Fabricado') return

  piezaEditar.id = pieza.id
  piezaEditar.nombre = pieza.nombre
  piezaEditar.peso_teorico = pieza.peso_teorico
  piezaEditar.bloque_id = pieza.bloque_id
  modalEditar.value = true
}

const cerrarModalEditar = () => {
  modalEditar.value = false
  piezaEditar.id = ''
  piezaEditar.nombre = ''
  piezaEditar.peso_teorico = ''
  piezaEditar.bloque_id = ''
}

const actualizarPieza = () => {
  procesandoModal.value = true

  // Activar loader
  loaderState.show = true
  loaderState.type = 'spinner'
  loaderState.message = 'Actualizando pieza...'
  loaderState.submessage = piezaEditar.nombre

  router.put(`/piezas/${piezaEditar.id}`, {
    id: piezaEditar.id,
    nombre: piezaEditar.nombre,
    peso_teorico: piezaEditar.peso_teorico,
    bloque_id: piezaEditar.bloque_id
  }, {
    onSuccess: () => {
      mostrarAlerta('success', 'Pieza actualizada exitosamente')
      cerrarModalEditar()
    },
    onError: () => {
      mostrarAlerta('error', 'Error al actualizar la pieza')
    },
    onFinish: () => {
      procesandoModal.value = false
      loaderState.show = false
    }
  })
}

const confirmarEliminar = (pieza) => {
  if (pieza.estado === 'Fabricado') return

  piezaEliminar.value = pieza
  modalEliminar.value = true
}

const cerrarModalEliminar = () => {
  modalEliminar.value = false
  piezaEliminar.value = null
}

const eliminarPieza = () => {
  procesandoModal.value = true

  // Activar loader
  loaderState.show = true
  loaderState.type = 'pulse'
  loaderState.message = 'Eliminando pieza...'
  loaderState.submessage = piezaEliminar.value.nombre

  router.delete(`/piezas/${piezaEliminar.value.id}`, {
    onSuccess: () => {
      mostrarAlerta('success', 'Pieza eliminada exitosamente')
      cerrarModalEliminar()
    },
    onError: () => {
      mostrarAlerta('error', 'Error al eliminar la pieza')
    },
    onFinish: () => {
      procesandoModal.value = false
      loaderState.show = false
    }
  })
}

const mostrarAlerta = (tipo, mensaje) => {
  alerta.tipo = tipo
  alerta.mensaje = mensaje
  alerta.mostrar = true

  setTimeout(() => {
    alerta.mostrar = false
  }, 5000)
}

const getAlertaClass = () => {
  return alerta.tipo === 'success'
    ? 'bg-green-100 text-green-800 border border-green-200'
    : 'bg-red-100 text-red-800 border border-red-200'
}

// Inicialización
onMounted(() => {
  if (usePage().props.success) {
    mostrarAlerta('success', usePage().props.success)
  }
  if (usePage().props.error) {
    mostrarAlerta('error', usePage().props.error)
  }
})
</script>
