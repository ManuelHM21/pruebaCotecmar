<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        :class="[
          'fixed top-4 right-4 p-4 rounded-xl shadow-2xl z-50 max-w-sm',
          'border-l-4 backdrop-blur-sm',
          alertClasses
        ]"
      >
        <div class="flex items-start">
          <!-- Icon -->
          <div class="flex-shrink-0">
            <component :is="iconComponent" :class="['w-6 h-6', iconColorClass]" />
          </div>

          <!-- Content -->
          <div class="ml-3 flex-1">
            <p v-if="title" :class="['text-sm font-bold', titleColorClass]">
              {{ title }}
            </p>
            <p :class="['text-sm', titleColorClass, title ? 'mt-1' : '']">
              {{ message }}
            </p>
          </div>

          <!-- Close Button -->
          <button
            v-if="closeable"
            @click="close"
            :class="[
              'ml-4 flex-shrink-0 rounded-lg p-1.5 inline-flex',
              'hover:bg-white/20 transition-colors focus:outline-none focus:ring-2',
              closeButtonClass
            ]"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>

        <div
          v-if="autoClose && duration"
          class="absolute bottom-0 left-0 h-1 bg-white/30 rounded-full transition-all"
          :style="{ width: progressWidth + '%' }"
        ></div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, ref, watch, onUnmounted, h } from 'vue'

const IconSuccess = () => h('svg', {
  fill: 'currentColor',
  viewBox: '0 0 20 20',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'fill-rule': 'evenodd',
  'd': 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
  'clip-rule': 'evenodd'
}))

const IconError = () => h('svg', {
  fill: 'currentColor',
  viewBox: '0 0 20 20',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'fill-rule': 'evenodd',
  'd': 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
  'clip-rule': 'evenodd'
}))

const IconWarning = () => h('svg', {
  fill: 'currentColor',
  viewBox: '0 0 20 20',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'fill-rule': 'evenodd',
  'd': 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
  'clip-rule': 'evenodd'
}))

const IconInfo = () => h('svg', {
  fill: 'currentColor',
  viewBox: '0 0 20 20',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'fill-rule': 'evenodd',
  'd': 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
  'clip-rule': 'evenodd'
}))

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    required: true
  },
  duration: {
    type: Number,
    default: 5000
  },
  autoClose: {
    type: Boolean,
    default: true
  },
  closeable: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close', 'update:show'])

const progressWidth = ref(100)
let timer = null
let progressTimer = null

const alertClasses = computed(() => {
  const classes = {
    'success': 'bg-green-50/95 border-green-500',
    'error': 'bg-red-50/95 border-red-500',
    'warning': 'bg-yellow-50/95 border-yellow-500',
    'info': 'bg-blue-50/95 border-blue-500'
  }
  return classes[props.type]
})

const titleColorClass = computed(() => {
  const colors = {
    'success': 'text-green-900',
    'error': 'text-red-900',
    'warning': 'text-yellow-900',
    'info': 'text-blue-900'
  }
  return colors[props.type]
})

const iconColorClass = computed(() => {
  const colors = {
    'success': 'text-green-600',
    'error': 'text-red-600',
    'warning': 'text-yellow-600',
    'info': 'text-blue-600'
  }
  return colors[props.type]
})

const closeButtonClass = computed(() => {
  const colors = {
    'success': 'text-green-700 focus:ring-green-500',
    'error': 'text-red-700 focus:ring-red-500',
    'warning': 'text-yellow-700 focus:ring-yellow-500',
    'info': 'text-blue-700 focus:ring-blue-500'
  }
  return colors[props.type]
})

const iconComponent = computed(() => {
  const icons = {
    'success': IconSuccess,
    'error': IconError,
    'warning': IconWarning,
    'info': IconInfo
  }
  return icons[props.type]
})

const close = () => {
  clearTimers()
  emit('close')
  emit('update:show', false)
}

const clearTimers = () => {
  if (timer) {
    clearTimeout(timer)
    timer = null
  }
  if (progressTimer) {
    clearInterval(progressTimer)
    progressTimer = null
  }
}

const startAutoClose = () => {
  if (props.autoClose && props.duration && props.show) {
    progressWidth.value = 100

    timer = setTimeout(() => {
      close()
    }, props.duration)

    const interval = 50
    const decrement = (100 / props.duration) * interval

    progressTimer = setInterval(() => {
      progressWidth.value -= decrement
      if (progressWidth.value <= 0) {
        clearInterval(progressTimer)
        progressTimer = null
      }
    }, interval)
  }
}

watch(() => props.show, (newValue) => {
  if (newValue) {
    startAutoClose()
  } else {
    clearTimers()
  }
}, { immediate: true })

onUnmounted(() => {
  clearTimers()
})
</script>
