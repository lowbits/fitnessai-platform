<script setup lang="ts">
import PrimaryButton from '@/components/PrimaryButton.vue';
import { TabPanel } from '@headlessui/vue';

defineProps<{
    headline: string
    subline: string
}>()
</script>
<template>
    <TabPanel class="flex min-h-[500px] flex-col">
        <div class="flex-shrink-0">
            <h4 class="text-center font-display text-3xl font-semibold">
                {{ headline }}
            </h4>
            <p class="mt-2 text-center font-display text-base leading-6 text-secondary-300">
                {{subline}}
            </p>
        </div>
        <div class="mt-8 flex-grow">
            <slot />
        </div>
        <PrimaryButton
            class="mt-10 w-full flex-shrink-0"
            @click="$emit('click:next')"
            type="button"
            >{{ $t('form_panel.submit') }}</PrimaryButton
        >
    </TabPanel>
</template>
