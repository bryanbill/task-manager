<template>
    <slot v-if="!hasError" />
    <div v-else class="p-6 bg-red-50 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Something went wrong
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>{{ errorMessage }}</p>
                </div>
                <div class="mt-4">
                    <button type="button" @click="resetError"
                        class="text-sm font-medium text-red-700 hover:text-red-600">
                        Try again
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { ExclamationCircleIcon } from '@heroicons/vue/outline'

const hasError = ref(false)
const errorMessage = ref('')

const errorHandler = (error) => {
    hasError.value = true
    errorMessage.value = error.message || 'An unknown error occurred'
}

const resetError = () => {
    hasError.value = false
    errorMessage.value = ''
}

defineExpose({
    errorHandler
})
</script>