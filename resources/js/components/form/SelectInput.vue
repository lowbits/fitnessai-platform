<script setup lang="ts">
interface Props {
    name?: string;
    id?: string;
    defaultLabel?: string;
}

withDefaults(defineProps<Props>(), {
    defaultLabel: 'Select',
});

const model = defineModel<string>();
</script>

<template>
    <select
        :id="id"
        :name="name"
        v-model="model"
        class="w-full rounded-xl border border-dark-surfaces-25 bg-transparent px-3 py-2 transition-colors outline-none focus:border-green-500 focus:ring-green-500 focus:outline-none disabled:text-secondary-200"
        :class="{
            'text-primary-500': model && model !== '',
            'text-secondary-200': !model || model === '',
        }"
    >
        <option value="" disabled selected hidden>
            {{ defaultLabel }}
        </option>
        <slot />
    </select>
</template>
