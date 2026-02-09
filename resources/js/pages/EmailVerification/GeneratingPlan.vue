<script setup lang="ts">
import { Button } from '@/components/ui/button';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { usePoll } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';

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
    iosAppStoreUrl: string;
    setPasswordUrl: string;
}

const props = defineProps<Props>();

const { t, locale } = useI18n();

// Reviews rotation using translations
const reviews = computed(() => [
    {
        quote: t('generatingPlan.reviews.review1.quote'),
        author: t('generatingPlan.reviews.review1.author'),
    },
    {
        quote: t('generatingPlan.reviews.review2.quote'),
        author: t('generatingPlan.reviews.review2.author'),
    },
    {
        quote: t('generatingPlan.reviews.review3.quote'),
        author: t('generatingPlan.reviews.review3.author'),
    },
    {
        quote: t('generatingPlan.reviews.review4.quote'),
        author: t('generatingPlan.reviews.review4.author'),
    },
]);

const currentReviewIndex = ref(0);
let reviewInterval: ReturnType<typeof setInterval> | null = null;

// Check if plan generation is complete
const isComplete = computed(() => props.status?.is_complete ?? false);

// ETA countdown
const etaSeconds = ref(240); // 4 minutes
let etaInterval: ReturnType<typeof setInterval> | null = null;

const etaText = computed(() => {
    if (isComplete.value) {
        return '';
    }
    if (etaSeconds.value > 60) {
        const minutes = Math.ceil(etaSeconds.value / 60);
        return t('generatingPlan.eta.minutes', { count: minutes });
    } else if (etaSeconds.value > 0) {
        return t('generatingPlan.eta.lessThanMinute');
    } else {
        return t('generatingPlan.eta.almostDone');
    }
});

// Progress status text
const progressText = computed(() => {
    if (isComplete.value) {
        return t('generatingPlan.progress.completed');
    }
    // Show meals progress first, then workouts
    if (props.status?.meal_plans && props.status.meal_plans.pending > 0) {
        return t('generatingPlan.progress.meals');
    }
    return t('generatingPlan.progress.workouts');
});

// OS Detection
const isIOS = ref(false);
const isAndroid = ref(false);

const detectOS = () => {
    const ua = navigator.userAgent;
    isIOS.value = /iPhone|iPad|iPod/.test(ua);
    isAndroid.value = /Android/.test(ua);
};

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

// Track events with Plausible
const trackEvent = (eventName: string, eventProps?: Record<string, any>) => {
    if (window.plausible) {
        window.plausible(eventName, { props: eventProps });
    }
};

const handleStoreClick = (store: 'app_store' | 'play_store') => {
    trackEvent('Store Click', { store, source: 'loading-page' });
};

const handlePasswordClick = () => {
    trackEvent('Set Password Click', { source: 'loading-page' });

    const url = new URL(props.setPasswordUrl);
    window.location.href = `fytrr://${url.pathname.slice(1)}${url.search}`;
};

onMounted(() => {
    // Detect OS
    detectOS();

    // Start review rotation
    reviewInterval = setInterval(() => {
        currentReviewIndex.value =
            (currentReviewIndex.value + 1) % reviews.value.length;
    }, 4000);

    // Start ETA countdown
    etaInterval = setInterval(() => {
        if (etaSeconds.value > 30) {
            etaSeconds.value -= 1;
        }
    }, 1000);
});

onUnmounted(() => {
    if (reviewInterval) clearInterval(reviewInterval);
    if (etaInterval) clearInterval(etaInterval);
});
</script>

<template>
    <GuestLayout>
        <div class="container mx-auto max-w-lg px-5 pt-7 pb-12">
            <!-- 1. HEADER -->
            <div class="header animate-fade-in mb-6 text-center">
                <div
                    :class="[
                        'live-badge mb-4 inline-flex items-center gap-2 rounded-full border px-3.5 py-1.5 text-xs font-semibold',
                        isComplete
                            ? 'border-green-500/20 bg-green-500/10 text-green-400'
                            : 'border-primary-500/20 bg-primary-500/10 text-primary-400',
                    ]"
                >
                    <span
                        v-if="!isComplete"
                        class="live-dot animate-pulse-dot h-1.5 w-1.5 rounded-full bg-primary-400"
                    ></span>
                    <span v-else class="text-sm">✓</span>
                    {{
                        isComplete
                            ? $t('generatingPlan.badge.completed')
                            : $t('generatingPlan.badge.generating')
                    }}
                </div>
                <h1
                    class="mb-2 text-2xl leading-tight font-extrabold tracking-tight text-white"
                >
                    {{ $t('generatingPlan.header.title') }}<br /><span
                        class="text-primary-400"
                        >{{ $t('generatingPlan.header.titleHighlight') }}</span
                    >
                </h1>
                <p class="text-sm leading-relaxed text-gray-400">
                    {{ $t('generatingPlan.header.description') }}
                </p>
            </div>

            <!-- 2. PROGRESS -->
            <div
                :class="[
                    'progress animate-fade-in-delay-1 mb-7 rounded-xl border p-3.5',
                    isComplete
                        ? 'border-green-500/30 bg-green-500/5'
                        : 'border-gray-700 bg-gray-800',
                ]"
            >
                <div class="mb-2.5 flex gap-1">
                    <div
                        class="h-1 flex-1 overflow-hidden rounded-full bg-primary-400 shadow-[0_0_8px_rgba(74,222,128,0.25)]"
                    ></div>
                    <div
                        class="h-1 flex-1 overflow-hidden rounded-full bg-primary-400 shadow-[0_0_8px_rgba(74,222,128,0.25)]"
                    ></div>
                    <div
                        :class="[
                            'h-1 flex-1 overflow-hidden rounded-full',
                            isComplete
                                ? 'bg-primary-400 shadow-[0_0_8px_rgba(74,222,128,0.25)]'
                                : 'animate-fill-pulse bg-gray-700',
                        ]"
                    ></div>
                    <div
                        :class="[
                            'h-1 flex-1 overflow-hidden rounded-full',
                            isComplete
                                ? 'bg-primary-400 shadow-[0_0_8px_rgba(74,222,128,0.25)]'
                                : 'bg-gray-700/50',
                        ]"
                    ></div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-medium">
                        <div
                            v-if="!isComplete"
                            class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-primary-400/15 border-t-primary-400"
                        ></div>
                        <span v-else class="text-green-400">✓</span>
                        {{ progressText }}
                    </div>
                    <div v-if="!isComplete" class="text-xs text-gray-500">
                        {{ etaText }}
                    </div>
                </div>
            </div>

            <!-- 3. COMPARISON -->
            <div class="animate-fade-in-delay-2 mb-7">
                <div
                    class="section-title mb-3.5 text-center text-[11px] font-bold tracking-widest text-gray-500 uppercase"
                >
                    {{ $t('generatingPlan.comparison.title') }}
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <!-- PDF Column -->
                    <div
                        class="col rounded-2xl border border-gray-700/50 bg-gray-800 p-3 text-center"
                    >
                        <div class="mb-2 text-3xl">📄</div>
                        <div class="mb-3 text-sm font-bold text-gray-400">
                            {{ $t('generatingPlan.comparison.pdf.title') }}
                        </div>
                        <div class="flex flex-col gap-1.5 text-left">
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-gray-500"
                                    >✓</span
                                >
                                <span class="text-gray-500">{{
                                    $t(
                                        'generatingPlan.comparison.pdf.sevenDayTraining',
                                    )
                                }}</span>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-gray-500"
                                    >✓</span
                                >
                                <span class="text-gray-500">{{
                                    $t(
                                        'generatingPlan.comparison.pdf.sevenDayNutrition',
                                    )
                                }}</span>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-red-500/50"
                                    >✗</span
                                >
                                <span class="text-gray-500">{{
                                    $t(
                                        'generatingPlan.comparison.pdf.noAdaptation',
                                    )
                                }}</span>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-red-500/50"
                                    >✗</span
                                >
                                <span class="text-gray-500">{{
                                    $t(
                                        'generatingPlan.comparison.pdf.noTracking',
                                    )
                                }}</span>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-red-500/50"
                                    >✗</span
                                >
                                <span class="text-gray-500">{{
                                    $t(
                                        'generatingPlan.comparison.pdf.noAiCoach',
                                    )
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- App Column -->
                    <div
                        class="col relative rounded-2xl border-[1.5px] border-primary-500/30 bg-linear-to-bl from-primary-500/10 to-primary-500/5 p-3 text-center"
                    >
                        <div
                            class="absolute top-[-9px] left-1/2 -translate-x-1/2 rounded-lg bg-linear-to-r from-primary-500 to-primary-400 px-2.5 py-0.5 text-[9px] font-extrabold tracking-wide whitespace-nowrap text-black uppercase"
                        >
                            {{ $t('generatingPlan.comparison.app.freeTrial') }}
                        </div>
                        <div class="mb-2 text-3xl">📱</div>
                        <div class="mb-3 text-sm font-bold text-white">
                            {{ $t('generatingPlan.comparison.app.title') }}
                        </div>
                        <div class="flex flex-col gap-1.5 text-left">
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-primary-400"
                                    >✓</span
                                >
                                <span class="text-gray-300">{{
                                    $t(
                                        'generatingPlan.comparison.app.everythingFromPdf',
                                    )
                                }}</span>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-primary-400"
                                    >✓</span
                                >
                                <span class="text-gray-300">{{
                                    $t(
                                        'generatingPlan.comparison.app.newPlansWeekly',
                                    )
                                }}</span>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-primary-400"
                                    >✓</span
                                >
                                <span class="text-gray-300">{{
                                    $t('generatingPlan.comparison.app.aiCoach')
                                }}</span>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-primary-400"
                                    >✓</span
                                >
                                <span class="text-gray-300">{{
                                    $t(
                                        'generatingPlan.comparison.app.workoutTracking',
                                    )
                                }}</span>
                            </div>
                            <div class="flex items-start gap-1.5 text-[11px]">
                                <span class="mt-0.5 text-xs text-primary-400"
                                    >✓</span
                                >
                                <span class="text-gray-300">{{
                                    $t(
                                        'generatingPlan.comparison.app.mealTracking',
                                    )
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. MONA DEMO -->
            <div
                class="animate-fade-in-delay-3 mb-5 rounded-2xl border border-gray-700 bg-gray-800 p-4"
            >
                <div
                    class="mb-3 text-[11px] font-bold tracking-wide text-primary-400 uppercase"
                >
                    {{ $t('generatingPlan.monaDemo.title') }}
                </div>
                <div class="chat flex flex-col gap-2">
                    <div
                        class="msg-user max-w-[86%] self-end rounded-2xl rounded-br-sm border border-primary-500/12 bg-primary-500/10 px-3.5 py-2.5 text-xs"
                    >
                        {{ $t('generatingPlan.monaDemo.userMessage') }}
                    </div>
                    <div
                        class="msg-ai max-w-[86%] self-start rounded-2xl rounded-bl-sm border border-gray-700/50 bg-white/5 px-3.5 py-2.5 text-xs"
                    >
                        <div
                            class="msg-ai-name mb-1 text-[11px] font-bold text-primary-400"
                        >
                            Mona
                        </div>
                        <div class="text-gray-400">
                            {{ $t('generatingPlan.monaDemo.monaResponse') }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center text-xs font-medium text-gray-500">
                    {{ $t('generatingPlan.monaDemo.footer') }}
                </div>
            </div>

            <!-- 5. SOCIAL PROOF -->
            <div
                class="animate-fade-in-delay-4 mb-7 flex items-center gap-3.5 rounded-xl border border-gray-700 bg-gray-800 p-3.5"
            >
                <div class="shrink-0 text-center">
                    <div class="mb-0.5 text-xs tracking-wide text-yellow-400">
                        ★★★★★
                    </div>
                    <div class="text-[11px] font-medium text-gray-500">
                        5 / 5
                    </div>
                </div>
                <div class="h-8 w-px shrink-0 bg-gray-700"></div>
                <div class="relative min-h-12 flex-1">
                    <transition name="fade" mode="out-in">
                        <div :key="currentReviewIndex" class="review">
                            <q
                                class="block text-xs leading-relaxed text-gray-400 italic"
                                >{{ reviews[currentReviewIndex].quote }}</q
                            >
                            <cite
                                class="mt-1 block text-[11px] font-semibold text-gray-500 not-italic"
                                >—
                                {{ reviews[currentReviewIndex].author }}</cite
                            >
                        </div>
                    </transition>
                </div>
            </div>

            <!-- 6. CTA ZONE -->
            <div class="cta-zone animate-fade-in-delay-5 mb-5">
                <!-- Price -->
                <div
                    class="mb-3.5 rounded-xl border border-gray-700 bg-gray-800 p-3.5 text-center"
                >
                    <div class="mb-1 text-lg font-extrabold text-primary-400">
                        {{ $t('generatingPlan.cta.price.title') }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $t('generatingPlan.cta.price.subtitle') }}
                    </div>
                </div>

                <!-- Step 1: Download App -->
                <div
                    class="mb-2.5 rounded-2xl border border-gray-700 bg-gray-800 p-4"
                >
                    <div class="mb-3.5 flex items-center gap-3">
                        <div
                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-linear-to-br from-primary-500 to-primary-400 text-xs font-extrabold text-black"
                        >
                            1
                        </div>
                        <div class="text-sm font-bold text-white">
                            {{ $t('generatingPlan.cta.step1.title') }}
                        </div>
                    </div>

                    <!-- Mobile: iOS -->
                    <a
                        v-if="isIOS"
                        :href="iosAppStoreUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-block transition-opacity hover:opacity-80"
                        @click="handleStoreClick('app_store')"
                    >
                        <img
                            :src="`/assets/badges/App_Store_Badge_${locale.toUpperCase()}.svg`"
                            alt="Download on App Store"
                            width="120"
                            height="40"
                            class="h-10 w-auto"
                        />
                    </a>

                    <!-- Mobile: Android (coming soon) -->
                    <div
                        v-else-if="isAndroid"
                        class="inline-flex items-center gap-2.5 rounded-xl border border-gray-700 bg-gray-900 px-3 py-2.5 text-white opacity-50"
                    >
                        <svg
                            class="h-5 w-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path
                                d="M3.609 1.814L13.792 12 3.61 22.186a.996.996 0 01-.61-.92V2.734a1 1 0 01.609-.92zm10.89 10.893l2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.199l2.807 1.626a1 1 0 010 1.732l-2.807 1.627L15.206 12l2.492-2.492zM5.864 2.658L16.8 8.99l-2.302 2.302-8.634-8.634z"
                            />
                        </svg>
                        <div class="text-left">
                            <div class="text-[9px] leading-none text-gray-500">
                                {{ $t('generatingPlan.cta.step1.comingSoon') }}
                            </div>
                            <div class="text-xs leading-tight font-bold">
                                Google Play
                            </div>
                        </div>
                    </div>

                    <!-- Desktop: QR Code + App Store badge -->
                    <div v-else class="flex items-center justify-center gap-6">
                        <!-- QR Code -->
                        <div class="flex flex-col items-center">
                            <div
                                class="rounded-xl border border-gray-600 bg-white p-2"
                            >
                                <img
                                    src="/assets/download-on-app-store-qr-code.png"
                                    alt="QR Code - App Store"
                                    width="400"
                                    height="400"
                                    class="h-28 w-28"
                                />
                            </div>
                            <span class="mt-1.5 text-[10px] text-gray-500">{{
                                $t('generatingPlan.cta.step1.scanQr')
                            }}</span>
                        </div>

                        <!-- Divider -->
                        <div class="flex flex-col items-center gap-1">
                            <div class="h-6 w-px bg-gray-700"></div>
                            <span class="text-[10px] text-gray-500">{{
                                $t('generatingPlan.cta.step1.or')
                            }}</span>
                            <div class="h-6 w-px bg-gray-700"></div>
                        </div>

                        <!-- App Store Badge -->
                        <a
                            :href="iosAppStoreUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="transition-opacity hover:opacity-80"
                            @click="handleStoreClick('app_store')"
                        >
                            <img
                                :src="`/assets/badges/App_Store_Badge_${locale.toUpperCase()}.svg`"
                                alt="Download on App Store"
                                width="120"
                                height="40"
                                class="h-10 w-auto"
                            />
                        </a>
                    </div>
                </div>

                <!-- Step 2: Set Password -->
                <div class="rounded-2xl border border-gray-700 bg-gray-800 p-4">
                    <div class="mb-3.5 flex items-center gap-3">
                        <div
                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-linear-to-br from-primary-500 to-primary-400 text-xs font-extrabold text-black"
                        >
                            2
                        </div>
                        <div class="text-sm font-bold text-white">
                            {{ $t('generatingPlan.cta.step2.title') }}
                        </div>
                    </div>

                    <Button
                        variant="default"
                        class="w-full"
                        @click.prevent="handlePasswordClick"
                    >
                        {{ $t('generatingPlan.cta.step2.button') }}
                    </Button>

                    <div class="mt-1.5 text-center text-[11px] text-gray-500">
                        {{ $t('generatingPlan.cta.step2.hint') }}
                    </div>
                </div>
            </div>

            <!-- 7. PDF FALLBACK -->
            <div
                class="fallback animate-fade-in-delay-6 border-t border-gray-800/50 pt-5 text-center"
            >
                <p class="mb-1 text-xs text-gray-400">
                    {{ $t('generatingPlan.fallback.title') }}
                </p>
                <small class="block text-[11px] leading-relaxed text-gray-500">
                    {{ $t('generatingPlan.fallback.description') }}
                </small>
            </div>

            <div class="animate-fade-in-delay-7 mt-4 text-center">
                <a href="#" class="text-[11px] text-gray-500 underline">{{
                    $t('generatingPlan.closePage')
                }}</a>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Pulse animation for the live dot */
.animate-pulse-dot {
    animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.4);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(74, 222, 128, 0);
    }
}

/* Fill pulse animation for progress bar */
.animate-fill-pulse {
    background: linear-gradient(
        to right,
        rgb(74 222 128) 50%,
        rgb(55 65 81) 50%
    );
    background-size: 200% 100%;
    animation: fill-pulse 2.5s ease-in-out infinite;
}

@keyframes fill-pulse {
    0%,
    100% {
        background-position: 100% 0;
    }
    50% {
        background-position: 0 0;
    }
}

/* Fade in animations */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease both;
}

.animate-fade-in-delay-1 {
    animation: fade-in 0.5s ease both 0.08s;
}

.animate-fade-in-delay-2 {
    animation: fade-in 0.5s ease both 0.18s;
}

.animate-fade-in-delay-3 {
    animation: fade-in 0.5s ease both 0.28s;
}

.animate-fade-in-delay-4 {
    animation: fade-in 0.5s ease both 0.38s;
}

.animate-fade-in-delay-5 {
    animation: fade-in 0.5s ease both 0.48s;
}

.animate-fade-in-delay-6 {
    animation: fade-in 0.5s ease both 0.58s;
}

.animate-fade-in-delay-7 {
    animation: fade-in 0.5s ease both 0.68s;
}

/* Vue Transition classes for reviews */
:deep(.fade-enter-active),
:deep(.fade-leave-active) {
    transition: opacity 0.5s ease;
}

:deep(.fade-enter-from),
:deep(.fade-leave-to) {
    opacity: 0;
}

/* Background gradient effect */
body::before {
    content: '';
    position: fixed;
    top: -40%;
    right: -30%;
    width: 500px;
    height: 500px;
    background: radial-gradient(
        circle,
        rgba(74, 222, 128, 0.05) 0%,
        transparent 70%
    );
    pointer-events: none;
    z-index: -1;
}
</style>
