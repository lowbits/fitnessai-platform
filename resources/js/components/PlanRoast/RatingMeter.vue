<script setup lang="ts">
import { computed } from 'vue';

type Rating = 'good' | 'ok' | 'poor';

interface Props {
    rating: Rating;
    label: string;
}

const props = defineProps<Props>();

const level: Record<Rating, number> = { poor: 1, ok: 2, good: 3 };
const fillClass: Record<Rating, string> = {
    poor: 'bg-red-400',
    ok: 'bg-amber-400',
    good: 'bg-emerald-400',
};

const bars = [
    { index: 1, height: 'h-2' },
    { index: 2, height: 'h-3' },
    { index: 3, height: 'h-4' },
];

const filled = computed(() => level[props.rating]);
const color = computed(() => fillClass[props.rating]);
</script>

<template>
    <span
        class="flex shrink-0 items-end gap-1"
        role="img"
        :aria-label="label"
        :title="label"
    >
        <span
            v-for="bar in bars"
            :key="bar.index"
            class="w-1.5 rounded-sm transition-colors"
            :class="[bar.height, bar.index <= filled ? color : 'bg-ink/15']"
        />
    </span>
</template>
