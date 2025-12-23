<script setup lang="ts">
import { usePoll } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';

interface Props {
    user: {
        id: number;
        name: string;
        email: string;
    };
    plan: {
        id: number;
        name: string;
        start_date: string;
        workouts_per_week: number;
    };
    status: {
        status: 'generating' | 'completed' | 'partial_failure';
        overall_progress: number;
        meal_plans: {
            total: number;
            generated: number;
            failed: number;
            pending: number;
            progress: number;
        };
        workout_plans: {
            total: number;
            generated: number;
            failed: number;
            pending: number;
            progress: number;
        };
        is_complete: boolean;
        has_failures: boolean;
    } | null;
}

const props = defineProps<Props>();

// Use Inertia's usePoll for automatic polling
const { stop: stopPolling } = usePoll(3000, {
    onBefore() {
        // Stop polling if complete
        if (props.status?.is_complete) {
            stopPolling();
            return false; // Cancel this poll request
        }
    },
});
</script>

<template>
    <GuestLayout>
        <div class="mx-auto mt-10 max-w-2xl">
            <!-- Success Header -->
            <div class="mb-8 text-center">
                <div
                    class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-green-500"
                >
                    <svg
                        class="h-10 w-10 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        ></path>
                    </svg>
                </div>
                <h1 class="mb-2 text-4xl font-bold text-white">
                    {{ $t('emailVerification.generating.verified') }}
                </h1>
                <p class="text-xl text-gray-300">{{ $t('emailVerification.generating.welcome', { name: user.name }) }}</p>
            </div>


            <!-- Progress Card -->
            <div
                class="rounded-2xl border border-gray-700 bg-gray-800 p-8 shadow-2xl"
            >
                <div class="mb-8 text-center">
                    <h2 class="mb-2 text-2xl font-semibold text-white">
                        {{ $t('emailVerification.generating.title') }}
                    </h2>
                    <p class="text-gray-400">
                        {{ $t('emailVerification.generating.description') }}
                    </p>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="mt-8 text-center">
                    <div
                        v-if="status.is_complete"
                        class="text-lg font-medium text-green-400"
                    >
                        {{ $t('emailVerification.generating.complete') }}
                    </div>
                    <div
                        v-else-if="status.has_failures"
                        class="font-medium text-yellow-400"
                    >
                        {{ $t('emailVerification.generating.hasFailures') }}
                    </div>
                    <div v-else class="text-gray-400">
                        <div
                            class="mr-2 inline-block h-5 w-5 animate-spin rounded-full border-b-2 border-primary-500"
                        ></div>
                        {{ $t('emailVerification.generating.generating') }}
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="!status" class="py-8 text-center">
                    <div
                        class="inline-block h-12 w-12 animate-spin rounded-full border-b-2 border-primary-500"
                    ></div>
                    <p class="mt-4 text-gray-400">{{ $t('emailVerification.generating.loadingStatus') }}</p>
                </div>
            </div>

            <!-- Plan Details -->
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>
                    {{ $t('emailVerification.generating.planLabel') }} <span class="text-gray-300">{{ plan.name }}</span>
                </p>
                <p>
                    {{ $t('emailVerification.generating.startDateLabel') }}
                    <span class="text-gray-300">{{ plan.start_date }}</span>
                </p>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
