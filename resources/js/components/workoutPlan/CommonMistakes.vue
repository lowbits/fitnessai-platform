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
            const firstHidden = document.getElementById(
                `mistake-${props.initialShowCount}`,
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
    <section class="bg-dark-surfaces-800 py-12 sm:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h2
                class="mb-4 text-center font-display text-3xl font-bold text-white"
            >
                {{ $t('workout_plan.common_mistakes.heading') }}
            </h2>
            <p class="mb-8 text-center text-gray-400 sm:mb-12">
                {{ $t('workout_plan.common_mistakes.subheading') }}
            </p>

            <div class="space-y-4">
                <article
                    v-for="(mistake, index) in mistakes"
                    :id="`mistake-${index}`"
                    :key="index"
                    v-show="showAll || index < initialShowCount"
                    class="overflow-hidden rounded-xl border border-dark-surfaces-500 transition-all hover:border-primary-500/50"
                    itemscope
                    itemtype="https://schema.org/HowToSection"
                >
                    <!-- Header - Always visible -->
                    <button
                        @click="toggle(index)"
                        class="flex w-full items-center justify-between p-6 text-left transition-colors hover:bg-dark-surfaces-500/30"
                        :aria-expanded="expandedIndex === index"
                        :aria-controls="`mistake-content-${index}`"
                    >
                        <div class="flex flex-1 items-center gap-4">
                            <!-- Number Badge -->
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-lg font-bold"
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
                            class="h-6 w-6 shrink-0 text-gray-400 transition-transform"
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
                            <div class="mb-2 flex items-start gap-2">
                                <svg
                                    class="mt-0.5 h-5 w-5 shrink-0 text-red-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    />
                                </svg>
                                <h4 class="font-semibold text-white">
                                    {{
                                        $t(
                                            'workout_plan.common_mistakes.problem',
                                        )
                                    }}:
                                </h4>
                            </div>
                            <p class="ml-7 leading-relaxed text-gray-300">
                                {{ mistake.problem }}
                            </p>
                        </div>

                        <!-- Consequence -->
                        <div class="mb-4">
                            <div class="mb-2 flex items-start gap-2">
                                <svg
                                    class="mt-0.5 h-5 w-5 shrink-0 text-orange-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <h4 class="font-semibold text-white">
                                    {{
                                        $t(
                                            'workout_plan.common_mistakes.consequence',
                                        )
                                    }}:
                                </h4>
                            </div>
                            <p class="ml-7 leading-relaxed text-gray-300">
                                {{ mistake.consequence }}
                            </p>
                        </div>

                        <!-- Solution -->
                        <div class="mb-4">
                            <div class="mb-2 flex items-start gap-2">
                                <svg
                                    class="mt-0.5 h-5 w-5 shrink-0 text-primary-300"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <h4 class="font-semibold text-white">
                                    {{
                                        $t(
                                            'workout_plan.common_mistakes.solution',
                                        )
                                    }}:
                                </h4>
                            </div>
                            <p
                                class="ml-7 leading-relaxed text-gray-300"
                                itemprop="text"
                            >
                                {{ mistake.solution }}
                            </p>
                        </div>

                        <!-- Example -->
                        <div
                            class="mt-4 rounded-lg border border-primary-500/20 bg-primary-500/10 p-4"
                        >
                            <p
                                class="mb-2 text-xs font-semibold text-primary-300"
                            >
                                {{
                                    $t('workout_plan.common_mistakes.example')
                                }}:
                            </p>
                            <p class="text-sm leading-relaxed text-gray-300">
                                {{ mistake.example }}
                            </p>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Simple Text Link instead of Button -->
            <div
                v-if="mistakes.length > initialShowCount"
                class="mt-8 text-center"
            >
                <button
                    @click="toggleShowAll"
                    class="inline-flex items-center gap-2 font-medium text-secondary-300 transition hover:text-secondary-200"
                >
                    <span v-if="!showAll">
                        {{
                            $t('workout_plan.common_mistakes.show_more', {
                                count: mistakes.length - initialShowCount,
                            })
                        }}
                    </span>
                    <span v-else>
                        {{ $t('workout_plan.common_mistakes.show_less') }}
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
