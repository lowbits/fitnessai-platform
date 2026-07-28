<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    /** 0..1 */
    value: number;
    /** Any CSS color, typically a macro token via var(). */
    color: string;
}

const props = defineProps<Props>();

const width = computed(() => `${Math.max(0, Math.min(1, props.value)) * 100}%`);
</script>

<template>
    <div
        aria-hidden="true"
        class="h-2 w-full overflow-hidden rounded-full bg-paper-line"
    >
        <div
            class="h-full rounded-full transition-[width] duration-300"
            :style="{ width, backgroundColor: color }"
        />
    </div>
</template>
