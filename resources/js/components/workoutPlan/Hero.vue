<script setup lang="ts">
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';

interface Props {
    title: string;
    intro: string;
    workoutDetails: {
        weeks: number;
        workoutsPerWeek: number;
        durationMinutes: number;
        level: string;
    };
}

defineProps<Props>();
defineEmits<{
    openForm: [];
}>();
</script>

<template>
    <section
        class="relative overflow-hidden bg-gradient-to-b from-dark-surfaces-800 to-dark-surfaces-900 px-4 py-20 sm:px-6 lg:px-8"
    >
        <!-- Background Gradient Image -->
        <div
            class="absolute inset-0 flex items-center justify-center opacity-30"
        >
            <img
                src="/assets/gradient.png"
                alt="Abstract gradient background"
                class="h-full w-full object-cover"
                loading="lazy"
                decoding="async"
            />
        </div>

        <div class="relative mx-auto max-w-4xl text-center">
            <!-- Badge -->
            <div
                class="mb-6 inline-flex items-center gap-2 rounded-full bg-primary-500/10 px-4 py-2 text-sm font-semibold text-primary-300"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
                {{ $t('workout_plan.hero.free_badge') }}
            </div>

            <!-- H1 Title -->
            <h1
                class="font-display text-4xl leading-tight font-bold text-white sm:text-5xl lg:text-6xl"
            >
                {{ title }}
            </h1>

            <!-- Intro Text -->
            <p
                class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-gray-300 sm:text-xl"
            >
                {{ intro }}
            </p>

            <!-- Workout Details Grid -->
            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl bg-dark-surfaces-500/30 p-4">
                    <div class="text-3xl font-bold text-primary-300">
                        {{ workoutDetails.weeks }}
                    </div>
                    <div class="mt-1 text-sm text-gray-400">
                        {{ $t('workout_plan.hero.weeks') }}
                    </div>
                </div>
                <div class="rounded-xl bg-dark-surfaces-500/30 p-4">
                    <div class="text-3xl font-bold text-primary-300">
                        {{ workoutDetails.workoutsPerWeek }}
                    </div>
                    <div class="mt-1 text-sm text-gray-400">
                        {{ $t('workout_plan.hero.times_per_week') }}
                    </div>
                </div>
                <div class="rounded-xl bg-dark-surfaces-500/30 p-4">
                    <div class="text-3xl font-bold text-primary-300">
                        {{ workoutDetails.durationMinutes }}
                    </div>
                    <div class="mt-1 text-sm text-gray-400">
                        {{ $t('workout_plan.hero.minutes') }}
                    </div>
                </div>
                <div class="rounded-xl bg-dark-surfaces-500/30 p-4">
                    <div class="text-sm font-medium text-primary-300">
                        {{ workoutDetails.level }}
                    </div>
                    <div class="mt-1 text-sm text-gray-400">
                        {{ $t('workout_plan.hero.level') }}
                    </div>
                </div>
            </div>
            <GenerateFitnessPlanModal #default="{ open }">
                <!-- CTA Button -->
                <button
                    @click="open"
                    class="mt-10 inline-flex items-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-lg font-semibold text-white transition hover:bg-primary-400"
                >
                    {{ $t('workout_plan.hero.cta_button') }}
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3"
                        />
                    </svg>
                </button>
            </GenerateFitnessPlanModal>
        </div>
    </section>
</template>
