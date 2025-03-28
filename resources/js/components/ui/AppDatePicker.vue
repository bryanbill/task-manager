<template>
    <div class="space-y-1">
        <label v-if="label" class="block text-sm font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative rounded-md shadow-sm">
            <input type="date" :value="formattedValue" @input="handleInput" :class="inputClasses"
                :required="required" />
        </div>
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: [Date, String],
        default: null
    },
    label: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const formattedValue = computed(() => {
    if (!props.modelValue) return ''
    const date = new Date(props.modelValue)
    return date.toISOString().split('T')[0]
})

const handleInput = (event) => {
    const value = event.target.value
    emit('update:modelValue', value ? new Date(value) : null)
}

const inputClasses = computed(() => {
    const base = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2'
    return props.error
        ? `${base} border-red-300 text-red-900 placeholder-red-300`
        : base
})
</script>