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
            class="w-full bg-transparent px-3 py-2  border border-dark-surfaces-25 outline-none focus:outline-none focus:ring-green-500 transition-colors focus:border-green-500 rounded-xl placeholder:text-secondary-200/20 text-green-600"
            :class="{'pr-14': suffix}"
            type="number"
            v-bind="$attrs"
        >
        <div
            v-if="suffix"
            class="border-l border-dark-surfaces-25 rounded-r-xl inset-y-0 px-3 text-center inline-flex items-center absolute right-0 top-0 text-primary-200 font-semibold">
            {{ suffix }}
        </div>
    </div>
</template>
