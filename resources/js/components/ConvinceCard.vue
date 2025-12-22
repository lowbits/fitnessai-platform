<script lang="ts" setup>
defineProps<{ headline: string, body: string }>()
</script>

<template>
    <article class="text-white bg-dark-surfaces-900 border border-dark-surfaces-25 px-10 py-8 rounded-lg">
        <div
            class="w-10 h-10 rounded-full inline-flex items-center justify-center bg-dark-surfaces-25 border-2 border-dark-surfaces-200">
            <slot name="icon"/>
        </div>

        <h2 class="mt-3 text-xl text-white font-semibold">{{ headline }}</h2>
        <p class="mt-3 text-base text-secondary-100 leading-6 text-medium">{{
                body
            }}</p>
    </article>
</template>
