<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <h1 class="text-xl 2xl:text-2xl font-bold text-gray-900 mb-6">Create New Task</h1>

            <form @submit.prevent="handleSubmitTask" class="space-y-6">
                <AppInput label="Title" v-model="form.title" :error="errors.title" required />

                <AppTextArea label="Description" v-model="form.description" :error="errors.description" :rows="4" />

                <AppDatePicker label="Due Date" v-model="form.due_date" :error="errors.due_date" />

                <div class="flex space-x-4">
                    <AppButton type="button" variant="outline" @click="router.go(-1)" class="flex-1">
                        Cancel
                    </AppButton>
                    <AppButton type="submit" :loading="isSubmitting" class="flex-1">
                        Create Task
                    </AppButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { useToast } from 'vue-toastification'
import AppLayout from '@/views/layouts/AppLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppTextArea from '@/components/ui/AppTextArea.vue'
import AppDatePicker from '@/components/ui/AppDatePicker.vue'
import AppButton from '@/components/ui/AppButton.vue'

const router = useRouter()
const store = useStore()
const toast = useToast()


const isSubmitting = ref(false)

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
    errors.title = form.title.trim() ? '' : 'Title is required'
    errors.description = form.description ? '' : 'Description is required'
    errors.due_date = form.due_date ? '' : 'Due date is required'
    return Object.values(errors).every((error) => !error)
}

const handleSubmitTask = (async (_) => {
    try {
        isSubmitting.value = true
        if (!validateForm()) return
        await store.dispatch('createTask', form)
        toast.success('Task created successfully')
        router.push({ name: 'tasks.index' })
    } catch (error) {
        console.log(error)
        toast.error(error.response?.data?.message || 'Failed to create task')
    } finally {
        isSubmitting.value = false
    }
})


</script>