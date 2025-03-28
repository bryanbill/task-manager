<template>
    <div class="h-screen overflow-hidden bg-gray-50 flex flex-col justify-center items-center">
        <main>
            <div class="max-w-md mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-10">
                    <div class="sm:mx-auto sm:w-full sm:max-w-md">
                        <h2 class="text-center text-2xl font-bold ">
                            <slot name="title" />
                        </h2>
                        <p v-if="$slots.subtitle" class="mt-2 text-center text-sm text-gray-600">
                            <slot name="subtitle" />
                        </p>
                    </div>

                    <div class="mt-8">
                        <slot />
                    </div>

                    <div v-if="$slots.footer" class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300" />
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 text-gray-500">
                                    <slot name="footer" />
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>