<script setup lang="ts">
import {Tab} from "@headlessui/vue";
</script>

<template>
    <Tab
        as="template"
        v-slot="{ selected }"
    >
        <div
            :class="[
              'w-full rounded-lg py-1.5 text-sm font-medium leading-5 text-blue-700',
              'ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
              selected
                ? 'bg-primary-500'
                : 'bg-dark-surfaces-25',
            ]"
        />
    </Tab>
</template>
