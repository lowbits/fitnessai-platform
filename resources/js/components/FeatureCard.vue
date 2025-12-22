<script lang="ts" setup>
defineProps<{
    headline: string;
    body: string;
    illustrationPath: string;
    isPreview?: boolean;
}>();
</script>

<template>
    <!-- TODO: gradient 0% 1D262B -> 20% 1A2328 -> 100% 12181E-->
    <article
        class="relative w-full rounded-lg border border-dark-surfaces-25 bg-linear-to-bl from-[#1D262B] via-[#1A2328] to-dark-surfaces-900 py-10 text-white md:w-1/3"
    >
        <header class="border-b border-dark-surfaces-25">
            <img
                class="mx-auto"
                :src="illustrationPath"
                :alt="`${headline} illustration`"
            />
        </header>

        <span
            v-if="isPreview"
            class="absolute top-4 right-4 rounded-full bg-dark-surfaces-900 px-2 py-1 text-sm text-primary-300"
            >Coming soon</span
        >

        <div class="mt-10 px-10">
            <div class="flex items-center gap-2">
                <div class="inline-flex items-center justify-center">
                    <slot name="icon" />
                </div>

                <h2 class="text-2xl font-semibold">{{ headline }}</h2>
            </div>

            <p class="text-medium mt-3 text-base leading-6">{{ body }}</p>
        </div>
    </article>
</template>
