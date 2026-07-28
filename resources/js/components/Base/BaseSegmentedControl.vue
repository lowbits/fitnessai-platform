<script setup lang="ts" generic="T extends string">
import { computed } from 'vue';

interface Option {
    value: T;
    label: string;
}

interface Props {
    options: Option[];
    ariaLabel: string;
}

const props = defineProps<Props>();
const model = defineModel<T | null>({ required: true });

const selectedIndex = computed(() =>
    props.options.findIndex((o) => o.value === model.value),
);

// When nothing is selected yet, the first radio is the tab stop.
const tabStop = computed(() =>
    selectedIndex.value < 0 ? 0 : selectedIndex.value,
);

const select = (value: T) => {
    model.value = value;
};

// Arrow-key navigation across the radios, wrapping at the ends.
const onKeydown = (event: KeyboardEvent, index: number) => {
    const { key } = event;
    if (!['ArrowRight', 'ArrowDown', 'ArrowLeft', 'ArrowUp'].includes(key))
        return;
    event.preventDefault();

    const forward = key === 'ArrowRight' || key === 'ArrowDown';
    const next =
        (index + (forward ? 1 : -1) + props.options.length) %
        props.options.length;
    model.value = props.options[next].value;

    const group = event.currentTarget as HTMLElement;
    const buttons =
        group.parentElement?.querySelectorAll<HTMLButtonElement>(
            '[role="radio"]',
        );
    buttons?.[next]?.focus();
};
</script>

<template>
    <div
        role="radiogroup"
        :aria-label="ariaLabel"
        class="inline-flex w-full gap-2 rounded-[16px] border border-stroke bg-surface-raised p-1"
    >
        <button
            v-for="(option, index) in options"
            :key="option.value"
            type="button"
            role="radio"
            :aria-checked="model === option.value"
            :tabindex="index === tabStop ? 0 : -1"
            class="flex-1 rounded-[12px] px-4 py-2.5 text-sm font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-brand focus-visible:outline-none"
            :class="
                model === option.value
                    ? 'bg-brand text-on-brand'
                    : 'text-ink-muted hover:text-ink'
            "
            @click="select(option.value)"
            @keydown="onKeydown($event, index)"
        >
            {{ option.label }}
        </button>
    </div>
</template>
