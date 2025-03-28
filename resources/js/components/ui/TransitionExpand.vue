<template>
    <transition @before-enter="beforeEnter" @enter="enter" @after-enter="afterEnter" @before-leave="beforeLeave"
        @leave="leave" @after-leave="afterLeave">
        <slot />
    </transition>
</template>

<script setup>
import { ref } from 'vue'

const beforeEnter = (el) => {
    el.style.height = '0'
    el.style.overflow = 'hidden'
}

const enter = (el) => {
    el.style.height = `${el.scrollHeight}px`
    el.style.overflow = ''
}

const afterEnter = (el) => {
    el.style.height = ''
    el.style.overflow = ''
}

const beforeLeave = (el) => {
    el.style.height = `${el.scrollHeight}px`
    el.style.overflow = 'hidden'
}

const leave = (el) => {
    requestAnimationFrame(() => {
        el.style.height = '0'
    })
}

const afterLeave = (el) => {
    el.style.height = ''
    el.style.overflow = ''
}
</script>

<style scoped>
/* Transition styles */
.v-enter-active,
.v-leave-active {
    transition: height 0.3s ease-in-out;
    will-change: height;
}

/* This prevents content from peeking out during transition */
.v-enter-from,
.v-leave-to {
    overflow: hidden;
}
</style>