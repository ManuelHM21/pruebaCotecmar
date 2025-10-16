<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
        @click="closeOnBackdrop && close()"
      >
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
        <Transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to-class="opacity-100 translate-y-0 sm:scale-100"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 translate-y-0 sm:scale-100"
          leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div
            v-if="show"
            class="relative flex min-h-full items-center justify-center"
          >
            <div
              @click.stop
              :class="[
                'relative w-full transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all',
                maxWidthClass
              ]"
            >
              <div
                v-if="$slots.header || title"
                :class="[
                  'relative px-6 py-5 border-b border-gray-200',
                  headerClass
                ]"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div
                      v-if="icon"
                      :class="[
                        'flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-xl',
                        iconBackgroundClass
                      ]"
                    >
                      <component :is="iconComponent" :class="['w-6 h-6', iconColorClass]" />
                    </div>

                    <div>
                      <slot name="header">
                        <h3 class="text-xl font-bold text-gray-900">
                          {{ title }}
                        </h3>
                        <p v-if="subtitle" class="mt-1 text-sm text-gray-500">
                          {{ subtitle }}
                        </p>
                      </slot>
                    </div>
                  </div>

                  <button
                    v-if="closeable"
                    @click="close"
                    type="button"
                    class="flex-shrink-0 rounded-lg p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
              </div>

              <div :class="['px-6 py-6', bodyClass]">
                <slot />
              </div>
              <div
                v-if="$slots.footer"
                :class="[
                  'px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3',
                  footerClass
                ]"
              >
                <slot name="footer" />
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, watch, onMounted, onUnmounted, h } from 'vue'

const IconEdit = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'stroke-linecap': 'round',
  'stroke-linejoin': 'round',
  'stroke-width': '2',
  d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
}))

const IconTrash = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'stroke-linecap': 'round',
  'stroke-linejoin': 'round',
  'stroke-width': '2',
  d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z'
}))

const IconInfo = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'stroke-linecap': 'round',
  'stroke-linejoin': 'round',
  'stroke-width': '2',
  d: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
}))

const IconCheck = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'stroke-linecap': 'round',
  'stroke-linejoin': 'round',
  'stroke-width': '2',
  d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
}))

const IconPlus = () => h('svg', {
  fill: 'none',
  stroke: 'currentColor',
  viewBox: '0 0 24 24',
  xmlns: 'http://www.w3.org/2000/svg'
}, h('path', {
  'stroke-linecap': 'round',
  'stroke-linejoin': 'round',
  'stroke-width': '2',
  d: 'M12 4v16m8-8H4'
}))

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  maxWidth: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'].includes(value)
  },
  closeable: {
    type: Boolean,
    default: true
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  },
  icon: {
    type: String,
    default: '',
    validator: (value) => ['', 'edit', 'trash', 'info', 'check', 'plus'].includes(value)
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'danger', 'success', 'info', 'warning'].includes(value)
  },
  headerClass: {
    type: String,
    default: ''
  },
  bodyClass: {
    type: String,
    default: ''
  },
  footerClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close', 'update:show'])

const maxWidthClass = computed(() => {
  const widths = {
    'sm': 'sm:max-w-sm',
    'md': 'sm:max-w-md',
    'lg': 'sm:max-w-lg',
    'xl': 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    '3xl': 'sm:max-w-3xl',
    '4xl': 'sm:max-w-4xl',
    '5xl': 'sm:max-w-5xl',
    '6xl': 'sm:max-w-6xl',
    '7xl': 'sm:max-w-7xl'
  }
  return widths[props.maxWidth]
})

const iconComponent = computed(() => {
  const icons = {
    'edit': IconEdit,
    'trash': IconTrash,
    'info': IconInfo,
    'check': IconCheck,
    'plus': IconPlus
  }
  return icons[props.icon] || null
})

const iconBackgroundClass = computed(() => {
  const backgrounds = {
    'default': 'bg-blue-100',
    'danger': 'bg-red-100',
    'success': 'bg-green-100',
    'info': 'bg-blue-100',
    'warning': 'bg-yellow-100'
  }
  return backgrounds[props.variant]
})

const iconColorClass = computed(() => {
  const colors = {
    'default': 'text-blue-600',
    'danger': 'text-red-600',
    'success': 'text-green-600',
    'info': 'text-blue-600',
    'warning': 'text-yellow-600'
  }
  return colors[props.variant]
})

const close = () => {
  if (props.closeable) {
    emit('close')
    emit('update:show', false)
  }
}

const closeOnEscape = (e) => {
  if (e.key === 'Escape' && props.show && props.closeable) {
    close()
  }
}

watch(() => props.show, (value) => {
  if (value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  document.addEventListener('keydown', closeOnEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape)
  document.body.style.overflow = ''
})
</script>

