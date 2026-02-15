<script setup lang="ts">
import PrimaryButton from '@/components/PrimaryButton.vue';
import { TabPanel } from '@headlessui/vue';

defineProps<{
    headline: string;
    subline: string;
}>();
</script>
<template>
    <TabPanel class="flex min-h-[500px] flex-col">
        <div class="shrink-0">
            <p class="text-center text-white font-display text-3xl font-semibold">
                {{ headline }}
            </p>
            <p
                class="mt-2 text-center font-display text-base leading-6 text-secondary-300"
            >
                {{ subline }}
            </p>
        </div>
        <div class="mt-8 grow">
            <slot />
        </div>
        <PrimaryButton
            class="mt-10 w-full shrink-0"
            @click="$emit('click:next')"
            type="button"
            >{{ $t('form_panel.submit') }}</PrimaryButton
        >
    </TabPanel>
</template>
