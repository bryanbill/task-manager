<template>
    <div class="space-y-1">
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="mt-1">
            <textarea :id="id" :rows="rows" :value="modelValue" :placeholder="placeholder" :class="textareaClasses"
                :required="required" @input="$emit('update:modelValue', $event.target.value)" />
        </div>
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { v4 as uuidv4 } from 'uuid'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    rows: {
        type: Number,
        default: 3
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

const id = uuidv4()

const textareaClasses = computed(() => {
    const base = 'shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md'
    return props.error
        ? `${base} border-red-300 text-red-900 placeholder-red-300`
        : base
})
</script>