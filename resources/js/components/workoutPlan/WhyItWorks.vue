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
            const firstHidden = document.getElementById(`why-it-works-${props.initialShowCount}`);
            firstHidden?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
};
</script>

<template>
    <section class="py-12 sm:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h2 class="font-display text-3xl font-bold text-white mb-3 text-center">
                {{ $t('workout_plan.why_it_works.heading') }}
            </h2>
            <p class="text-gray-400 text-center mb-10 sm:mb-12 max-w-2xl mx-auto">
                {{ $t('workout_plan.why_it_works.subheading') }}
            </p>

            <div class="space-y-6">
                <article
                    v-for="(section, index) in whyItWorks"
                    :id="`why-it-works-${index}`"
                    :key="index"
                    v-show="showAll || index < initialShowCount"
                    class="relative rounded-lg p-6 bg-dark-surfaces-800 border border-dark-surfaces-500"
                >
                    <!-- Number Badge - Subtler with primary-25 -->
                    <div class="absolute -left-3 -top-3 w-8 h-8 rounded-full bg-primary-25 flex items-center justify-center border border-primary-500/20">
                        <span class="text-sm font-bold text-primary-500">{{ index + 1 }}</span>
                    </div>

                    <!-- Content -->
                    <div class="ml-3">
                        <h3 class="text-lg font-bold text-white mb-3">
                            {{ section.heading }}
                        </h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ section.text }}
                        </p>
                    </div>
                </article>
            </div>

            <!-- Simple Text Link instead of Button -->
            <div v-if="whyItWorks.length > initialShowCount" class="mt-8 text-center">
                <button
                    @click="toggleShowAll"
                    class="inline-flex items-center gap-2 text-secondary-300 hover:text-secondary-200 font-medium transition"
                >
                    <span v-if="!showAll">
                        {{ $t('workout_plan.why_it_works.show_more', { count: whyItWorks.length - initialShowCount }) }}
                    </span>
                    <span v-else>
                        {{ $t('workout_plan.why_it_works.show_less') }}
                    </span>
                    <svg
                        class="w-4 h-4 transition-transform"
                        :class="{ 'rotate-180': showAll }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        </div>
    </section>
</template>

