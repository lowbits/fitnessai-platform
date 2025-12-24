<script setup lang="ts">
import { ref } from 'vue';

interface Mistake {
    title: string;
    problem: string;
    consequence: string;
    solution: string;
    example: string;
}

interface Props {
    mistakes: Mistake[];
    initialShowCount?: number;
}

const props = withDefaults(defineProps<Props>(), {
    initialShowCount: 3,
});

const expandedIndex = ref<number>(0);
const showAll = ref(false);

const toggle = (index: number) => {
    expandedIndex.value = expandedIndex.value === index ? -1 : index;
};

const toggleShowAll = () => {
    showAll.value = !showAll.value;
    // Scroll to first hidden item when expanding
    if (showAll.value && props.mistakes.length > props.initialShowCount) {
        setTimeout(() => {
            const firstHidden = document.getElementById(`mistake-${props.initialShowCount}`);
            firstHidden?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
};
</script>

<template>
    <section class="bg-dark-surfaces-800 py-12 sm:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h2 class="font-display text-3xl font-bold text-white mb-4 text-center">
                {{ $t('workout_plan.common_mistakes.heading') }}
            </h2>
            <p class="text-gray-400 text-center mb-8 sm:mb-12">
                {{ $t('workout_plan.common_mistakes.subheading') }}
            </p>

            <div class="space-y-4">
                <article
                    v-for="(mistake, index) in mistakes"
                    :id="`mistake-${index}`"
                    :key="index"
                    v-show="showAll || index < initialShowCount"
                    class="border border-dark-surfaces-500 rounded-xl overflow-hidden transition-all hover:border-primary-500/50"
                    itemscope
                    itemtype="https://schema.org/HowToSection"
                >
                    <!-- Header - Always visible -->
                    <button
                        @click="toggle(index)"
                        class="w-full flex items-center justify-between p-6 text-left transition-colors hover:bg-dark-surfaces-500/30"
                        :aria-expanded="expandedIndex === index"
                        :aria-controls="`mistake-content-${index}`"
                    >
                        <div class="flex items-center gap-4 flex-1">
                            <!-- Number Badge -->
                            <div
                                class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg"
                                :class="
                                    expandedIndex === index
                                        ? 'bg-primary-500 text-dark-surfaces-900'
                                        : 'bg-dark-surfaces-500 text-gray-400'
                                "
                            >
                                {{ index + 1 }}
                            </div>

                            <!-- Title -->
                            <h3
                                class="text-lg font-bold transition-colors"
                                :class="
                                    expandedIndex === index
                                        ? 'text-primary-300'
                                        : 'text-white'
                                "
                                itemprop="name"
                            >
                                {{ mistake.title }}
                            </h3>
                        </div>

                        <!-- Chevron -->
                        <svg
                            class="w-6 h-6 text-gray-400 transition-transform shrink-0"
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

                    <!-- Expandable Content -->
                    <div
                        v-show="expandedIndex === index"
                        :id="`mistake-content-${index}`"
                        class="px-6 pb-6"
                        itemprop="itemListElement"
                        itemscope
                        itemtype="https://schema.org/HowToStep"
                    >
                        <!-- Problem -->
                        <div class="mb-4">
                            <div class="flex items-start gap-2 mb-2">
                                <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h4 class="font-semibold text-white">{{ $t('workout_plan.common_mistakes.problem') }}:</h4>
                            </div>
                            <p class="text-gray-300 leading-relaxed ml-7">
                                {{ mistake.problem }}
                            </p>
                        </div>

                        <!-- Consequence -->
                        <div class="mb-4">
                            <div class="flex items-start gap-2 mb-2">
                                <svg class="w-5 h-5 text-orange-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h4 class="font-semibold text-white">{{ $t('workout_plan.common_mistakes.consequence') }}:</h4>
                            </div>
                            <p class="text-gray-300 leading-relaxed ml-7">
                                {{ mistake.consequence }}
                            </p>
                        </div>

                        <!-- Solution -->
                        <div class="mb-4">
                            <div class="flex items-start gap-2 mb-2">
                                <svg class="w-5 h-5 text-primary-300 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h4 class="font-semibold text-white">{{ $t('workout_plan.common_mistakes.solution') }}:</h4>
                            </div>
                            <p class="text-gray-300 leading-relaxed ml-7" itemprop="text">
                                {{ mistake.solution }}
                            </p>
                        </div>

                        <!-- Example -->
                        <div class="mt-4 bg-primary-500/10 border border-primary-500/20 rounded-lg p-4">
                            <p class="text-xs font-semibold text-primary-300 mb-2">
                                {{ $t('workout_plan.common_mistakes.example') }}:
                            </p>
                            <p class="text-sm text-gray-300 leading-relaxed">
                                {{ mistake.example }}
                            </p>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Simple Text Link instead of Button -->
            <div v-if="mistakes.length > initialShowCount" class="mt-8 text-center">
                <button
                    @click="toggleShowAll"
                    class="inline-flex items-center gap-2 text-secondary-300 hover:text-secondary-200 font-medium transition"
                >
                    <span v-if="!showAll">
                        {{ $t('workout_plan.common_mistakes.show_more', { count: mistakes.length - initialShowCount }) }}
                    </span>
                    <span v-else>
                        {{ $t('workout_plan.common_mistakes.show_less') }}
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

