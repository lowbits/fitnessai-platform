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
        class="w-full border px-3 py-2 bg-transparent border-dark-surfaces-25 outline-none focus:outline-none focus:ring-green-500 transition-colors disabled:text-secondary-200 focus:border-green-500 rounded-xl"
        :class="{
            'text-primary-500': model && model !== '',
            'text-secondary-200': !model || model === ''
        }"
    >
        <option value="" disabled selected hidden>
            {{ defaultLabel }}
        </option>
        <slot />
    </select>
</template>
