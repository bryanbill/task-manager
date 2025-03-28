<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-start mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ task.title }}</h1>
                <div class="flex space-x-2">
                    <AppButton v-on:click="router.push({ name: 'tasks.edit', params: { id: task.id } })"
                        variant="outline" size="sm">
                        Edit
                    </AppButton>
                    <AppButton v-on:click="handleDelete" variant="danger" size="sm">
                        Delete
                    </AppButton>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <div v-if="task.description" class="prose max-w-none">
                    <p class="whitespace-pre-wrap">{{ task.description }}</p>
                </div>
                <div v-else class="text-gray-500">
                    No description provided
                </div>

                <div class="flex flex-wrap gap-4">
                    <div v-if="task.due_date" class="flex items-center text-gray-600">
                        <CalendarIcon class="h-5 w-5 mr-2" />
                        <span>Due {{ formatDate(task.due_date) }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <ClockIcon class="h-5 w-5 mr-2" />
                        <span>Created {{ formatDate(task.created_at, true) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { useToast } from 'vue-toastification'
import { CalendarIcon, ClockIcon } from '@heroicons/vue/outline'
import AppLayout from '@/views/layouts/AppLayout.vue'
import AppButton from '@/components/ui/AppButton.vue'

const route = useRoute()
const router = useRouter()
const store = useStore()
const toast = useToast()

const task = ref({})
const isLoading = ref(true)

const formatDate = (dateString, relative = false) => {
    const date = new Date(dateString)
    if (relative) {
        return date.toLocaleDateString()
    }
    return date.toLocaleString()
}

onMounted(async () => {
    try {
        task.value = await store.dispatch('fetchTask', route.params.id)
    } catch (error) {
        toast.error('Failed to load task')
        router.push({ name: 'tasks' })
    } finally {
        isLoading.value = false
    }
})

const handleDelete = async () => {
    try {
        await store.dispatch('deleteTask', route.params.id)
        toast.success('Task deleted successfully')
        router.push({ name: 'tasks.index' })
    } catch (error) {
        toast.error('Failed to delete task')
    }
}
</script>