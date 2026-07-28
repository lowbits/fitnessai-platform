<script setup lang="ts">
import BaseProgressBar from '@/components/Base/BaseProgressBar.vue';
import type { MacroPortion } from '@/composables/useMacroCalculator';

interface Props {
    label: string;
    portion: MacroPortion;
    color: string;
}

defineProps<Props>();
</script>

<template>
    <div>
        <div class="flex items-baseline justify-between">
            <span class="flex items-center gap-2 font-semibold text-paper-ink">
                <span
                    class="h-2.5 w-2.5 rounded-full"
                    :style="{ backgroundColor: color }"
                />
                {{ label }}
            </span>
            <span class="text-paper-ink tabular-nums">
                <span class="text-lg font-bold">{{ portion.grams }}</span> g
            </span>
        </div>
        <div class="mt-2">
            <BaseProgressBar :value="portion.share" :color="color" />
        </div>
        <div class="mt-1.5 flex justify-between text-sm text-paper-soft">
            <span class="tabular-nums">{{ portion.kcal }} kcal</span>
            <span class="tabular-nums"
                >{{ Math.round(portion.share * 100) }} %</span
            >
        </div>
    </div>
</template>
