<template>
  <AppLayout title="Registrar Peso Real">
    <template #header>
      <h2 class="font-semibold text-md text-gray-800 leading-tight">
        Registrar Peso Real de Piezas
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtros y estadísticas -->
        <div class="mb-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
              <div class="text-center">
                <div class="text-3xl font-bold text-[#003882]">{{ piezasPendientes.length }}</div>
                <div class="text-sm text-gray-600">Total Piezas</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-red-600">{{ piezasPendientesFiltradas.length }}</div>
                <div class="text-sm text-gray-600">Pendientes</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ piezasFabricadas.length }}</div>
                <div class="text-sm text-gray-600">Fabricadas</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ porcentajeCompletado }}%</div>
                <div class="text-sm text-gray-600">Completado</div>
              </div>
            </div>

            <!-- Filtros -->
            <div class="flex flex-wrap gap-4 items-center">
              <div class="flex-1 min-w-64">
                <input
                  v-model="filtroNombre"
                  type="text"
                  placeholder="Buscar por nombre de pieza..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#003882] focus:border-transparent"
                />
              </div>
              <div>
                <select v-model="filtroEstado" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#003882] focus:border-transparent">
                  <option value="">Todos los estados</option>
                  <option value="Pendiente">Pendientes</option>
                  <option value="Fabricado">Fabricadas</option>
                </select>
              </div>
              <div>
                <select v-model="filtroProyecto" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#003882] focus:border-transparent">
                  <option value="">Todos los proyectos</option>
                  <option v-for="proyecto in proyectosUnicos" :key="proyecto.id" :value="proyecto.id">
                    {{ proyecto.nombre }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Grid de tarjetas -->
        <div v-if="piezasFiltradas.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🔍</div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">No se encontraron piezas</h3>
          <p class="text-gray-600">Intenta ajustar los filtros de búsqueda.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="pieza in piezasFiltradas"
            :key="pieza.id"
            class="flex flex-col h-[337px] justify-between bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-200"
          >
            <!-- Header de la tarjeta -->
            <div class="p-4 border-b border-gray-100">
              <div class="flex items-center justify-between mb-2">
                <span :class="getEstadoBadge(pieza.estado)" class="px-2 py-1 rounded-full text-xs font-semibold">
                  {{ pieza.estado }}
                </span>
                <span class="text-xs text-gray-500">ID: {{ pieza.id }}</span>
              </div>
              <h3 class="font-semibold text-gray-900 text-lg mb-1 truncate" :title="pieza.nombre">
                {{ pieza.nombre }}
              </h3>
              <p class="text-sm text-gray-600">{{ pieza.bloque.proyecto.nombre }}</p>
            </div>

            <!-- Contenido de la tarjeta -->
            <div class="p-4">
              <div class="space-y-2 mb-4">
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-600">Bloque:</span>
                  <span class="text-sm text-gray-900">{{ pieza.bloque.nombre }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-600">Peso Teórico:</span>
                  <span class="text-sm text-gray-900 font-semibold">{{ pieza.peso_teorico }} kg</span>
                </div>
                <div v-if="pieza.peso_real" class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-600">Peso Real:</span>
                  <span class="text-sm text-gray-900 font-semibold">{{ pieza.peso_real }} kg</span>
                </div>
                <div v-if="pieza.peso_real" class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-600">Diferencia:</span>
                  <span :class="getDiferenciaClass(pieza)" class="text-sm font-semibold">
                    {{ getDiferencia(pieza) }} kg
                  </span>
                </div>
                <div v-if="pieza.registrado_por" class="flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-600">Registrado por:</span>
                  <span class="text-sm text-gray-900">{{ pieza.registrado_por }}</span>
                </div>
              </div>

              <!-- Botón de acción -->
              <button
                v-if="pieza.estado === 'Pendiente'"
                @click="abrirModal(pieza)"
                class="w-full bg-[#003782] hover:bg-[#0047ab] text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Registrar Peso
              </button>

              <div v-else class="w-full bg-green-100 text-green-800 font-medium py-2 px-4 rounded-lg text-center flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Completado
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para registrar peso -->
    <Modal
      :show="modalAbierto"
      @close="cerrarModal"
      max-width="md"
      icon="check"
      variant="default"
      title="Registrar Peso Real"
      subtitle="Ingrese el peso real de la pieza fabricada"
    >
      <form @submit.prevent="enviarRegistro" class="space-y-4">
        <div v-if="piezaModal">
          <!-- Información de la pieza -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg space-y-2 border border-blue-200">
            <h4 class="font-bold text-gray-900 text-lg">{{ piezaModal.nombre }}</h4>
            <div class="text-sm text-gray-700 space-y-1">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span><span class="font-medium">Proyecto:</span> {{ piezaModal.bloque.proyecto.nombre }}</span>
              </div>
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span><span class="font-medium">Bloque:</span> {{ piezaModal.bloque.nombre }}</span>
              </div>
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l-3-9m3 9l3-9"/>
                </svg>
                <span><span class="font-medium">Peso Teórico:</span> <span class="font-bold text-blue-700">{{ piezaModal.peso_teorico }} kg</span></span>
              </div>
            </div>
          </div>

          <!-- Input peso real -->
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Peso Real (kg) <span class="text-red-500">*</span>
            </label>
            <input
              v-model="pesoRealModal"
              @input="calcularDiferenciaModal"
              type="number"
              step="0.01"
              min="0"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
              placeholder="Ingrese el peso real..."
              required
              autofocus
            />
            <span v-if="errores.peso_real" class="text-red-500 text-sm mt-1 block">{{ errores.peso_real }}</span>
          </div>

          <!-- Diferencia calculada -->
          <div v-if="diferenciaModal !== ''" class="bg-gradient-to-r from-blue-50 to-cyan-50 p-4 rounded-lg border border-blue-200">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-blue-900">Diferencia:</span>
              <span :class="getDiferenciaModalClass()" class="text-lg font-bold">
                {{ diferenciaModal }} kg ({{ porcentajeDiferenciaModal }}%)
              </span>
            </div>

            <!-- Advertencia si la diferencia es muy grande -->
            <div v-if="Math.abs(parseFloat(diferenciaModal)) > 0 && Math.abs(porcentajeDiferenciaModal) > 20"
                 class="mt-3 p-3 bg-amber-50 border border-amber-300 rounded-lg text-sm">
              <div class="flex items-start">
                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                  <p class="font-semibold text-amber-900">⚠️ Advertencia</p>
                  <p class="text-amber-800 mt-1">La diferencia es mayor al 20%. Verifique que el valor sea correcto antes de continuar.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>

      <template #footer>
        <button
          type="button"
          @click="cerrarModal"
          class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors font-medium"
        >
          Cancelar
        </button>
        <button
          @click="enviarRegistro"
          :disabled="procesando || !pesoRealModal"
          class="px-4 py-2 bg-[#003782] hover:bg-[#0047ab] text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center font-medium"
        >
          <svg v-if="procesando" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ procesando ? 'Guardando...' : 'Guardar Registro' }}
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

    <!-- Alertas de notificación -->
    <Alert
      :show="alerta.mostrar"
      :type="alerta.tipo"
      :message="alerta.mensaje"
      @close="alerta.mostrar = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import Alert from '@/Components/Alert.vue'
import Loader from '@/Components/Loader.vue'

// Props y datos reactivos
const piezasPendientes = ref(usePage().props.piezas || [])
const filtroNombre = ref('')
const filtroEstado = ref('Pendiente')
const filtroProyecto = ref('')
const modalAbierto = ref(false)
const piezaModal = ref(null)
const pesoRealModal = ref('')
const diferenciaModal = ref('')
const porcentajeDiferenciaModal = ref(0)
const procesando = ref(false)
const errores = reactive({})

// Estado del loader
const loaderState = reactive({
  show: false,
  type: 'spinner',
  message: '',
  submessage: ''
})

// Sistema de alertas
const alerta = reactive({
  mostrar: false,
  tipo: 'success', // 'success' o 'error'
  mensaje: ''
})

// Computed properties
const proyectosUnicos = computed(() => {
  const proyectos = []
  const seen = new Set()

  piezasPendientes.value.forEach(pieza => {
    const proyecto = pieza.bloque.proyecto
    if (!seen.has(proyecto.id)) {
      seen.add(proyecto.id)
      proyectos.push(proyecto)
    }
  })

  return proyectos
})

const piezasPendientesFiltradas = computed(() => {
  return piezasPendientes.value.filter(pieza => pieza.estado === 'Pendiente')
})

const piezasFabricadas = computed(() => {
  return piezasPendientes.value.filter(pieza => pieza.estado === 'Fabricado')
})

const porcentajeCompletado = computed(() => {
  if (piezasPendientes.value.length === 0) return 0
  return Math.round((piezasFabricadas.value.length / piezasPendientes.value.length) * 100)
})

const piezasFiltradas = computed(() => {
  return piezasPendientes.value.filter(pieza => {
    const coincideNombre = pieza.nombre.toLowerCase().includes(filtroNombre.value.toLowerCase())
    const coincideEstado = !filtroEstado.value || pieza.estado === filtroEstado.value
    const coincideProyecto = !filtroProyecto.value || pieza.bloque.proyecto.id == filtroProyecto.value

    return coincideNombre && coincideEstado && coincideProyecto
  })
})

const getEstadoBadge = (estado) => {
  return estado === 'Pendiente'
    ? 'bg-red-100 text-red-800'
    : 'bg-green-100 text-green-800'
}

const getDiferencia = (pieza) => {
  if (!pieza.peso_real) return '0.00'
  const diferencia = parseFloat(pieza.peso_teorico) - parseFloat(pieza.peso_real)
  return diferencia.toFixed(2)
}

const getDiferenciaClass = (pieza) => {
  const diferencia = parseFloat(getDiferencia(pieza))
  if (diferencia > 0) return 'text-red-600'
  if (diferencia < 0) return 'text-blue-600'
  return 'text-gray-600'
}

const getDiferenciaModalClass = () => {
  const diferencia = parseFloat(diferenciaModal.value)
  if (diferencia > 0) return 'text-red-600'
  if (diferencia < 0) return 'text-blue-600'
  return 'text-gray-600'
}

const abrirModal = (pieza) => {
  piezaModal.value = pieza
  pesoRealModal.value = ''
  diferenciaModal.value = ''
  porcentajeDiferenciaModal.value = 0
  Object.keys(errores).forEach(k => delete errores[k])
  modalAbierto.value = true
}

const cerrarModal = () => {
  modalAbierto.value = false
  piezaModal.value = null
  pesoRealModal.value = ''
  diferenciaModal.value = ''
  porcentajeDiferenciaModal.value = 0
  Object.keys(errores).forEach(k => delete errores[k])
}

const calcularDiferenciaModal = () => {
  if (!piezaModal.value || !pesoRealModal.value) {
    diferenciaModal.value = ''
    porcentajeDiferenciaModal.value = 0
    return
  }

  const teorico = parseFloat(piezaModal.value.peso_teorico) || 0
  const real = parseFloat(pesoRealModal.value) || 0
  const diferencia = teorico - real
  diferenciaModal.value = diferencia.toFixed(2)

  if (teorico > 0) {
    porcentajeDiferenciaModal.value = ((diferencia / teorico) * 100).toFixed(1)
  } else {
    porcentajeDiferenciaModal.value = 0
  }
}

const mostrarAlerta = (tipo, mensaje) => {
  alerta.tipo = tipo
  alerta.mensaje = mensaje
  alerta.mostrar = true

  setTimeout(() => {
    alerta.mostrar = false
  }, 5000)
}

// Enviar registro
const enviarRegistro = () => {
  if (!piezaModal.value || !pesoRealModal.value) return

  procesando.value = true
  Object.keys(errores).forEach(k => delete errores[k])

  // Activar loader
  loaderState.show = true
  loaderState.type = 'dots'
  loaderState.message = 'Registrando peso...'
  loaderState.submessage = piezaModal.value.nombre

  router.post('/fabricacion/registrar-peso', {
    pieza_id: piezaModal.value.id,
    peso_real: pesoRealModal.value
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      // Actualizar la pieza en la lista local
      const index = piezasPendientes.value.findIndex(p => p.id === piezaModal.value.id)
      if (index !== -1) {
        piezasPendientes.value[index] = {
          ...piezasPendientes.value[index],
          estado: 'Fabricado',
          peso_real: parseFloat(pesoRealModal.value),
          registrado_por: usePage().props.auth.user.name
        }
      }

      const mensaje = page.props?.flash?.success || '¡Peso registrado exitosamente!'
      mostrarAlerta('success', mensaje)
      cerrarModal()
    },
    onError: (errors) => {
      console.error('Errores de validación:', errors)
      Object.assign(errores, errors)

      const primerError = Object.values(errors)[0]
      if (primerError) {
        mostrarAlerta('error', Array.isArray(primerError) ? primerError[0] : primerError)
      } else {
        mostrarAlerta('error', 'Error al registrar el peso. Verifique los datos.')
      }
    },
    onFinish: () => {
      procesando.value = false
      loaderState.show = false
    }
  })
}

onMounted(() => {
  if (usePage().props.success) {
    mostrarAlerta('success', usePage().props.success)
  }
})
</script>

<style scoped>
.fixed.top-4.right-4 {
  animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>
