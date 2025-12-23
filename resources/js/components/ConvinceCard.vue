<script lang="ts" setup>
defineProps<{ headline: string; body: string }>();
</script>

<template>
    <article
        class="w-full rounded-lg border border-dark-surfaces-25 bg-dark-surfaces-900 px-10 py-8 text-white"
    >
        <div
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border-2 border-dark-surfaces-200 bg-dark-surfaces-25"
        >
            <slot name="icon" />
        </div>

        <h2 class="mt-3 text-xl font-semibold text-white">{{ headline }}</h2>
        <p class="text-medium mt-3 text-base leading-6 text-secondary-100">
            {{ body }}
        </p>
    </article>
</template>
