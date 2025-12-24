<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label: string;
    hint?: string;
    name: string;
    errors?: [] | string;
    fullWidth?: boolean;
}

const props = defineProps<Props>();
const hasError = computed(() => props.errors?.length);
</script>
<template>
    <div
        class="relative flex flex-col text-primary-25"
        :class="{ 'flex-1': fullWidth }"
    >
        <label :for="name" class="mb-2">{{ label }}</label>
        <slot :hasError="hasError" />

        <span v-if="hint" class="mt-1 text-sm leading-6 text-gray-600">{{
            hint
        }}</span>
        <span
            class="absolute -bottom-4 text-xs leading-none text-red-400"
            v-if="hasError"
            >{{ props.errors }}</span
        >
    </div>
</template>
