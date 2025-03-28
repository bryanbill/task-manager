<template>
    <AuthLayout>
        <form @submit.prevent="submit" class="space-y-6 ">
            <h2 class="text-xl 2xl:text-2xl font-bold text-gray-900">Create new account</h2>

            <AppInput label="Name" v-model="form.name" required />

            <AppInput label="Email" type="email" v-model="form.email" required />

            <AppInput label="Password" type="password" v-model="form.password" required />

            <div class="flex items-center justify-between">
                <router-link :to="{ name: 'login' }" class="text-sm text-blue-600 hover:underline">
                    Already have an account?
                </router-link>
            </div>

            <AppButton type="submit" :loading="isSubmitting" class="w-full">
                Register
            </AppButton>
        </form>
    </AuthLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { useToast } from 'vue-toastification'
import AuthLayout from '@/views/layouts/AuthLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'

const router = useRouter()
const store = useStore()
const toast = useToast()


const isSubmitting = ref(false)

const form = reactive({
    name: '',
    email: '',
    password: ''
})

const submit = async (_) => {
    try {
        isSubmitting.value = true
        await store.dispatch('register', form)
        toast.success('Account created successfully')
        router.push({ name: 'tasks.index' })
    } catch (error) {
        toast.error(error.response?.data?.message || 'Registration failed')
    } finally {
        isSubmitting.value = false
    }
}
</script>