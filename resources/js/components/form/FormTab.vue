<script setup lang="ts">
import { Tab } from '@headlessui/vue';
</script>

<template>
    <Tab as="template" v-slot="{ selected }">
        <div
            :class="[
                'w-full rounded-lg py-1.5 text-sm leading-5 font-medium transition-all duration-300 ease-out',
                'focus:ring-2 focus:ring-white/60 focus:ring-offset-2 focus:outline-none',
                selected
                    ? 'bg-primary-500 shadow-[0_0_8px_rgba(var(--color-primary-500),0.4)]'
                    : 'bg-dark-surfaces-25',
            ]"
        />
    </Tab>
</template>
