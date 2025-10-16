<template>
  <AppLayout title="Gestión de Bloques">
    <template #header>
      <h2 class="font-semibold text-md text-gray-800 leading-tight">
        Gestión de Bloques
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Formulario de Creación -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Crear Nuevo Bloque</h3>

            <form @submit.prevent="enviarBloque" class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID del Bloque</label>
                <input v-model="id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required placeholder="Ej: 130-1110" />
                <span v-if="errores.id" class="text-red-500 text-sm">{{ errores.id }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input v-model="nombre" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required placeholder="Nombre del bloque" />
                <span v-if="errores.nombre" class="text-red-500 text-sm">{{ errores.nombre }}</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Proyecto</label>
                <select v-model="proyecto_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                  <option value="">Seleccione un proyecto</option>
                  <option v-for="p in proyectos" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                </select>
                <span v-if="errores.proyecto_id" class="text-red-500 text-sm">{{ errores.proyecto_id }}</span>
              </div>

              <div class="flex items-end">
                <button type="submit" :disabled="procesando"
                        class="w-full bg-[#003782] hover:bg-[#0047ab] text-white font-medium py-2 px-4 rounded-lg transition-colors disabled:opacity-50">
                  {{ procesando ? 'Creando...' : 'Crear Bloque' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Bloques</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ bloques.length }}</dd>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Piezas</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ totalPiezas }}</dd>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Proyectos Activos</dt>
                    <dd class="text-3xl font-semibold text-gray-900">{{ proyectos.length }}</dd>
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
                placeholder="Buscar bloques..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <select v-model="filtroProyecto" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los proyectos</option>
                <option v-for="proyecto in proyectos" :key="proyecto.id" :value="proyecto.id">
                  {{ proyecto.nombre }}
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
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Piezas</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="bloque in bloquesFiltrados" :key="bloque.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ bloque.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ bloque.nombre }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ bloque.proyecto.nombre }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      {{ bloque.piezas_count || 0 }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatearFecha(bloque.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <button @click="abrirModalEditar(bloque)"
                              class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button @click="confirmarEliminar(bloque)"
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

          <div v-if="bloquesFiltrados.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay bloques</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo bloque.</p>
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
      title="Editar Bloque"
      subtitle="Modifica la información del bloque"
    >
      <form @submit.prevent="actualizarBloque" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">ID del Bloque</label>
          <input
            disabled
            v-model="bloqueEditar.id"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Bloque</label>
          <input
            v-model="bloqueEditar.nombre"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
          <select
            v-model="bloqueEditar.proyecto_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            required
          >
            <option value="">Seleccione un proyecto</option>
            <option v-for="p in proyectos" :key="p.id" :value="p.id">{{ p.nombre }}</option>
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
          @click="actualizarBloque"
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
        <p class="mb-2">¿Estás seguro de que deseas eliminar el bloque?</p>
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-4">
          <p class="font-semibold text-red-900">{{ bloqueEliminar?.nombre }}</p>
          <p class="text-sm text-red-700 mt-1">ID: {{ bloqueEliminar?.id }}</p>
        </div>
        <p class="text-sm text-gray-500 mt-4">
          Todos los datos asociados a este bloque se eliminarán permanentemente.
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
          @click="eliminarBloque"
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

// Props
const props = defineProps({
  proyectos: Array,
  bloques: Array
})

const id = ref('')
const nombre = ref('')
const proyecto_id = ref('')
const errores = reactive({})
const procesando = ref(false)

const filtroNombre = ref('')
const filtroProyecto = ref('')

const modalEditar = ref(false)
const modalEliminar = ref(false)
const bloqueEditar = reactive({ id: '', nombre: '', proyecto_id: '' })
const bloqueEliminar = ref(null)
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

const bloquesFiltrados = computed(() => {
  return props.bloques.filter(bloque => {
    const coincideNombre = bloque.nombre.toLowerCase().includes(filtroNombre.value.toLowerCase()) ||
                          bloque.id.toLowerCase().includes(filtroNombre.value.toLowerCase())
    const coincideProyecto = !filtroProyecto.value || bloque.proyecto_id === filtroProyecto.value

    return coincideNombre && coincideProyecto
  })
})

const totalPiezas = computed(() => {
  return props.bloques.reduce((total, bloque) => total + (bloque.piezas_count || 0), 0)
})

const enviarBloque = () => {
  Object.keys(errores).forEach(k => delete errores[k])

  if (!id.value) errores.id = 'Ingrese el ID'
  if (!nombre.value) errores.nombre = 'Ingrese el nombre'
  if (!proyecto_id.value) errores.proyecto_id = 'Seleccione un proyecto'
  if (Object.keys(errores).length) return

  procesando.value = true

  // Activar loader
  loaderState.show = true
  loaderState.type = 'circle'
  loaderState.message = 'Creando bloque...'
  loaderState.submessage = nombre.value

  router.post('/bloques', {
    id: id.value,
    nombre: nombre.value,
    proyecto_id: proyecto_id.value
  }, {
    onSuccess: () => {
      mostrarAlerta('success', 'Bloque creado correctamente')
      id.value = ''
      nombre.value = ''
      proyecto_id.value = ''
    },
    onError: (err) => {
      Object.assign(errores, err)
      mostrarAlerta('error', 'Error al crear el bloque')
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

const abrirModalEditar = (bloque) => {
  bloqueEditar.id = bloque.id
  bloqueEditar.nombre = bloque.nombre
  bloqueEditar.proyecto_id = bloque.proyecto_id
  modalEditar.value = true
}

const cerrarModalEditar = () => {
  modalEditar.value = false
  bloqueEditar.id = ''
  bloqueEditar.nombre = ''
  bloqueEditar.proyecto_id = ''
}

const actualizarBloque = () => {
  procesandoModal.value = true

  // Activar loader
  loaderState.show = true
  loaderState.type = 'spinner'
  loaderState.message = 'Actualizando bloque...'
  loaderState.submessage = bloqueEditar.nombre

  router.put(`/bloques/${bloqueEditar.id}`, {
    id: bloqueEditar.id,
    nombre: bloqueEditar.nombre,
    proyecto_id: bloqueEditar.proyecto_id
  }, {
    onSuccess: () => {
      mostrarAlerta('success', 'Bloque actualizado exitosamente')
      cerrarModalEditar()
    },
    onError: () => {
      mostrarAlerta('error', 'Error al actualizar el bloque')
    },
    onFinish: () => {
      procesandoModal.value = false
      loaderState.show = false
    }
  })
}

const confirmarEliminar = (bloque) => {
  bloqueEliminar.value = bloque
  modalEliminar.value = true
}

const cerrarModalEliminar = () => {
  modalEliminar.value = false
  bloqueEliminar.value = null
}

const eliminarBloque = () => {
  procesandoModal.value = true

  // Activar loader
  loaderState.show = true
  loaderState.type = 'pulse'
  loaderState.message = 'Eliminando bloque...'
  loaderState.submessage = bloqueEliminar.value.nombre

  router.delete(`/bloques/${bloqueEliminar.value.id}`, {
    onSuccess: () => {
      mostrarAlerta('success', 'Bloque eliminado exitosamente')
      cerrarModalEliminar()
    },
    onError: () => {
      mostrarAlerta('error', 'Error al eliminar el bloque')
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

onMounted(() => {
  if (usePage().props.success) {
    mostrarAlerta('success', usePage().props.success)
  }
  if (usePage().props.error) {
    mostrarAlerta('error', usePage().props.error)
  }
})
</script>
