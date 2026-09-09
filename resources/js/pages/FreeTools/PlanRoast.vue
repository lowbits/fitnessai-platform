<script setup lang="ts">
import AppUpsellBanner from '@/components/AppUpsellBanner.vue';
import BaseButton from '@/components/Base/BaseButton.vue';
import BaseCard from '@/components/Base/BaseCard.vue';
import FaqCard from '@/components/Base/FaqCard.vue';
import SectionHeader from '@/components/Base/SectionHeader.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import PlanComposer from '@/components/PlanRoast/PlanComposer.vue';
import RatingMeter from '@/components/PlanRoast/RatingMeter.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import {
    Dumbbell,
    Gauge,
    Layers,
    Moon,
    Repeat,
    Scale,
    TrendingUp,
} from 'lucide-vue-next';
import { type Component, computed, nextTick, onBeforeUnmount, ref } from 'vue';
import { useI18n } from 'vue-i18n';

interface Dimension {
    key: string;
    rating: 'good' | 'ok' | 'poor';
    label: string;
    evidence: string;
}

interface Ranking {
    percentile: number;
    sample_size: number;
}

interface Source {
    authors: string;
    title: string;
    publication: string;
    year: string;
    url: string;
}

interface Props {
    meta: {
        title: string;
        description: string;
        canonical: string;
        ogImage: string;
        ogImageAlt: string;
    };
    schema: Record<string, unknown>[];
    sources: Source[];
    author: { name: string; title: string; bio: string; image: string };
    internalLinks: { id: string; url: string }[];
    relatedArticles: { url: string; title: string; description: string }[];
}

const props = defineProps<Props>();
const { t, locale } = useI18n();
const page = usePage<{
    footerLinks: { appStoreUrl: string; aboutUrl?: string };
}>();

const appStoreUrl = computed(() => page.props.footerLinks?.appStoreUrl ?? '#');
const aboutUrl = computed(() => page.props.footerLinks?.aboutUrl);
const schemaJson = computed(() => props.schema.map((s) => JSON.stringify(s)));

const measureKeys = [
    'coverage',
    'volume',
    'balance',
    'intensity',
    'progression',
    'frequency',
    'recovery',
] as const;
const measureIcons: Record<(typeof measureKeys)[number], Component> = {
    coverage: Dumbbell,
    volume: Layers,
    balance: Scale,
    intensity: Gauge,
    progression: TrendingUp,
    frequency: Repeat,
    recovery: Moon,
};

const howToSteps = computed(() => {
    const howTo = props.schema.find((s) => s['@type'] === 'HowTo') as
        | { step?: { name: string; text: string }[] }
        | undefined;
    return howTo?.step ?? [];
});

const faqs = computed(() => {
    const faqPage = props.schema.find((s) => s['@type'] === 'FAQPage') as
        | { mainEntity?: { name: string; acceptedAnswer: { text: string } }[] }
        | undefined;
    return (faqPage?.mainEntity ?? []).map((q) => ({
        question: q.name,
        answer: q.acceptedAnswer.text,
    }));
});

const tone = ref<'roast' | 'neutral'>('roast');
const loading = ref(false);
const errorKey = ref('');
const status = ref('');
const score = ref<number | null>(null);
const displayScore = ref(0);
const dimensions = ref<Dimension[]>([]);
const ranking = ref<Ranking | null>(null);
const verdict = ref('');
const streaming = ref(false);
const resultRef = ref<HTMLElement | null>(null);
let source: EventSource | null = null;

const busy = computed(() => loading.value || streaming.value);
const hasResult = computed(() => score.value !== null);
const showRanking = computed(() => (ranking.value?.sample_size ?? 0) >= 20);

const errorMessages = computed<Record<string, string>>(() => ({
    tooLarge: t('planRoast.states.tooLarge', { maxFiles: 6, maxSize: 10 }),
    uploadTooLarge: t('planRoast.states.uploadTooLarge'),
    rateLimit: t('planRoast.states.rateLimit'),
    error: t('planRoast.states.error'),
    notWorkout: t('planRoast.states.notWorkout'),
}));
const errorMessage = computed(() => errorMessages.value[errorKey.value] ?? '');

function statusToErrorKey(status: number): string {
    if (status === 413) return 'uploadTooLarge';
    if (status === 429) return 'rateLimit';
    return 'error';
}

const scoreBand = computed<'good' | 'ok' | 'poor'>(() => {
    const value = score.value ?? 0;
    if (value >= 80) return 'good';
    if (value >= 50) return 'ok';
    return 'poor';
});

const scoreTextClass: Record<'good' | 'ok' | 'poor', string> = {
    good: 'text-emerald-400',
    ok: 'text-amber-400',
    poor: 'text-red-400',
};

const scoreBarClass: Record<'good' | 'ok' | 'poor', string> = {
    good: 'bg-emerald-400',
    ok: 'bg-amber-400',
    poor: 'bg-red-400',
};

function onSubmit(payload: { text: string; files: File[] }): void {
    const body = new FormData();
    if (payload.text) body.append('plan', payload.text);
    payload.files.forEach((file) => body.append('files[]', file));
    run(body);
}

function onInvalid(): void {
    reset();
    errorKey.value = 'tooLarge';
}

async function run(body: FormData): Promise<void> {
    reset();
    loading.value = true;
    status.value = t('planRoast.states.analyzing');
    body.append('tone', tone.value);
    body.append('locale', locale.value);

    try {
        const response = await fetch('/api/plan-roast/evaluate', {
            method: 'POST',
            headers: { Accept: 'application/json' },
            body,
        });

        if (!response.ok) {
            errorKey.value = statusToErrorKey(response.status);
            return;
        }

        const data = await response.json();

        if (data.plan_type !== 'workout') {
            errorKey.value = 'notWorkout';
            return;
        }

        score.value = data.score;
        animateScore(data.score);
        dimensions.value = data.dimensions;
        ranking.value = data.ranking;
        startStream(data.stream_token);
        nextTick(() =>
            resultRef.value?.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            }),
        );
    } catch {
        errorKey.value = 'error';
    } finally {
        loading.value = false;
    }
}

function startStream(token: string): void {
    streaming.value = true;
    status.value = t('planRoast.states.writing');
    source = new EventSource(`/api/plan-roast/stream/${token}`);

    source.onmessage = (event) => {
        if (event.data === '[DONE]') {
            closeStream();
            return;
        }

        try {
            const parsed = JSON.parse(event.data);
            if (parsed.type === 'text_delta' && parsed.delta) {
                verdict.value += parsed.delta;
            }
        } catch {
            // keep-alive or non-JSON frame, ignore
        }
    };

    source.onerror = () => closeStream();
}

function closeStream(): void {
    source?.close();
    source = null;
    streaming.value = false;
}

function animateScore(target: number): void {
    if (window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) {
        displayScore.value = target;
        return;
    }

    const start = performance.now();
    const duration = 800;

    function tick(now: number): void {
        const progress = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - progress, 3);
        displayScore.value = Math.round(target * eased);
        if (progress < 1) requestAnimationFrame(tick);
    }

    requestAnimationFrame(tick);
}

function reset(): void {
    errorKey.value = '';
    score.value = null;
    displayScore.value = 0;
    dimensions.value = [];
    ranking.value = null;
    verdict.value = '';
    closeStream();
}

onBeforeUnmount(closeStream);
</script>

<template>
    <Head :title="meta.title">
        <meta name="description" :content="meta.description" />
        <link rel="canonical" :href="meta.canonical" />
        <meta property="og:title" :content="meta.title" />
        <meta property="og:description" :content="meta.description" />
        <meta property="og:url" :content="meta.canonical" />
        <meta property="og:type" content="website" />
        <meta property="og:image" :content="meta.ogImage" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:image:alt" :content="meta.ogImageAlt" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="meta.title" />
        <meta name="twitter:description" :content="meta.description" />
        <meta name="twitter:image" :content="meta.ogImage" />
        <component
            v-for="(s, i) in schemaJson"
            :key="i"
            :is="'script'"
            type="application/ld+json"
        >
            {{ s }}
        </component>
    </Head>

    <GuestLayout>
        <div class="theme-v2 bg-canvas text-ink">
            <div class="mx-auto max-w-[1200px] px-6 sm:px-8 lg:px-[120px]">
                <!-- Hero -->
                <section class="pt-14 pb-10 lg:pt-20">
                    <SectionHeader
                        as="h1"
                        class="items-center text-center"
                        :eyebrow="t('planRoast.hero.eyebrow')"
                        :title="t('planRoast.hero.h1')"
                        :subtitle="t('planRoast.hero.subtitle')"
                    />
                </section>

                <!-- Input -->
                <section class="pb-8">
                    <div class="mx-auto max-w-2xl">
                        <PlanComposer
                            v-model:tone="tone"
                            :busy="busy"
                            :error="errorMessage"
                            @submit="onSubmit"
                            @invalid="onInvalid"
                        />
                        <p
                            v-if="busy"
                            class="mt-3 text-center text-sm text-ink-muted"
                        >
                            {{ status }}
                        </p>
                    </div>
                </section>

                <!-- Result -->
                <Transition name="reveal">
                    <section
                        v-if="hasResult"
                        ref="resultRef"
                        class="scroll-mt-24 pb-6"
                    >
                        <div class="mx-auto max-w-2xl text-center">
                            <div
                                class="flex items-baseline justify-center gap-2"
                            >
                                <span
                                    class="font-grotesk text-7xl font-extrabold tabular-nums"
                                    :class="scoreTextClass[scoreBand]"
                                    >{{ displayScore }}</span
                                >
                                <span class="text-xl text-ink-muted"
                                    >/ 100</span
                                >
                            </div>
                            <p
                                class="mt-1 text-sm font-medium tracking-wide text-ink-muted uppercase"
                            >
                                {{ t('planRoast.result.scoreLabel') }}
                            </p>
                            <div
                                class="mx-auto mt-4 h-1.5 w-full max-w-sm overflow-hidden rounded-full bg-surface-raised"
                            >
                                <div
                                    class="h-full rounded-full"
                                    :class="scoreBarClass[scoreBand]"
                                    :style="{ width: `${displayScore}%` }"
                                />
                            </div>
                            <p
                                v-if="showRanking"
                                class="mt-4 text-sm font-medium text-brand"
                            >
                                {{
                                    t('planRoast.result.ranking', {
                                        percent: ranking?.percentile,
                                    })
                                }}
                            </p>
                        </div>

                        <div class="mt-10 grid gap-4 md:grid-cols-2">
                            <BaseCard
                                v-for="dimension in dimensions"
                                :key="dimension.key"
                                tone="surface"
                            >
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <h3 class="font-semibold text-ink">
                                        {{ dimension.label }}
                                    </h3>
                                    <RatingMeter
                                        :rating="dimension.rating"
                                        :label="
                                            t(
                                                `planRoast.ratings.${dimension.rating}`,
                                            )
                                        "
                                    />
                                </div>
                                <p
                                    class="mt-2 text-sm leading-relaxed text-ink-muted"
                                >
                                    {{ dimension.evidence }}
                                </p>
                            </BaseCard>
                        </div>

                        <div class="mt-8">
                            <h2
                                class="text-xs font-semibold tracking-[0.14em] text-ink-muted uppercase"
                            >
                                {{ t('planRoast.result.verdictLabel') }}
                            </h2>
                            <BaseCard tone="surface" class="mt-3">
                                <p
                                    class="min-h-[6rem] leading-relaxed whitespace-pre-line text-ink"
                                >
                                    {{ verdict
                                    }}<span
                                        v-if="streaming"
                                        class="ml-0.5 inline-block h-5 w-[3px] animate-pulse bg-brand align-middle"
                                    />
                                </p>
                            </BaseCard>
                        </div>
                    </section>
                </Transition>

                <!-- Result-moment CTA: get the app -->
                <section v-if="hasResult" class="pb-16">
                    <div
                        class="flex flex-col gap-6 rounded-[24px] border border-brand/40 bg-brand/5 p-8 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <p class="max-w-lg text-2xl font-bold text-ink">
                            {{ t('planRoast.resultCta.headline') }}
                        </p>
                        <div
                            class="flex shrink-0 flex-col items-start gap-2 lg:items-end"
                        >
                            <BaseButton
                                as="a"
                                size="lg"
                                :href="appStoreUrl"
                                target="_blank"
                                rel="noopener"
                            >
                                {{ t('planRoast.resultCta.cta') }}
                                <span aria-hidden="true">&rarr;</span>
                            </BaseButton>
                            <p class="text-sm text-ink-muted">
                                {{ t('planRoast.resultCta.trust') }}
                            </p>
                            <GenerateFitnessPlanModal
                                utm-content="plan_roast_result"
                                utm-campaign="plan_roast"
                                #default="{ open }"
                            >
                                <button
                                    type="button"
                                    class="mt-1 text-sm font-medium text-ink-muted underline-offset-4 transition-colors hover:text-ink hover:underline"
                                    @click="open"
                                >
                                    {{ t('planRoast.resultCta.planHint') }}
                                </button>
                            </GenerateFitnessPlanModal>
                        </div>
                    </div>
                </section>

                <!-- How it works -->
                <section
                    id="how-it-works"
                    class="border-t border-stroke py-16 lg:py-[96px]"
                >
                    <SectionHeader
                        :eyebrow="t('planRoast.howItWorks.eyebrow')"
                        :title="t('planRoast.howItWorks.h2')"
                        :subtitle="t('planRoast.howItWorks.subtitle')"
                    />
                    <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                        <BaseCard
                            v-for="(step, i) in howToSteps"
                            :key="i"
                            tone="surface"
                        >
                            <span
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand font-grotesk font-bold text-on-brand"
                                >{{ i + 1 }}</span
                            >
                            <h3 class="mt-4 text-xl font-semibold text-ink">
                                {{ step.name }}
                            </h3>
                            <p class="mt-3 leading-relaxed text-ink-muted">
                                {{ step.text }}
                            </p>
                        </BaseCard>
                    </div>
                </section>

                <!-- What we measure -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <SectionHeader
                        :eyebrow="t('planRoast.measure.eyebrow')"
                        :title="t('planRoast.measure.h2')"
                        :subtitle="t('planRoast.measure.subtitle')"
                    />
                    <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <BaseCard
                            v-for="key in measureKeys"
                            :key="key"
                            tone="surface"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand"
                                >
                                    <component
                                        :is="measureIcons[key]"
                                        class="h-5 w-5"
                                    />
                                </span>
                                <h3 class="text-lg font-semibold text-ink">
                                    {{
                                        t(
                                            `planRoast.measure.items.${key}.title`,
                                        )
                                    }}
                                </h3>
                            </div>
                            <p class="mt-4 leading-relaxed text-ink">
                                {{ t(`planRoast.measure.items.${key}.what`) }}
                            </p>
                            <p
                                class="mt-2 text-sm leading-relaxed text-ink-muted"
                            >
                                {{ t(`planRoast.measure.items.${key}.why`) }}
                            </p>
                        </BaseCard>
                    </div>
                    <p
                        class="mt-8 max-w-2xl text-sm leading-relaxed text-ink-muted"
                    >
                        {{ t('planRoast.measure.trust') }}
                    </p>
                </section>

                <!-- Sources + author (E-E-A-T) -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <article class="mx-auto max-w-3xl">
                        <p class="text-sm leading-relaxed text-ink-muted">
                            {{ t('planRoast.content.disclaimer') }}
                        </p>

                        <div
                            class="mt-6 border-t border-stroke pt-6 text-sm text-ink-muted"
                        >
                            <p class="font-semibold text-ink">
                                {{ t('planRoast.content.sourcesTitle') }}
                            </p>
                            <ul class="mt-2 space-y-2">
                                <li v-for="src in sources" :key="src.url">
                                    <a
                                        :href="src.url"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="text-brand underline"
                                        >{{ src.authors }} ({{ src.year }})</a
                                    >. {{ src.title }}.
                                    <em>{{ src.publication }}</em
                                    >.
                                </li>
                            </ul>
                            <p class="mt-4">
                                {{ t('planRoast.content.reviewed') }}
                            </p>
                        </div>

                        <div
                            class="mt-8 flex flex-col gap-4 rounded-[16px] border border-stroke bg-surface p-6 sm:flex-row sm:items-center"
                            itemscope
                            itemtype="https://schema.org/Person"
                        >
                            <img
                                :src="author.image"
                                :alt="author.name"
                                itemprop="image"
                                width="64"
                                height="64"
                                loading="lazy"
                                class="h-16 w-16 shrink-0 rounded-full object-cover"
                            />
                            <div>
                                <p
                                    class="font-semibold text-ink"
                                    itemprop="name"
                                >
                                    <a
                                        v-if="aboutUrl"
                                        :href="aboutUrl"
                                        class="transition-colors hover:text-brand"
                                        itemprop="url"
                                        >{{ author.name }}</a
                                    >
                                    <template v-else>{{
                                        author.name
                                    }}</template>
                                </p>
                                <p
                                    class="text-sm font-medium text-brand"
                                    itemprop="jobTitle"
                                >
                                    {{ author.title }}
                                </p>
                                <p class="mt-1 text-sm text-ink-muted">
                                    {{ author.bio }}
                                </p>
                            </div>
                        </div>
                    </article>
                </section>

                <!-- FAQ -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <SectionHeader
                        :eyebrow="t('planRoast.faq.eyebrow')"
                        :title="t('planRoast.faq.heading')"
                    />
                    <div class="mt-12 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <FaqCard
                            v-for="faq in faqs"
                            :key="faq.question"
                            :question="faq.question"
                            :answer="faq.answer"
                        />
                    </div>
                </section>

                <!-- Related tools + further reading -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <h2 class="text-2xl font-bold text-ink">
                        {{ t('planRoast.relatedTools.heading') }}
                    </h2>
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <a
                            v-for="link in internalLinks"
                            :key="link.id"
                            :href="link.url"
                            class="flex items-center justify-between gap-4 rounded-[16px] border border-stroke bg-surface p-5 transition-colors hover:border-brand"
                        >
                            <span class="font-semibold text-ink">{{
                                t(`planRoast.relatedTools.${link.id}`)
                            }}</span>
                            <span class="shrink-0 text-brand">&rarr;</span>
                        </a>
                    </div>

                    <template v-if="relatedArticles.length">
                        <h3 class="mt-10 text-lg font-semibold text-ink">
                            {{ t('planRoast.furtherReading.heading') }}
                        </h3>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <a
                                v-for="article in relatedArticles"
                                :key="article.url"
                                :href="article.url"
                                class="flex items-center justify-between gap-4 rounded-[16px] border border-stroke bg-surface p-5 transition-colors hover:border-brand"
                            >
                                <span>
                                    <span
                                        class="block font-semibold text-ink"
                                        >{{ article.title }}</span
                                    >
                                    <span
                                        class="mt-1 block text-sm text-ink-muted"
                                        >{{ article.description }}</span
                                    >
                                </span>
                                <span class="shrink-0 text-brand">&rarr;</span>
                            </a>
                        </div>
                    </template>
                </section>
            </div>

            <!-- App upsell banner: the goal is more users in the app -->
            <AppUpsellBanner class="border-t border-stroke" />
        </div>
    </GuestLayout>
</template>

<style scoped>
.reveal-enter-active {
    transition:
        opacity 0.4s ease,
        transform 0.4s ease;
}

.reveal-enter-from {
    opacity: 0;
    transform: translateY(12px);
}
</style>
