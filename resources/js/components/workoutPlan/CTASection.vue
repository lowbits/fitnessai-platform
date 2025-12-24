<script setup lang="ts">
const emit = defineEmits<{
    openForm: [];
}>();


</script>

<template>
    <section class="bg-linear-to-b from-dark-surfaces-900 to-dark-surfaces-800 px-4 py-20 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">
                {{ $t('workout_plan.cta.heading') }}
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-300">
                {{ $t('workout_plan.cta.description') }}
            </p>

            <button
                @click="emit('openForm')"
                class="mt-8 inline-flex items-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-lg font-semibold text-white transition hover:bg-primary-400"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                {{ $t('workout_plan.cta.button') }}
            </button>
        </div>
    </section>
</template>
