<script setup lang="ts">
import EyebrowLabel from './EyebrowLabel.vue';

interface Props {
    eyebrow?: string;
    title: string;
    subtitle?: string;
    as?: 'h1' | 'h2';
}

withDefaults(defineProps<Props>(), {
    as: 'h2',
});
</script>

<template>
    <div class="flex flex-col">
        <EyebrowLabel v-if="eyebrow">{{ eyebrow }}</EyebrowLabel>
        <component
            :is="as"
            class="mt-[36px] text-3xl font-bold text-balance text-ink sm:text-4xl lg:text-[56px] lg:leading-[1.05]"
            :class="{ 'mt-0': !eyebrow }"
        >
            {{ title }}
        </component>
        <p
            v-if="subtitle"
            class="mt-4 max-w-2xl text-lg leading-relaxed text-ink-muted"
        >
            {{ subtitle }}
        </p>
    </div>
</template>
