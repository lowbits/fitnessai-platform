<script setup lang="ts">
import PrimaryButton from '@/components/PrimaryButton.vue';
import { TabPanel } from '@headlessui/vue';

defineProps<{
    headline: string;
    subline: string;
}>();
</script>
<template>
    <TabPanel class="flex h-[600px] flex-col">
        <div class="animate-fade-in shrink-0">
            <p
                class="text-center font-display text-3xl font-semibold text-white"
            >
                {{ headline }}
            </p>
            <p
                class="mt-2 text-center font-display text-base leading-6 text-secondary-300"
            >
                {{ subline }}
            </p>
        </div>
        <div class="animate-scale-in mt-4 grow">
            <slot />
        </div>
        <PrimaryButton
            class="animate-scale-in mt-auto w-full shrink-0 [animation-delay:80ms]"
            @click="$emit('click:next')"
            type="button"
            >{{ $t('form_panel.submit') }}</PrimaryButton
        >
    </TabPanel>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.99);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fade-in 0.25s ease-out both;
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out both;
}
</style>
