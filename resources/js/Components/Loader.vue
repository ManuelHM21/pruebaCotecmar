<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm"
      >
        <div class="relative">
          <div
            class="bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center justify-center space-y-4 min-w-[200px]"
          >
            <div v-if="type === 'spinner'" class="relative">
              <div class="w-16 h-16 border-4 border-gray-200 rounded-full"></div>
              <div
                class="w-16 h-16 border-4 border-[#003782] border-t-transparent rounded-full animate-spin absolute top-0 left-0"
              ></div>
            </div>

            <div v-else-if="type === 'dots'" class="flex space-x-2">
              <div
                class="w-3 h-3 bg-[#003782] rounded-full animate-bounce"
                style="animation-delay: 0s"
              ></div>
              <div
                class="w-3 h-3 bg-[#003782] rounded-full animate-bounce"
                style="animation-delay: 0.1s"
              ></div>
              <div
                class="w-3 h-3 bg-[#003782] rounded-full animate-bounce"
                style="animation-delay: 0.2s"
              ></div>
            </div>

            <div v-else-if="type === 'pulse'" class="relative">
              <div class="w-16 h-16 bg-[#003782] rounded-full animate-ping opacity-75"></div>
              <div class="w-16 h-16 bg-[#003782] rounded-full absolute top-0 left-0"></div>
            </div>

            <div v-else-if="type === 'bars'" class="flex space-x-1.5 items-end h-16">
              <div
                class="w-2 bg-[#003782] rounded-full animate-pulse"
                style="animation-delay: 0s; height: 50%"
              ></div>
              <div
                class="w-2 bg-[#003782] rounded-full animate-pulse"
                style="animation-delay: 0.1s; height: 70%"
              ></div>
              <div
                class="w-2 bg-[#003782] rounded-full animate-pulse"
                style="animation-delay: 0.2s; height: 100%"
              ></div>
              <div
                class="w-2 bg-[#003782] rounded-full animate-pulse"
                style="animation-delay: 0.3s; height: 70%"
              ></div>
              <div
                class="w-2 bg-[#003782] rounded-full animate-pulse"
                style="animation-delay: 0.4s; height: 50%"
              ></div>
            </div>

            <div v-else-if="type === 'circle'" class="relative w-16 h-16">
              <svg class="animate-spin" viewBox="0 0 50 50">
                <circle
                  class="stroke-current text-gray-200"
                  cx="25"
                  cy="25"
                  r="20"
                  fill="none"
                  stroke-width="4"
                ></circle>
                <circle
                  class="stroke-current text-[#003782]"
                  cx="25"
                  cy="25"
                  r="20"
                  fill="none"
                  stroke-width="4"
                  stroke-dasharray="80"
                  stroke-dashoffset="60"
                  stroke-linecap="round"
                ></circle>
              </svg>
            </div>

            <div v-else-if="type === 'logo'" class="relative">
              <img
                src="/img/CotecmarLogo.png"
                alt="Cotecmar"
                class="w-16 h-16 animate-pulse"
              />
            </div>

            <div v-if="message" class="text-center">
              <p class="text-gray-700 font-medium">{{ message }}</p>
              <p v-if="submessage" class="text-gray-500 text-sm mt-1">{{ submessage }}</p>
            </div>

            <div v-if="showProgress && progress !== null" class="w-full">
              <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div
                  class="bg-[#003782] h-2 rounded-full transition-all duration-300 ease-out"
                  :style="{ width: progress + '%' }"
                ></div>
              </div>
              <p class="text-xs text-gray-500 text-center mt-1">{{ progress }}%</p>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'spinner',
    validator: (value) => ['spinner', 'dots', 'pulse', 'bars', 'circle', 'logo'].includes(value)
  },
  message: {
    type: String,
    default: ''
  },
  submessage: {
    type: String,
    default: ''
  },
  progress: {
    type: Number,
    default: null,
    validator: (value) => value === null || (value >= 0 && value <= 100)
  },
  showProgress: {
    type: Boolean,
    default: false
  }
})

watch(() => props.show, (value) => {
  if (value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
@keyframes heightPulse {
  0%, 100% {
    height: 50%;
  }
  50% {
    height: 100%;
  }
}
</style>
