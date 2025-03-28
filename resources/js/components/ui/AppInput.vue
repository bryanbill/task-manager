<template>
    <div class="space-y-1">
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="relative rounded-md shadow-sm">
            <input :id="id" :type="type" :value="modelValue" :placeholder="placeholder" :class="inputClasses"
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
        type: [String, Number],
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
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

const id = uuidv4()

const inputClasses = computed(() => {
    const base = 'block w-full rounded-md border border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2'
    return props.error
        ? `${base} border-red-300 text-red-900 placeholder-red-300`
        : base
})
</script>