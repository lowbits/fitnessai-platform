<script setup lang="ts">
interface Plan {
    type: string;
    title: string;
    description: string;
    url: string;
}

interface Props {
    plans: Plan[];
}

defineProps<Props>();
</script>

<template>
    <section class="px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <h2 class="font-display text-3xl font-bold text-white">{{ $t('workout_plan.related.heading') }}</h2>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <a
                    v-for="plan in plans"
                    :key="plan.type"
                    :href="plan.url"
                    class="group overflow-hidden rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800 transition hover:border-primary-500"
                >
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white group-hover:text-primary-300 transition">
                            {{ plan.title }}
                        </h3>
                        <p class="mt-3 text-gray-400 leading-relaxed">
                            {{ plan.description }}
                        </p>
                        <div class="mt-4 flex items-center gap-2 text-primary-300 font-semibold">
                            <span>{{ $t('workout_plan.related.view_plan') }}</span>
                            <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
</template>

