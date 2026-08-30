<script setup lang="ts">
import BaseButton from '@/components/Base/BaseButton.vue';
import BaseCard from '@/components/Base/BaseCard.vue';
import FaqCard from '@/components/Base/FaqCard.vue';
import SectionHeader from '@/components/Base/SectionHeader.vue';
import CalorieCalculatorForm from '@/components/CalorieCalculator/CalorieCalculatorForm.vue';
import CalorieResultSheet from '@/components/CalorieCalculator/CalorieResultSheet.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import {
    ACTIVITY_FACTORS,
    useCalorieCalculator,
    type Activity,
    type CalorieResult,
    type Goal,
} from '@/composables/useCalorieCalculator';
import { useTracking } from '@/composables/useTracking';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';

type PageProps = {
    footerLinks: { appStoreUrl: string; aboutUrl?: string };
    currentLocale: string;
};

interface Props {
    meta: {
        title: string;
        description: string;
        canonical: string;
        ogImage: string;
        ogImageAlt: string;
    };
    alternateUrls: Record<string, string>;
    schema: object[];
    relatedArticles: { url: string; title: string; description: string }[];
    author: { name: string; title: string; bio: string; image: string };
    internalLinks: { id: string; url: string }[];
    result: CalorieResult | null;
}

const props = defineProps<Props>();

const { t, locale } = useI18n();
const { trackEvent } = useTracking();
const page = usePage<PageProps>();
const appStoreUrl = computed(() => page.props.footerLinks.appStoreUrl);
const aboutUrl = computed(() => page.props.footerLinks.aboutUrl);

const calc = useCalorieCalculator();
const { input, isComplete } = calc;
const result = computed(() => props.result);

const numberFormat = computed(() => new Intl.NumberFormat(locale.value));
const formattedCalories = computed(() =>
    result.value ? numberFormat.value.format(result.value.calories) : '',
);

const goalToBodyGoal: Record<Goal, string> = {
    lose: 'lose_weight',
    maintain: 'get_fit',
    gain: 'build_muscle',
};
const activityToPlanLevel: Record<Activity, string> = {
    sedentary: 'mainly_sitting',
    light: 'mainly_sitting',
    moderate: 'mainly_standing',
    active: 'mainly_walking',
    veryActive: 'hard_working',
};

const planPrefill = computed(() => ({
    gender: input.gender ?? '',
    age: input.age != null ? String(input.age) : '',
    weight: input.weight != null ? String(input.weight) : '',
    height: input.height != null ? String(input.height) : '',
    body_goal: goalToBodyGoal[input.goal],
    activity_level: activityToPlanLevel[input.activity],
}));

const schemaJson = computed(() => props.schema.map((s) => JSON.stringify(s)));

const faqs = computed(() => {
    const faqPage = props.schema.find(
        (
            s,
        ): s is {
            mainEntity: { name: string; acceptedAnswer: { text: string } }[];
        } => (s as { '@type'?: string })['@type'] === 'FAQPage',
    );
    return (faqPage?.mainEntity ?? []).map((q) => ({
        question: q.name,
        answer: q.acceptedAnswer.text,
    }));
});

const methodSteps = ['bmr', 'activity', 'goal'] as const;
const proseSections = ['grundumsatz', 'losing', 'muscle', 'menWomen'] as const;

const activityRows = computed(() =>
    (
        ['sedentary', 'light', 'moderate', 'active', 'veryActive'] as Activity[]
    ).map((key) => ({
        key,
        label: t(`calorieCalculator.form.activityOptions.${key}.title`),
        factor: ACTIVITY_FACTORS[key].toLocaleString(locale.value, {
            minimumFractionDigits: 0,
        }),
        man: numberFormat.value.format(
            Math.round(1780 * ACTIVITY_FACTORS[key]),
        ),
        woman: numberFormat.value.format(
            Math.round(1400 * ACTIVITY_FACTORS[key]),
        ),
    })),
);

const onCtaClick = (location: 'result_app' | 'result_plan' | 'footer') => {
    trackEvent('calorie_calc_cta_click', { location });
};

const resultRef = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;
let viewTimer: ReturnType<typeof setTimeout> | null = null;
let resultViewed = false;

onMounted(() => {
    if (!resultRef.value || typeof IntersectionObserver === 'undefined') return;

    observer = new IntersectionObserver(
        (entries) => {
            const visible = entries[0]?.isIntersecting;
            if (visible && result.value && !resultViewed && !viewTimer) {
                viewTimer = setTimeout(() => {
                    resultViewed = true;
                    trackEvent('calorie_calc_result_viewed');
                    observer?.disconnect();
                }, 2000);
            } else if (!visible && viewTimer) {
                clearTimeout(viewTimer);
                viewTimer = null;
            }
        },
        { threshold: 0.5 },
    );
    observer.observe(resultRef.value);
});

onBeforeUnmount(() => {
    if (viewTimer) clearTimeout(viewTimer);
    observer?.disconnect();
});
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
        <meta property="og:image:height" content="800" />
        <meta property="og:image:alt" :content="meta.ogImageAlt" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="meta.title" />
        <meta name="twitter:description" :content="meta.description" />
        <meta name="twitter:image" :content="meta.ogImage" />
        <!-- hreflang + x-default are emitted globally by app.blade.php. -->
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
                <section
                    class="relative overflow-hidden pt-14 pb-14 text-center lg:pt-20 lg:pb-16"
                >
                    <div
                        aria-hidden="true"
                        class="pointer-events-none absolute inset-x-0 -top-32 -z-10 mx-auto h-[420px] max-w-3xl rounded-full bg-brand/10 blur-3xl"
                    />
                    <div class="relative mx-auto max-w-4xl">
                        <h1
                            data-speakable="headline"
                            class="text-4xl font-extrabold tracking-tight text-balance text-ink sm:text-5xl lg:text-[64px] lg:leading-[1.02]"
                        >
                            {{ t('calorieCalculator.hero.h1') }}
                        </h1>
                        <div class="mt-5 flex justify-center">
                            <span
                                class="inline-block -rotate-[1.5deg] rounded-[18px] bg-brand px-6 py-2 font-grotesk text-3xl font-extrabold tracking-tight text-on-brand sm:text-4xl lg:text-[52px] lg:leading-[1.2]"
                            >
                                {{ t('calorieCalculator.hero.accent') }}
                            </span>
                        </div>
                        <p
                            data-speakable="summary"
                            class="mx-auto mt-8 max-w-2xl text-lg leading-relaxed text-ink-muted"
                        >
                            {{ t('calorieCalculator.hero.answer') }}
                        </p>
                        <p
                            class="mt-6 font-caveat text-2xl font-semibold text-brand"
                        >
                            {{ t('calorieCalculator.hero.annotation') }}
                        </p>
                    </div>
                </section>

                <!-- Calculator -->
                <section class="pb-10">
                    <div
                        class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_400px] lg:items-start"
                    >
                        <BaseCard tone="surface">
                            <CalorieCalculatorForm :calc="calc" />
                        </BaseCard>

                        <div ref="resultRef" class="lg:sticky lg:top-24">
                            <CalorieResultSheet :result="result" />
                        </div>
                    </div>
                </section>

                <!-- Result-moment CTA: get the app -->
                <section class="pb-16">
                    <div
                        class="flex flex-col gap-6 rounded-[24px] border border-brand/40 bg-brand/5 p-8 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div class="max-w-lg">
                            <p
                                v-if="isComplete && result"
                                class="font-grotesk text-lg font-bold text-brand"
                            >
                                {{
                                    t('calorieCalculator.resultCta.needLine', {
                                        calories: formattedCalories,
                                    })
                                }}
                            </p>
                            <p class="mt-1 text-2xl font-bold text-ink">
                                {{ t('calorieCalculator.resultCta.headline') }}
                            </p>
                        </div>
                        <div
                            class="flex shrink-0 flex-col items-start gap-2 lg:items-end"
                        >
                            <BaseButton
                                as="a"
                                size="lg"
                                :href="appStoreUrl"
                                target="_blank"
                                rel="noopener"
                                @click="onCtaClick('result_app')"
                            >
                                {{ t('calorieCalculator.resultCta.cta') }}
                                <span aria-hidden="true">&rarr;</span>
                            </BaseButton>
                            <p class="text-sm text-ink-muted">
                                {{ t('calorieCalculator.resultCta.trust') }}
                            </p>
                            <GenerateFitnessPlanModal
                                utm-content="calorie_calculator_result_plan"
                                utm-campaign="calorie_calculator"
                                :prefill="planPrefill"
                                #default="{ open }"
                            >
                                <button
                                    type="button"
                                    class="mt-1 text-sm font-medium text-ink-muted underline-offset-4 transition-colors hover:text-ink hover:underline"
                                    @click="
                                        () => {
                                            onCtaClick('result_plan');
                                            open();
                                        }
                                    "
                                >
                                    {{
                                        t(
                                            'calorieCalculator.resultCta.planHint',
                                        )
                                    }}
                                </button>
                            </GenerateFitnessPlanModal>
                        </div>
                    </div>
                </section>

                <!-- Method (HowTo): how to calculate your calorie needs -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <SectionHeader
                        :eyebrow="t('calorieCalculator.method.eyebrow')"
                        :title="t('calorieCalculator.method.h2')"
                        :subtitle="t('calorieCalculator.method.subtitle')"
                    />
                    <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                        <BaseCard
                            v-for="(step, i) in methodSteps"
                            :key="step"
                            tone="surface"
                        >
                            <span
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand font-grotesk font-bold text-on-brand"
                                >{{ i + 1 }}</span
                            >
                            <h3 class="mt-4 text-xl font-semibold text-ink">
                                {{
                                    t(
                                        `calorieCalculator.method.steps.${step}.title`,
                                    )
                                }}
                            </h3>
                            <p class="mt-3 leading-relaxed text-ink-muted">
                                {{
                                    t(
                                        `calorieCalculator.method.steps.${step}.body`,
                                    )
                                }}
                            </p>
                            <p
                                v-if="step === 'bmr'"
                                class="mt-4 rounded-[8px] bg-surface-raised px-4 py-3 font-mono text-sm text-ink"
                            >
                                {{ t('calorieCalculator.method.formula') }}
                            </p>
                        </BaseCard>
                    </div>
                </section>

                <!-- Activity comparison table -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <SectionHeader
                        :eyebrow="t('calorieCalculator.table.eyebrow')"
                        :title="t('calorieCalculator.table.h2')"
                        :subtitle="t('calorieCalculator.table.subtitle')"
                    />
                    <div
                        class="mt-10 overflow-x-auto rounded-[16px] border border-stroke"
                    >
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-stroke bg-surface">
                                    <th
                                        class="px-4 py-4 font-semibold text-ink"
                                    >
                                        {{ t('calorieCalculator.table.level') }}
                                    </th>
                                    <th
                                        class="px-4 py-4 font-semibold text-ink"
                                    >
                                        {{
                                            t('calorieCalculator.table.factor')
                                        }}
                                    </th>
                                    <th
                                        class="px-4 py-4 font-semibold text-ink"
                                    >
                                        {{ t('calorieCalculator.table.man') }}
                                    </th>
                                    <th
                                        class="px-4 py-4 font-semibold text-ink"
                                    >
                                        {{ t('calorieCalculator.table.woman') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="row in activityRows"
                                    :key="row.key"
                                    class="border-b border-stroke last:border-0"
                                >
                                    <td class="px-4 py-4 font-medium text-ink">
                                        {{ row.label }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-ink-muted tabular-nums"
                                    >
                                        {{ row.factor }}
                                    </td>
                                    <td
                                        class="px-4 py-4 text-ink-muted tabular-nums"
                                    >
                                        {{ row.man }} kcal
                                    </td>
                                    <td
                                        class="px-4 py-4 text-ink-muted tabular-nums"
                                    >
                                        {{ row.woman }} kcal
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 text-sm text-ink-muted">
                        {{ t('calorieCalculator.table.note') }}
                    </p>
                </section>

                <!-- SEO prose: question-led sections -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <article class="mx-auto max-w-3xl">
                        <div
                            v-for="section in proseSections"
                            :key="section"
                            class="mt-10 first:mt-0"
                        >
                            <h2 class="text-3xl font-bold text-ink">
                                {{
                                    t(`calorieCalculator.content.${section}.h2`)
                                }}
                            </h2>
                            <p class="mt-4 leading-relaxed text-ink-muted">
                                {{
                                    t(
                                        `calorieCalculator.content.${section}.body`,
                                    )
                                }}
                            </p>
                        </div>

                        <!-- Disclaimer -->
                        <p class="mt-10 text-sm leading-relaxed text-ink-muted">
                            {{ t('calorieCalculator.content.disclaimer') }}
                        </p>

                        <!-- Sources + reviewed date -->
                        <div
                            class="mt-6 border-t border-stroke pt-6 text-sm text-ink-muted"
                        >
                            <p class="font-semibold text-ink">
                                {{
                                    t('calorieCalculator.content.sourcesTitle')
                                }}
                            </p>
                            <ul class="mt-2 space-y-1">
                                <li>
                                    <a
                                        href="https://pubmed.ncbi.nlm.nih.gov/2305711/"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="text-brand underline"
                                        >Mifflin MD, St Jeor ST, et al.
                                        (1990)</a
                                    >
                                    —
                                    {{
                                        t(
                                            'calorieCalculator.content.sources.mifflin',
                                        )
                                    }}
                                </li>
                                <li>
                                    <a
                                        href="https://www.dge.de/wissenschaft/referenzwerte/"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="text-brand underline"
                                        >DGE</a
                                    >
                                    —
                                    {{
                                        t(
                                            'calorieCalculator.content.sources.dge',
                                        )
                                    }}
                                </li>
                            </ul>
                            <p class="mt-4">
                                {{ t('calorieCalculator.content.reviewed') }}
                            </p>
                        </div>

                        <!-- Author byline (E-E-A-T) -->
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
                        :eyebrow="t('calorieCalculator.faq.eyebrow')"
                        :title="t('calorieCalculator.faq.heading')"
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
                        {{ t('calorieCalculator.relatedTools.heading') }}
                    </h2>
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <a
                            v-for="link in internalLinks"
                            :key="link.id"
                            :href="link.url"
                            class="flex items-center justify-between gap-4 rounded-[16px] border border-stroke bg-surface p-5 transition-colors hover:border-brand"
                        >
                            <span class="font-semibold text-ink">{{
                                t(`calorieCalculator.relatedTools.${link.id}`)
                            }}</span>
                            <span class="shrink-0 text-brand">&rarr;</span>
                        </a>
                    </div>

                    <template v-if="relatedArticles.length">
                        <h3 class="mt-10 text-lg font-semibold text-ink">
                            {{ t('calorieCalculator.furtherReading.heading') }}
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

                <!-- Closing upsell -->
                <section
                    class="border-t border-stroke py-16 text-center lg:py-[96px]"
                >
                    <SectionHeader
                        class="items-center text-center"
                        :eyebrow="t('calorieCalculator.upsell.eyebrow')"
                        :title="t('calorieCalculator.upsell.h2')"
                        :subtitle="t('calorieCalculator.upsell.subtitle')"
                    />
                    <GenerateFitnessPlanModal
                        utm-content="calorie_calculator_footer"
                        utm-campaign="calorie_calculator"
                        :prefill="planPrefill"
                        #default="{ open }"
                    >
                        <BaseButton
                            size="lg"
                            class="mt-8"
                            @click="
                                () => {
                                    onAppCtaClick('footer');
                                    open();
                                }
                            "
                        >
                            {{ t('calorieCalculator.upsell.cta') }}
                        </BaseButton>
                    </GenerateFitnessPlanModal>
                    <p class="mt-3 text-sm text-ink-muted">
                        {{ t('calorieCalculator.upsell.subline') }}
                    </p>
                </section>
            </div>
        </div>
    </GuestLayout>
</template>
