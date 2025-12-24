<script setup lang="ts">
import { Tab } from '@headlessui/vue';
</script>

<template>
    <Tab as="template" v-slot="{ selected }">
        <div
            :class="[
                'w-full rounded-lg py-1.5 text-sm leading-5 font-medium text-blue-700',
                'ring-opacity-60 ring-white ring-offset-2 ring-offset-blue-400 focus:ring-2 focus:outline-none',
                selected ? 'bg-primary-500' : 'bg-dark-surfaces-25',
            ]"
        />
    </Tab>
</template>
