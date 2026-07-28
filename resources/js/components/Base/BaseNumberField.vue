<script setup lang="ts">
import { computed, useId } from 'vue';

interface Props {
    label: string;
    suffix?: string;
    min?: number;
    max?: number;
    step?: number;
}

const props = withDefaults(defineProps<Props>(), {
    step: 1,
});

const model = defineModel<number | null>();

const inputId = useId();

const displayValue = computed(() =>
    model.value === null || model.value === undefined
        ? ''
        : String(model.value),
);

const onInput = (event: Event) => {
    const raw = (event.target as HTMLInputElement).value;
    model.value = raw === '' ? null : Number(raw);
};

// Clamp on blur, not while typing, so intermediate values are not fought.
const onBlur = () => {
    if (
        model.value === null ||
        model.value === undefined ||
        Number.isNaN(model.value)
    ) {
        return;
    }
    if (props.min !== undefined && model.value < props.min)
        model.value = props.min;
    if (props.max !== undefined && model.value > props.max)
        model.value = props.max;
};
</script>

<template>
    <div class="flex flex-col gap-2">
        <label :for="inputId" class="text-sm font-medium text-ink-muted">{{
            label
        }}</label>
        <div class="relative">
            <input
                :id="inputId"
                :value="displayValue"
                type="number"
                inputmode="numeric"
                :min="min"
                :max="max"
                :step="step"
                class="h-12 w-full rounded-[16px] border border-stroke bg-surface-raised px-4 text-ink tabular-nums transition-colors outline-none focus:border-brand"
                :class="{ 'pr-20': suffix }"
                @input="onInput"
                @blur="onBlur"
            />
            <!-- Sits left of the native number spinners so they don't overlap. -->
            <span
                v-if="suffix"
                class="pointer-events-none absolute inset-y-0 right-7 flex items-center text-sm font-medium text-ink-muted"
            >
                {{ suffix }}
            </span>
        </div>
    </div>
</template>
