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

const clamp = (value: number) => {
    if (props.min !== undefined && value < props.min) return props.min;
    if (props.max !== undefined && value > props.max) return props.max;
    return value;
};

const onInput = (event: Event) => {
    const raw = (event.target as HTMLInputElement).value;
    model.value = raw === '' ? null : Number(raw);
};

const onBlur = () => {
    if (
        model.value === null ||
        model.value === undefined ||
        Number.isNaN(model.value)
    ) {
        return;
    }
    model.value = clamp(model.value);
};

const stepBy = (direction: 1 | -1) => {
    const base = model.value ?? props.min ?? 0;
    model.value = clamp(
        Math.round((base + direction * props.step) * 100) / 100,
    );
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
                class="h-12 w-full [appearance:textfield] rounded-[16px] border border-stroke bg-surface-raised px-4 text-ink tabular-nums transition-colors outline-none focus:border-brand [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                :class="suffix ? 'pr-20' : 'pr-12'"
                @input="onInput"
                @blur="onBlur"
            />

            <div
                class="absolute inset-y-0 right-2.5 flex flex-col justify-center"
            >
                <button
                    type="button"
                    tabindex="-1"
                    aria-hidden="true"
                    class="flex h-4 w-5 items-center justify-center text-ink-muted transition-colors hover:text-brand"
                    @click="stepBy(1)"
                >
                    <svg
                        width="10"
                        height="7"
                        viewBox="0 0 10 7"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M1 5.5 5 1.5l4 4" />
                    </svg>
                </button>
                <button
                    type="button"
                    tabindex="-1"
                    aria-hidden="true"
                    class="flex h-4 w-5 items-center justify-center text-ink-muted transition-colors hover:text-brand"
                    @click="stepBy(-1)"
                >
                    <svg
                        width="10"
                        height="7"
                        viewBox="0 0 10 7"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M1 1.5 5 5.5l4-4" />
                    </svg>
                </button>
            </div>

            <span
                v-if="suffix"
                class="pointer-events-none absolute inset-y-0 right-10 flex items-center text-sm font-medium text-ink-muted"
            >
                {{ suffix }}
            </span>
        </div>
    </div>
</template>
