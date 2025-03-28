<template>
    <div @click="router.push({ name: 'tasks.show', params: { id: task.id } })" class="overflow-hidden shadow rounded-lg"
        :class="overdue ? 'bg-red-100' : 'bg-white'">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-medium text-gray-900 truncate">
                        {{ task.title }}
                    </h3>
                    <p v-if="task.description" class="mt-1 text-sm text-gray-500 whitespace-pre-line line-clamp-2">
                        {{ task.description }}
                    </p>
                    <div v-if="task.due_date" class="mt-2 flex items-center text-sm text-gray-500">
                        <CalendarIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" />
                        <span>
                            Due {{ formatDate(task.due_date) }}
                        </span>
                    </div>
                </div>
                <div class="ml-4 flex-shrink-0 flex space-x-2">
                    <AppButton v-on:click.stop="router.push({ name: 'tasks.edit', params: { id: task.id } })"
                        variant="outline" size="sm">
                        Edit
                    </AppButton>
                    <AppButton v-on:click.stop="confirmDelete" variant="danger" size="sm">
                        Delete
                    </AppButton>
                </div>
            </div>
        </div>
    </div>

    <ConfirmationDialog :show="showDeleteDialog" title="Delete Task"
        message="Are you sure you want to delete this task? This action cannot be undone."
        v-on:cancel="showDeleteDialog = false" v-on:confirm="handleDelete" />
</template>

<script setup>
import { ref, computed } from 'vue'
import { CalendarIcon } from '@heroicons/vue/outline'
import AppButton from '@/components/ui/AppButton.vue'
import ConfirmationDialog from '@/components/ui/ConfirmationDialog.vue'
import { useToast } from 'vue-toastification'
import router from '../router'

const props = defineProps({
    task: {
        type: Object,
        required: true
    }
})

const overdue = computed(() => {
    if (!props.task.due_date) return false
    return new Date(props.task.due_date) < new Date()
})

const emit = defineEmits(['delete'])

const toast = useToast()
const showDeleteDialog = ref(false)

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateString).toLocaleDateString(undefined, options)
}

const confirmDelete = () => {
    showDeleteDialog.value = true
}

const handleDelete = async () => {
    try {
        emit('delete', props.task.id)
    } catch (error) {
        toast.error('Failed to delete task')
    } finally {
        showDeleteDialog.value = false
    }
}
</script>