<template>
    <AuthLayout>
        <form @submit.prevent="handleLogin" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900">Sign in to your account</h2>

            <AppInput label="Email" type="email" v-model="form.email" required :errors="v$.email.$errors" />

            <AppInput label="Password" type="password" v-model="form.password" required :errors="v$.password.$errors" />

            <div class="flex items-center justify-between">
                <router-link :to="{ name: 'register' }" class="text-sm text-blue-600 hover:underline">
                    Don't have an account?
                </router-link>
            </div>

            <AppButton type="submit" :loading="isSubmitting" class="w-full">
                Sign in
            </AppButton>
        </form>
    </AuthLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { useToast } from 'vue-toastification'
import { useVuelidate } from '@vuelidate/core'
import { required, email } from '@vuelidate/validators'
import AuthLayout from '@/views/layouts/AuthLayout.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'

const router = useRouter()
const store = useStore()
const toast = useToast()

const isSubmitting = ref(false)

const form = reactive({
    email: '',
    password: ''
})

const rules = {
    email: { required, email },
    password: { required }
}

const v$ = useVuelidate(rules, form)

const handleLogin = async (ev) => {
    ev.preventDefault()
    const isValid = await v$.value.$validate()
    if (!isValid) return

    try {
        isSubmitting.value = true
        await store.dispatch('login', form)
        toast.success('Logged in successfully')
        router.push({ name: 'tasks' })
    } catch (error) {
        toast.error(error.response?.data?.message || 'Login failed')
    } finally {
        isSubmitting.value = false
    }
}
</script>