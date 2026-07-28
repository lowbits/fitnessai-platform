<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'primary' | 'ghost';
    size?: 'md' | 'lg';
    as?: 'button' | 'a';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
    as: 'button',
});

const classes = computed(() => [
    'inline-flex items-center justify-center gap-2 rounded-[30px] font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2 focus-visible:ring-offset-canvas disabled:cursor-not-allowed disabled:opacity-50',
    props.size === 'lg' ? 'h-14 px-8 text-base' : 'h-11 px-6 text-sm',
    props.variant === 'primary'
        ? 'bg-brand text-on-brand hover:bg-brand/90'
        : 'border border-stroke bg-transparent text-ink hover:bg-surface-raised',
]);
</script>

<template>
    <component :is="as" :class="classes">
        <slot />
    </component>
</template>
