<template>
    <AppLayout>
        <div class="flex flex-col space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl font-bold text-gray-900">My Tasks</h1>
                <div class="flex space-x-3 flex-col space-y-2 md:space-y-0 md:flex-row">
                    <div class="flex flex-row items-center space-x-2">
                        <AppInput v-model="searchQuery" placeholder="Search tasks..." class="flex-1" />

                        <AppButton type="button" v-on:click="isFiltersOpen = !isFiltersOpen" variant="outline">
                            <FilterIcon class="h-5 w-5" v-if="!isFiltersOpen" />
                            <XIcon class="h-5 w-5" v-else />
                        </AppButton>

                        <AppButton v-on:click="handleSort" variant="outline" class="flex items-center space-x-2">
                            Sort
                            <ChevronUpIcon class="h-5 w-5" v-if="sortDirection === 'asc'" />
                            <ChevronDownIcon class="h-5 w-5" v-else />
                        </AppButton>
                    </div>

                    <AppButton v-on:click="router.push({ name: 'tasks.create' })" class="whitespace-nowrap">
                        Add Task
                    </AppButton>


                </div>
            </div>

            <TransitionExpand>
                <div v-if="isFiltersOpen" class="bg-white p-4 rounded-lg shadow">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <AppDatePicker label="Due Date From" v-model="filters.due_date_from" />
                        <AppDatePicker label="Due Date To" v-model="filters.due_date_to" />
                        <div class="flex items-end">
                            <AppButton @click="resetFilters" variant="outline" class="w-full">
                                Reset
                            </AppButton>
                        </div>
                    </div>
                </div>
            </TransitionExpand>

            <div v-if="isLoading" class="flex justify-center py-8">
                <AppLoader />
            </div>

            <div v-else-if="tasks.length === 0" class="text-center py-12">
                <p class="text-gray-500">No tasks found</p>
            </div>

            <div v-else class="grid grid-cols-1 gap-4">
                <TaskCard v-for="task in tasks" :key="task.id" :task="task" v-on:delete="handleDeleteTask" />
            </div>

            <div v-if="tasks.length > 0" class="flex justify-center">
                <Pagination :links="pagination.links" :onPageChanged="handlePageChanged" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useStore } from 'vuex'
import { useToast } from 'vue-toastification'
import { useRouter } from 'vue-router'
import { useDebounceFn } from '@vueuse/core'
import AppLayout from '@/views/layouts/AppLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppDatePicker from '@/components/ui/AppDatePicker.vue'
import AppLoader from '@/components/ui/AppLoader.vue'
import TaskCard from '@/components/TaskCard.vue'
import Pagination from '@/components/ui/Pagination.vue'
import TransitionExpand from '@/components/ui/TransitionExpand.vue'
import { FilterIcon, XIcon, ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/outline'

const store = useStore()
const toast = useToast()
const router = useRouter()

const isFiltersOpen = ref(false)
const isLoading = ref(false)
const searchQuery = ref('')
const sortDirection = ref('asc')

const filters = ref({
    due_date_from: null,
    due_date_to: null
})

const tasks = computed(() => store.state.tasks)

const pagination = computed(() => store.state.pagination);

const fetchTasks = useDebounceFn(async () => {
    try {
        isLoading.value = true

        await store.dispatch('fetchTasks', {
            page: pagination.currentPage,
            search: searchQuery.value,
            due_date_from: filters.value.due_date_from,
            due_date_to: filters.value.due_date_to,
            sort: sortDirection.value
        })
    } catch (error) {
        toast.error('Failed to fetch tasks')
    } finally {
        isLoading.value = false
    }
}, 500)

// Watch for changes
watch([searchQuery, filters, sortDirection], () => {
    pagination.currentPage = 1
    fetchTasks()
}, { deep: true })

// Initial fetch
fetchTasks()

const handlePageChanged = (index) => {
    // set page query
    pagination.currentPage = index
    fetchTasks()
}

const resetFilters = () => {
    filters.value = {
        due_date_from: null,
        due_date_to: null
    }
    sortDirection.value = 'asc'
}

const handleDeleteTask = async (taskId) => {
    try {
        await store.dispatch('deleteTask', taskId)
        toast.success('Task deleted successfully')
        fetchTasks()
    } catch (error) {
        toast.error('Failed to delete task')
    }
}

const handleSort = () => {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    fetchTasks()
}
</script>