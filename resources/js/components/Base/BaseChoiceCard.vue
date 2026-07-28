<script setup lang="ts" generic="T extends string">
interface Props {
    value: T;
    title: string;
    description?: string;
}

const props = defineProps<Props>();
const model = defineModel<T | null>({ required: true });

const checked = () => model.value === props.value;
</script>

<template>
    <button
        type="button"
        role="radio"
        :aria-checked="checked()"
        class="flex w-full items-center justify-between gap-4 rounded-[16px] border p-4 text-left transition-colors focus-visible:ring-2 focus-visible:ring-brand focus-visible:outline-none"
        :class="
            checked()
                ? 'border-brand bg-brand/10'
                : 'border-stroke bg-surface-raised hover:border-ink-muted'
        "
        @click="model = value"
    >
        <span class="flex flex-col">
            <span class="font-semibold text-ink">{{ title }}</span>
            <span v-if="description" class="mt-0.5 text-sm text-ink-muted">{{
                description
            }}</span>
        </span>
        <span
            aria-hidden="true"
            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition-colors"
            :class="checked() ? 'border-brand bg-brand' : 'border-stroke'"
        >
            <span v-if="checked()" class="h-2 w-2 rounded-full bg-on-brand" />
        </span>
    </button>
</template>
