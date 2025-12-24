<script setup lang="ts">
interface Props {
    suffix?: string;
    name?: string;
}

defineProps<Props>();

const model = defineModel<string>();
</script>

<template>
    <div class="relative">
        <input
            v-model="model"
            :name="name"
            required
            class="w-full rounded-xl border border-dark-surfaces-25 bg-transparent px-3 py-2 text-green-600 transition-colors outline-none placeholder:text-secondary-200/20 focus:border-green-500 focus:ring-green-500 focus:outline-none"
            :class="{ 'pr-14': suffix }"
            type="number"
            v-bind="$attrs"
        />
        <div
            v-if="suffix"
            class="absolute inset-y-0 top-0 right-0 inline-flex items-center rounded-r-xl border-l border-dark-surfaces-25 px-3 text-center font-semibold text-primary-200"
        >
            {{ suffix }}
        </div>
    </div>
</template>
