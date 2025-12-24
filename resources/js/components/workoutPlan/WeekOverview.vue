<script setup lang="ts">
import { ref } from 'vue';

interface Exercise {
    name: string;
    sets: number;
    reps: string;
    rest: string;
    notes: string;
}

interface WorkoutDay {
    day: string;
    focus: string;
    exercises: Exercise[];
}

interface Props {
    schedule: WorkoutDay[];
    progression: string;
    tips: string[];
    equipment: string[];
}

defineProps<Props>();
const expandedDay = ref<number | null>(0);

const toggleDay = (index: number) => {
    expandedDay.value = expandedDay.value === index ? null : index;
};
</script>

<template>
    <div>
        <h2 class="font-display text-3xl font-bold text-white">{{ $t('workout_plan.week_overview.heading') }}</h2>
        <p class="mt-2 text-gray-400">{{ $t('workout_plan.week_overview.heading') }}</p>

        <!-- Equipment -->
        <div class="mt-6 flex flex-wrap gap-2">
            <span class="rounded-lg bg-primary-500/10 px-3 py-1 text-sm font-medium text-primary-300">
                {{ $t('workout_plan.week_overview.equipment_heading') }}:
            </span>
            <span
                v-for="item in equipment"
                :key="item"
                class="rounded-lg bg-dark-surfaces-500/50 px-3 py-1 text-sm text-gray-300"
            >
                {{ item }}
            </span>
        </div>

        <!-- Workout Days -->
        <div class="mt-10 space-y-4">
            <div
                v-for="(workout, index) in schedule"
                :key="index"
                class="overflow-hidden rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800"
            >
                <button
                    @click="toggleDay(index)"
                    class="flex w-full items-center justify-between p-6 text-left transition hover:bg-dark-surfaces-500/30"
                >
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ workout.day }}</h3>
                        <p class="mt-1 text-sm text-gray-400">{{ workout.focus }}</p>
                    </div>
                    <svg
                        class="h-6 w-6 text-primary-300 transition-transform"
                        :class="{ 'rotate-180': expandedDay === index }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    v-if="expandedDay === index"
                    class="border-t border-dark-surfaces-500 p-6"
                >
                    <div class="space-y-4">
                        <div
                            v-for="(exercise, exIndex) in workout.exercises"
                            :key="exIndex"
                            class="flex items-start gap-4 rounded-lg bg-dark-surfaces-900/50 p-4"
                        >
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-primary-500 text-sm font-bold text-dark-surfaces-900">
                                {{ exIndex + 1 }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-white">{{ exercise.name }}</h4>
                                <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-400">
                                    <span>{{ exercise.sets }} {{ $t('workout_plan.week_overview.sets') }}</span>
                                    <span>•</span>
                                    <span>{{ exercise.reps }} {{ $t('workout_plan.week_overview.reps') }}</span>
                                    <span>•</span>
                                    <span>{{ exercise.rest }} {{ $t('workout_plan.week_overview.rest') }}</span>
                                </div>
                                <p v-if="exercise.notes" class="mt-2 text-sm text-gray-500">{{ exercise.notes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression -->
        <div class="mt-10 rounded-xl bg-primary-500/10 p-6">
            <h3 class="text-lg font-bold text-primary-300">{{ $t('workout_plan.week_overview.progression_heading') }}</h3>
            <p class="mt-2 text-gray-300">{{ progression }}</p>
        </div>

        <!-- Tips -->
        <div class="mt-6 rounded-xl bg-dark-surfaces-800 p-6">
            <h3 class="text-lg font-bold text-white">{{ $t('workout_plan.week_overview.tips_heading') }}</h3>
            <ul class="mt-4 space-y-2">
                <li
                    v-for="(tip, index) in tips"
                    :key="index"
                    class="flex items-start gap-3 text-gray-300"
                >
                    <svg class="mt-1 h-5 w-5 flex-shrink-0 text-primary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ tip }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
