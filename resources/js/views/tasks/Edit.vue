<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Task</h1>

            <form @submit.prevent="handleSubmitTask" class="space-y-6">
                <AppInput label="Title" v-model="form.title" :error="errors.title" required />

                <AppTextArea label="Description" v-model="form.description" :error="errors.description" :rows="4" />

                <AppDatePicker label="Due Date" v-model="form.due_date" :error="errors.due_date" />

                <div class="flex space-x-4">
                    <AppButton type="button" variant="outline" @click="router.go(-1)" class="flex-1">
                        Cancel
                    </AppButton>
                    <AppButton type="submit" :loading="isSubmitting" class="flex-1">
                        Update Task
                    </AppButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { useToast } from 'vue-toastification'

import AppLayout from '@/views/layouts/AppLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppTextArea from '@/components/ui/AppTextArea.vue'
import AppDatePicker from '@/components/ui/AppDatePicker.vue'
import AppButton from '@/components/ui/AppButton.vue'

const route = useRoute()
const router = useRouter()
const store = useStore()
const toast = useToast()


const isSubmitting = ref(false)
const isLoading = ref(true)

const form = reactive({
    title: '',
    description: '',
    due_date: null
})

const errors = reactive({
    title: '',
    description: '',
    due_date: ''
})

const validateForm = () => {
    errors.title = form.title ? '' : 'Title is required'
    errors.description = form.description ? '' : 'Description is required'
    errors.due_date = form.due_date ? '' : 'Due date is required'
    return Object.values(errors).every((error) => !error)
}

onMounted(async () => {
    try {
        const task = await store.dispatch('fetchTask', route.params.id)
        form.title = task.title
        form.description = task.description
        form.due_date = task.due_date ? new Date(task.due_date) : null
    } catch (error) {
        toast.error('Failed to load task')
        router.push({ name: 'tasks' })
    } finally {
        isLoading.value = false
    }
})

const handleSubmitTask = (async (_) => {
    try {
        isSubmitting.value = true
        if (!validateForm()) return
        await store.dispatch('updateTask', {
            id: route.params.id,
            ...form
        })
        toast.success('Task updated successfully')
        router.push({ name: 'tasks.index' })
    } catch (error) {
        console.log(error)
        toast.error(error.response?.data?.message || 'Failed to update task')
    } finally {
        isSubmitting.value = false
    }
})
</script>