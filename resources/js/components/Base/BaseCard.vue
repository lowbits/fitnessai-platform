<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    tone?: 'surface' | 'paper';
    padded?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    tone: 'surface',
    padded: true,
});

const classes = computed(() => [
    props.tone === 'paper'
        ? 'rounded-[8px] border border-paper-line bg-paper text-paper-ink'
        : 'rounded-[24px] border border-stroke bg-surface text-ink',
    props.padded ? 'p-6 sm:p-8' : '',
]);
</script>

<template>
    <div :class="classes">
        <slot />
    </div>
</template>
