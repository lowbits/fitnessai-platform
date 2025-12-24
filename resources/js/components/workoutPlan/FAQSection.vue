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
    <section
        class="bg-dark-surfaces-800 px-4 py-16 sm:px-6 lg:px-8"
        itemscope
        itemtype="https://schema.org/FAQPage"
    >
        <div class="mx-auto max-w-3xl">
            <h2 class="font-display text-3xl font-bold text-white">
                {{ $t('workout_plan.faq.heading') }}
            </h2>

            <div class="mt-10 space-y-4">
                <div
                    v-for="(faq, index) in faqs"
                    :key="index"
                    class="overflow-hidden rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900"
                    itemscope
                    itemprop="mainEntity"
                    itemtype="https://schema.org/Question"
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
                            class="mt-1 h-6 w-6 flex-shrink-0 text-primary-300 transition-transform"
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
                        itemscope
                        itemprop="acceptedAnswer"
                        itemtype="https://schema.org/Answer"
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
