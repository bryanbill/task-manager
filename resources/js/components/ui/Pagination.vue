<template>
    <nav class="flex items-center justify-between px-4 sm:px-0">
        <div class="-mt-px flex w-0 flex-1 justify-center">
            <button v-for="(link, index) in links" :key="index" :class="[
                link.active ? 'bg-gray-100' : 'hover:text-gray-500 hover:border-gray-300',
                'border-t-2 border-transparent px-4 inline-flex items-center text-sm font-medium text-gray-500',
            ]" @click="handleClick(index)" v-html="link.label">
            </button>
        </div>
    </nav>
</template>

<script setup>
const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
    onPageChanged: {
        type: Function,
        required: true,
    },
});

const handleClick = (index) => {
    if (index === 0) {
        props.onPageChanged(1);
    } else if (index === props.links.length - 1) {
        props.onPageChanged(props.links.length - 2);
    } else {
        props.onPageChanged(index + 1);
    }
};
</script>