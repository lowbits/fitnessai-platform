<script setup lang="ts">
import { ref } from 'vue';

interface Subsection {
    heading: string;
    text: string;
}

interface Props {
    whyItWorks: Subsection[];
    initialShowCount?: number;
}

const props = withDefaults(defineProps<Props>(), {
    initialShowCount: 3,
});

const showAll = ref(false);

const toggleShowAll = () => {
    showAll.value = !showAll.value;
    // Scroll to first hidden item when expanding
    if (showAll.value && props.whyItWorks.length > props.initialShowCount) {
        setTimeout(() => {
            const firstHidden = document.getElementById(
                `why-it-works-${props.initialShowCount}`,
            );
            firstHidden?.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
            });
        }, 100);
    }
};
</script>

<template>
    <section class="py-12 sm:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h2
                class="mb-3 text-center font-display text-3xl font-bold text-white"
            >
                {{ $t('workout_plan.why_it_works.heading') }}
            </h2>
            <p
                class="mx-auto mb-10 max-w-2xl text-center text-gray-400 sm:mb-12"
            >
                {{ $t('workout_plan.why_it_works.subheading') }}
            </p>

            <div class="space-y-6">
                <article
                    v-for="(section, index) in whyItWorks"
                    :id="`why-it-works-${index}`"
                    :key="index"
                    v-show="showAll || index < initialShowCount"
                    class="relative rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-800 p-6"
                >
                    <!-- Number Badge - Subtler with primary-25 -->
                    <div
                        class="absolute -top-3 -left-3 flex h-8 w-8 items-center justify-center rounded-full border border-primary-500/20 bg-primary-25"
                    >
                        <span class="text-sm font-bold text-primary-500">{{
                            index + 1
                        }}</span>
                    </div>

                    <!-- Content -->
                    <div class="ml-3">
                        <h3 class="mb-3 text-lg font-bold text-white">
                            {{ section.heading }}
                        </h3>
                        <p class="leading-relaxed text-gray-300">
                            {{ section.text }}
                        </p>
                    </div>
                </article>
            </div>

            <!-- Simple Text Link instead of Button -->
            <div
                v-if="whyItWorks.length > initialShowCount"
                class="mt-8 text-center"
            >
                <button
                    @click="toggleShowAll"
                    class="inline-flex items-center gap-2 font-medium text-secondary-300 transition hover:text-secondary-200"
                >
                    <span v-if="!showAll">
                        {{
                            $t('workout_plan.why_it_works.show_more', {
                                count: whyItWorks.length - initialShowCount,
                            })
                        }}
                    </span>
                    <span v-else>
                        {{ $t('workout_plan.why_it_works.show_less') }}
                    </span>
                    <svg
                        class="h-4 w-4 transition-transform"
                        :class="{ 'rotate-180': showAll }"
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
            </div>
        </div>
    </section>
</template>
