<script setup lang="ts">
import { ref } from 'vue';

interface FAQ {
    question: string;
    answer: string;
}

interface Props {
    faqs: FAQ[];
}

defineProps<Props>();
const expandedIndex = ref<number | null>(null);

const toggle = (index: number) => {
    expandedIndex.value = expandedIndex.value === index ? null : index;
};
</script>

<template>
    <section class="bg-dark-surfaces-800 px-4 py-16 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <h2 class="font-display text-3xl font-bold text-white">
                {{ $t('workout_plan.faq.heading') }}
            </h2>

            <div class="mt-10 space-y-4">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="overflow-hidden rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900"
                >
                    <button
                        @click="toggle(index)"
                        class="flex w-full items-start justify-between p-6 text-left transition hover:bg-dark-surfaces-500/30"
                        :aria-expanded="expandedIndex === index"
                        :aria-controls="`faq-answer-${index}`"
                    >
                        <span class="pr-6 text-lg font-semibold text-white">{{
                            faq.question
                        }}</span>
                        <svg
                            class="mt-1 h-6 w-6 shrink-0 text-primary-300 transition-transform"
                            :class="{ 'rotate-180': expandedIndex === index }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <div
                        v-show="expandedIndex === index"
                        class="border-t border-dark-surfaces-500 p-6"
                    >
                        <p class="leading-relaxed text-gray-300">
                            {{ faq.answer }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

/ https://fytrr.com/de 2026-01-23T17:34:14+00:00 weekly 1.0
https://fytrr.com/de/kostenloser-trainingsplan 2025-12-24T00:00:00+00:00 weekly
0.9 https://fytrr.com/de/kostenloser-trainingsplan/abnehmen
2025-12-24T00:00:00+00:00 monthly 0.8
https://fytrr.com/de/kostenloser-trainingsplan/muskelaufbau
2025-12-24T00:00:00+00:00 monthly 0.8
https://fytrr.com/de/kostenloser-trainingsplan/anfaenger
2025-12-24T00:00:00+00:00 monthly 0.8
https://fytrr.com/de/kostenloser-trainingsplan/zuhause 2025-12-24T00:00:00+00:00
monthly 0.8 https://fytrr.com/de/kostenloser-trainingsplan/frauen
2025-12-24T00:00:00+00:00 monthly 0.8
https://fytrr.com/de/kostenloser-trainingsplan/neujahrs-trainingsplan
2025-12-24T00:00:00+00:00 monthly 0.8 https://fytrr.com/de/agb
2025-12-23T17:34:14+00:00 yearly 0.3 https://fytrr.com/de/datenschutz
2025-12-23T17:34:14+00:00 yearly 0.3 https://fytrr.com/en
2026-01-23T17:34:14+00:00 weekly 1.0 https://fytrr.com/en/free-workout-plan
2025-12-24T00:00:00+00:00 weekly 0.9
https://fytrr.com/en/free-workout-plan/weight-loss 2025-12-24T00:00:00+00:00
monthly 0.8 https://fytrr.com/en/free-workout-plan/muscle-gain
2025-12-24T00:00:00+00:00 monthly 0.8
https://fytrr.com/en/free-workout-plan/beginner 2025-12-24T00:00:00+00:00
monthly 0.8 https://fytrr.com/en/free-workout-plan/home
2025-12-24T00:00:00+00:00 monthly 0.8
https://fytrr.com/en/free-workout-plan/women 2025-12-24T00:00:00+00:00 monthly
0.8 https://fytrr.com/en/free-workout-plan/new-year-reset
2025-12-24T00:00:00+00:00 monthly 0.8 https://fytrr.com/en/terms-and-conditions
2025-12-23T17:34:14+00:00 yearly 0.3 https://fytrr.com/en/privacy-policy
2025-12-23T17:34:14+00:00 yearly 0.3
