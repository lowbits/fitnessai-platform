<script setup lang="ts">
import GenerateFitnessPlanForm from '@/components/GenerateFitnessPlanForm.vue';
import CTASection from '@/components/workoutPlan/CTASection.vue';
import { ref } from 'vue';

const showForm = ref(false);

const openForm = () => {
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
};
</script>
<template>
    <slot v-bind="{ open: openForm }">
        <CTASection @open-form="openForm" />
    </slot>
    <Teleport to="body">
        <div
            v-if="showForm"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
            @click.self="closeForm"
        >
            <div
                class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-dark-surfaces-800 p-6 shadow-2xl"
            >
                <button
                    @click="closeForm"
                    class="absolute top-4 right-4 text-gray-400 transition hover:text-white"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
                <GenerateFitnessPlanForm
                    :total-days="28"
                    @success="closeForm"
                />
            </div>
        </div>
    </Teleport>
</template>
