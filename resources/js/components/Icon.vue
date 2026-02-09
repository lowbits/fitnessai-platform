<script setup lang="ts">
import { type IconName, iconRegistry } from '@/constants/icons';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
    name: IconName;
    class?: string | object;
    size?: number;
    color?: string;
    strokeWidth?: number | string;
}

const props = withDefaults(defineProps<Props>(), {
    class: '',
    size: 16,
    strokeWidth: 2,
});

const className = computed(() => cn('h-4 w-4', props.class));

const icon = computed(() => iconRegistry[props.name]);
</script>

<template>
    <component
        :is="icon"
        :class="className"
        :size="size"
        :stroke-width="strokeWidth"
        :color="color"
    />
</template>
