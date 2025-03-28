<template>
    <button :type="type" :class="buttonClasses" :disabled="loading || disabled" @click="$emit('click', $event)">
        <span class="inline-flex items-center">
            <AppLoader v-if="loading" class="h-5 w-5 mr-2" />
            <slot />
        </span>
    </button>
</template>

<script setup>
import { computed } from 'vue'
import AppLoader from './AppLoader.vue'

const props = defineProps({
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'outline', 'danger', 'secondary'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    loading: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    fullWidth: {
        type: Boolean,
        default: false
    }
})

const buttonClasses = computed(() => {
    const base = [
        'inline-flex justify-center items-center border font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-150',
        props.fullWidth ? 'w-full' : ''
    ]

    const variants = {
        primary: 'border-transparent text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        secondary: 'border-transparent text-blue-700 bg-blue-100 hover:bg-blue-200 focus:ring-blue-500',
        outline: 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:ring-blue-500',
        danger: 'border-transparent text-white bg-red-600 hover:bg-red-700 focus:ring-red-500'
    }

    const sizes = {
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2 text-sm',
        lg: 'px-6 py-3 text-base'
    }

    return [...base, variants[props.variant], sizes[props.size]].join(' ')
})

const emit = defineEmits(['click'])
</script>