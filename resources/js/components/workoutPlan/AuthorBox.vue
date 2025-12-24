<script setup lang="ts">
interface Author {
    name: string;
    title: string;
    bio: string;
    image: string;
}

interface Reviewer {
    name: string;
    title: string;
}

interface Props {
    author: Author;
    reviewer?: Reviewer;
    lastUpdated: string;
    showDisclosure?: boolean;
}

withDefaults(defineProps<Props>(), {
    showDisclosure: true,
});
</script>

<template>
    <section class="bg-dark-surfaces-800 border border-dark-surfaces-500 rounded-xl p-6 sm:p-8">
        <!-- Primary Author -->
        <div
            class="flex flex-col sm:flex-row gap-4 sm:gap-6"
            itemscope
            itemtype="https://schema.org/Person"
        >
            <img
                :src="author.image"
                :alt="author.name"
                itemprop="image"
                class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-2 border-secondary-300"
                @error="(e) => (e.target as HTMLImageElement).src = '/assets/default-avatar.png'"
            />
            <div class="flex-1">
                <h3
                    class="text-lg sm:text-xl font-bold text-white mb-1"
                    itemprop="name"
                >
                    {{ author.name }}
                </h3>
                <p
                    class="text-sm text-secondary-200 font-medium mb-2"
                    itemprop="jobTitle"
                >
                    {{ author.title }}
                </p>
                <p class="text-sm text-gray-400 leading-relaxed">
                    {{ author.bio }}
                </p>
                <p class="text-xs text-gray-500 mt-3">
                    {{ $t('workout_plan.author.last_updated') }}: {{ lastUpdated }}
                </p>
            </div>
        </div>

        <!-- Reviewer Attribution -->
        <div
            v-if="reviewer"
            class="mt-4 pt-4 border-t border-dark-surfaces-500"
        >
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-primary-300 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-white">
                        {{ $t('workout_plan.author.reviewed_by') }}:
                    </p>
                    <p class="text-sm text-gray-400">
                        {{ reviewer.name }}, {{ reviewer.title }}
                    </p>
                </div>
            </div>
        </div>

        <!-- AI Disclosure -->
        <details
            v-if="showDisclosure"
            class="mt-4 text-xs text-gray-500"
        >
            <summary class="cursor-pointer hover:text-gray-400 transition">
                {{ $t('workout_plan.author.about_content') }}
            </summary>
            <p class="mt-2 leading-relaxed">
                {{ $t('workout_plan.author.disclosure') }}
            </p>
        </details>
    </section>
</template>

