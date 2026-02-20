<script setup lang="ts">
import { Button } from '@/components/ui/button';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, usePoll } from '@inertiajs/vue3';
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
        status: 'generating' | 'completed';
        is_complete: boolean;
    } | null;
    bodyGoal: string | null;
    smartLink: string;
    setPasswordUrl: string;
    iosAppStoreUrl: string;
}

const props = defineProps<Props>();

const { t } = useI18n();

// Reviews (static, stacked)
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
]);

// Check if plan generation is complete
const isComplete = computed(() => props.status?.is_complete ?? false);

// Body goal label for display
const bodyGoalLabel = computed(() => {
    if (!props.bodyGoal) {
        return '';
    }
    return t('enums.bodyGoal.' + props.bodyGoal);
});

// Fake progress step: 0 = analyzing goals, 1 = building workouts, 2 = creating nutrition
const currentStep = ref(0);
let stepTimeout1: ReturnType<typeof setTimeout> | null = null;
let stepTimeout2: ReturnType<typeof setTimeout> | null = null;

// ETA countdown
const etaSeconds = ref(240);
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

// Use Inertia's usePoll for automatic polling
const { stop: stopPolling } = usePoll(3000, {
    only: ['status'],
    onBefore() {
        if (props.status?.is_complete) {
            stopPolling();
            return false;
        }
    },
});

// Track events with Plausible
const trackEvent = (eventName: string, eventProps?: Record<string, any>) => {
    if (window.plausible) {
        window.plausible(eventName, { props: eventProps });
    }
};

const handleSmartLinkClick = () => {
    trackEvent('Smart Link Click', {
        source: 'loading-page',
        phase: isComplete.value ? 'complete' : 'generating',
    });
};

onMounted(() => {
    // Fake step progression: 0→1 at 15s, 1→2 at 75s
    stepTimeout1 = setTimeout(() => {
        currentStep.value = 1;
    }, 15_000);
    stepTimeout2 = setTimeout(() => {
        currentStep.value = 2;
    }, 75_000);

    // ETA countdown
    etaInterval = setInterval(() => {
        if (etaSeconds.value > 30) {
            etaSeconds.value -= 1;
        }
    }, 1000);
});

onUnmounted(() => {
    if (stepTimeout1) clearTimeout(stepTimeout1);
    if (stepTimeout2) clearTimeout(stepTimeout2);
    if (etaInterval) clearInterval(etaInterval);
});
</script>

<template>
    <Head :title="$t('generatingPlan.meta.title')">
        <meta
            name="apple-itunes-app"
            :content="`app-id=6757151695, app-argument=${setPasswordUrl}`"
        />
    </Head>

    <GuestLayout>
        <div class="container mx-auto max-w-lg px-5 pt-7 pb-12">
            <!-- ========== PHASE 1: GENERATING ========== -->
            <template v-if="!isComplete">
                <!-- Badge (pulsing dot) -->
                <div class="animate-fade-in mb-6 text-center">
                    <div
                        class="live-badge mb-4 inline-flex items-center gap-2 rounded-full border border-primary-500/20 bg-primary-500/10 px-3.5 py-1.5 text-xs font-semibold text-primary-400"
                    >
                        <span
                            class="live-dot animate-pulse-dot h-1.5 w-1.5 rounded-full bg-primary-400"
                        ></span>
                        {{ $t('generatingPlan.badge.generating') }}
                    </div>

                    <!-- Title -->
                    <h1
                        class="mb-1 text-2xl leading-tight font-extrabold tracking-tight text-white"
                    >
                        {{
                            $t('generatingPlan.phase1.title', {
                                bodyGoal: bodyGoalLabel,
                            })
                        }}
                    </h1>
                    <p class="text-sm text-gray-500">
                        {{ $t('generatingPlan.phase1.subtitle') }}
                    </p>
                </div>

                <!-- 3-step progress bar -->
                <div
                    class="animate-fade-in-delay-1 mb-7 rounded-xl border border-gray-700 bg-gray-800 p-3.5"
                >
                    <!-- Horizontal stepper: icon — line — icon — line — icon -->
                    <div class="flex items-center gap-0">
                        <!-- Step 1 icon -->
                        <span
                            v-if="currentStep > 0"
                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-green-500/20 text-[9px] text-green-400"
                        >
                            ✓
                        </span>
                        <span
                            v-else
                            class="step-spinner h-5 w-5 shrink-0 rounded-full border-2 border-primary-400/30 border-t-primary-400"
                        ></span>
                        <!-- Line 1→2 -->
                        <div
                            class="mx-1 h-0.5 flex-1 rounded-full transition-colors duration-500"
                            :class="
                                currentStep > 0
                                    ? 'bg-green-500/40'
                                    : 'bg-gray-600'
                            "
                        ></div>
                        <!-- Step 2 icon -->
                        <span
                            v-if="currentStep > 1"
                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-green-500/20 text-[9px] text-green-400"
                        >
                            ✓
                        </span>
                        <span
                            v-else-if="currentStep === 1"
                            class="step-spinner h-5 w-5 shrink-0 rounded-full border-2 border-primary-400/30 border-t-primary-400"
                        ></span>
                        <span
                            v-else
                            class="h-5 w-5 shrink-0 rounded-full border-2 border-gray-600"
                        ></span>
                        <!-- Line 2→3 -->
                        <div
                            class="mx-1 h-0.5 flex-1 rounded-full transition-colors duration-500"
                            :class="
                                currentStep > 1
                                    ? 'bg-green-500/40'
                                    : 'bg-gray-600'
                            "
                        ></div>
                        <!-- Step 3 icon -->
                        <span
                            v-if="isComplete"
                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-green-500/20 text-[9px] text-green-400"
                        >
                            ✓
                        </span>
                        <span
                            v-else-if="currentStep === 2"
                            class="step-spinner h-5 w-5 shrink-0 rounded-full border-2 border-primary-400/30 border-t-primary-400"
                        ></span>
                        <span
                            v-else
                            class="h-5 w-5 shrink-0 rounded-full border-2 border-gray-600"
                        ></span>
                    </div>
                    <!-- Labels below icons -->
                    <div
                        class="mt-2 flex justify-between text-[11px] text-gray-500"
                    >
                        <span
                            :class="
                                currentStep === 0
                                    ? 'text-primary-400'
                                    : currentStep > 0
                                      ? 'text-green-400'
                                      : ''
                            "
                        >
                            {{
                                $t(
                                    'generatingPlan.progressSteps.analyzingGoals',
                                )
                            }}
                        </span>
                        <span
                            :class="
                                currentStep === 1
                                    ? 'text-primary-400'
                                    : currentStep > 1
                                      ? 'text-green-400'
                                      : ''
                            "
                        >
                            {{
                                $t(
                                    'generatingPlan.progressSteps.buildingWorkouts',
                                )
                            }}
                        </span>
                        <span
                            :class="
                                currentStep === 2 && !isComplete
                                    ? 'text-primary-400'
                                    : isComplete
                                      ? 'text-green-400'
                                      : ''
                            "
                        >
                            {{
                                $t(
                                    'generatingPlan.progressSteps.creatingNutrition',
                                )
                            }}
                        </span>
                    </div>
                    <div
                        class="mt-2.5 flex items-center justify-end text-xs text-gray-500"
                    >
                        {{ etaText }}
                    </div>
                </div>

                <!-- Permission to leave -->
                <p
                    class="animate-fade-in-delay-1 mb-7 text-center text-xs text-gray-500"
                >
                    {{ $t('generatingPlan.phase1.canLeave') }}
                </p>

                <!-- Mona chat demo -->
                <div
                    class="animate-fade-in-delay-2 mb-5 rounded-2xl border border-gray-700 bg-gray-800 p-4"
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
                    <div
                        class="mt-3 text-center text-xs font-medium text-gray-500"
                    >
                        {{ $t('generatingPlan.monaDemo.footer') }}
                    </div>
                </div>

                <!-- Soft CTA -->
                <div class="animate-fade-in-delay-3 mb-5 text-center">
                    <a
                        :href="smartLink"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-xs text-primary-400 underline underline-offset-2 transition-opacity hover:opacity-80"
                        @click="handleSmartLinkClick"
                    >
                        {{ $t('generatingPlan.cta.softLabel') }}
                    </a>
                </div>
            </template>

            <!-- ========== PHASE 2: COMPLETE ========== -->
            <template v-else>
                <!-- Badge (green check) -->
                <div class="animate-fade-in mb-6 text-center">
                    <div
                        class="live-badge mb-4 inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-3.5 py-1.5 text-xs font-semibold text-green-400"
                    >
                        <span class="text-sm">✓</span>
                        {{ $t('generatingPlan.badge.completed') }}
                    </div>

                    <!-- Title -->
                    <h1
                        class="mb-1 text-2xl leading-tight font-extrabold tracking-tight text-white"
                    >
                        {{
                            $t('generatingPlan.phase2.title', {
                                bodyGoal: bodyGoalLabel,
                            })
                        }}
                    </h1>
                    <p class="text-sm text-gray-400">
                        {{ $t('generatingPlan.phase2.subtitle') }}
                    </p>
                </div>

                <!-- Primary CTA button -->
                <div class="animate-fade-in-delay-1 mb-4">
                    <a
                        :href="smartLink"
                        target="_blank"
                        rel="noopener noreferrer"
                        @click="handleSmartLinkClick"
                    >
                        <Button class="w-full" size="lg">
                            {{ $t('generatingPlan.phase2.cta') }}
                        </Button>
                    </a>
                </div>

                <!-- Trust line -->
                <p
                    class="animate-fade-in-delay-1 mb-7 text-center text-xs text-gray-500"
                >
                    {{ $t('generatingPlan.trustLine') }}
                </p>

                <!-- 3 testimonials stacked -->
                <div class="animate-fade-in-delay-2 mb-7 flex flex-col gap-3">
                    <div
                        v-for="(review, index) in reviews"
                        :key="index"
                        class="flex items-start gap-3.5 rounded-xl border border-gray-700 bg-gray-800 p-3.5"
                    >
                        <div class="shrink-0 text-center">
                            <div class="text-xs tracking-wide text-yellow-400">
                                ★★★★★
                            </div>
                        </div>
                        <div class="flex-1">
                            <q
                                class="block text-xs leading-relaxed text-gray-400 italic"
                                >{{ review.quote }}</q
                            >
                            <cite
                                class="mt-1 block text-[11px] font-semibold text-gray-500 not-italic"
                                >— {{ review.author }}</cite
                            >
                        </div>
                    </div>
                </div>
            </template>

            <!-- ========== SHARED: PDF fallback + close page ========== -->
            <div
                class="animate-fade-in-delay-3 border-t border-gray-800/50 pt-5 text-center"
            >
                <p class="text-xs text-gray-500">
                    {{ $t('generatingPlan.fallback') }}
                </p>
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

/* Spinning circle for active step */
.step-spinner {
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
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
</style>
