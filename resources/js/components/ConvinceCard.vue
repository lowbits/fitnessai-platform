<script lang="ts" setup>
import RoundedIcon from '@/components/ui/icons/RoundedIcon.vue';

defineProps<{ headline: string; body: string }>();
</script>

<template>
    <article
        class="w-full rounded-lg border border-dark-surfaces-25 bg-dark-surfaces-900 px-10 py-8 text-white"
    >
        <RoundedIcon>
            <slot name="icon" />
        </RoundedIcon>

        <h2 class="mt-3 text-xl font-semibold text-white">{{ headline }}</h2>
        <p class="text-medium mt-3 text-base leading-6 text-secondary-100">
            {{ body }}
        </p>
    </article>
</template>
