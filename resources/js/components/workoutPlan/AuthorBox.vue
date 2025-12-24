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
    <section
        class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800 p-6 sm:p-8"
    >
        <!-- Primary Author -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:gap-6"
            itemscope
            itemtype="https://schema.org/Person"
        >
            <img
                :src="author.image"
                :alt="author.name"
                itemprop="image"
                class="h-20 w-20 rounded-full border-2 border-secondary-300 object-cover sm:h-24 sm:w-24"
                @error="
                    (e) =>
                        ((e.target as HTMLImageElement).src =
                            '/assets/default-avatar.png')
                "
            />
            <div class="flex-1">
                <h3
                    class="mb-1 text-lg font-bold text-white sm:text-xl"
                    itemprop="name"
                >
                    {{ author.name }}
                </h3>
                <p
                    class="mb-2 text-sm font-medium text-secondary-200"
                    itemprop="jobTitle"
                >
                    {{ author.title }}
                </p>
                <p class="text-sm leading-relaxed text-gray-400">
                    {{ author.bio }}
                </p>
                <p class="mt-3 text-xs text-gray-500">
                    {{ $t('workout_plan.author.last_updated') }}:
                    {{ lastUpdated }}
                </p>
            </div>
        </div>

        <!-- Reviewer Attribution -->
        <div
            v-if="reviewer"
            class="mt-4 border-t border-dark-surfaces-500 pt-4"
        >
            <div class="flex items-start gap-3">
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
        <details v-if="showDisclosure" class="mt-4 text-xs text-gray-500">
            <summary class="cursor-pointer transition hover:text-gray-400">
                {{ $t('workout_plan.author.about_content') }}
            </summary>
            <p class="mt-2 leading-relaxed">
                {{ $t('workout_plan.author.disclosure') }}
            </p>
        </details>
    </section>
</template>
