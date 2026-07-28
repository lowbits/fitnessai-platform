<script setup lang="ts">
import BaseButton from '@/components/Base/BaseButton.vue';
import BaseCard from '@/components/Base/BaseCard.vue';
import FaqCard from '@/components/Base/FaqCard.vue';
import SectionHeader from '@/components/Base/SectionHeader.vue';
import MacroCalculatorForm from '@/components/MacroCalculator/MacroCalculatorForm.vue';
import MacroResultSheet from '@/components/MacroCalculator/MacroResultSheet.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import {
    useMacroCalculator,
    type Goal,
    type MacroResult,
} from '@/composables/useMacroCalculator';
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
    meta: { title: string; description: string; canonical: string };
    alternateUrls: Record<string, string>;
    schema: object[];
    relatedArticles: { url: string; title: string; description: string }[];
    author: { name: string; title: string; bio: string; image: string };
    internalLinks: { id: string; url: string }[];
    result: MacroResult | null;
}

const props = defineProps<Props>();

const { t } = useI18n();
const { trackEvent } = useTracking();
const page = usePage<PageProps>();
const locale = computed(() => page.props.currentLocale);
const showUnitToggle = computed(() => locale.value === 'en');
const aboutUrl = computed(() => page.props.footerLinks.aboutUrl);

// Calculator state lives here so the upsell CTA can prefill the plan form.
const calc = useMacroCalculator();

const goalToBodyGoal: Record<Goal, string> = {
    lose: 'lose_weight',
    maintain: 'get_fit',
    gain: 'build_muscle',
};

const planPrefill = computed(() => {
    const i = calc.input;
    return {
        gender: i.gender ?? '',
        age: i.age != null ? String(i.age) : '',
        weight: i.weight != null ? String(i.weight) : '',
        height: i.height != null ? String(i.height) : '',
        body_goal: i.goal ? goalToBodyGoal[i.goal] : '',
        activity_level: i.activity ?? '',
        training_sessions: i.sessions != null ? String(i.sessions) : '',
        dietary_preference: i.diet ?? '',
    };
});

const schemaJson = computed(() => props.schema.map((s) => JSON.stringify(s)));

const faqs = computed(() =>
    (
        props.schema[1] as {
            mainEntity: { name: string; acceptedAnswer: { text: string } }[];
        }
    ).mainEntity.map((q) => ({
        question: q.name,
        answer: q.acceptedAnswer.text,
    })),
);

const methodCards = ['bmr', 'activity', 'macros'] as const;
const proseSections = ['whatAreMacros', 'losing', 'muscle', 'diet'] as const;

const onCtaClick = (location: 'inline' | 'footer') => {
    trackEvent('macro_calc_cta_click', { location });
};

// Fire `macro_calc_result_viewed` once the result has been on screen for 2s.
const resultRef = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;
let viewTimer: ReturnType<typeof setTimeout> | null = null;
let resultViewed = false;

onMounted(() => {
    if (!resultRef.value || typeof IntersectionObserver === 'undefined') return;

    observer = new IntersectionObserver(
        (entries) => {
            const visible = entries[0]?.isIntersecting;
            if (visible && props.result && !resultViewed && !viewTimer) {
                viewTimer = setTimeout(() => {
                    resultViewed = true;
                    trackEvent('macro_calc_result_viewed');
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
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="meta.title" />
        <meta name="twitter:description" :content="meta.description" />
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
                    class="relative overflow-hidden pt-14 pb-14 text-center lg:pt-20 lg:pb-20"
                >
                    <!-- soft brand glow behind the headline -->
                    <div
                        aria-hidden="true"
                        class="pointer-events-none absolute inset-x-0 -top-32 -z-10 mx-auto h-[420px] max-w-3xl rounded-full bg-brand/10 blur-3xl"
                    />
                    <div class="relative mx-auto max-w-4xl">
                        <h1
                            class="text-5xl font-extrabold tracking-tight text-balance text-ink sm:text-6xl lg:text-[80px] lg:leading-[0.98]"
                        >
                            {{ t('macroCalculator.hero.h1') }}
                        </h1>
                        <div class="mt-5 flex justify-center">
                            <span
                                class="inline-block -rotate-[1.5deg] rounded-[18px] bg-brand px-6 py-2 font-grotesk text-4xl font-extrabold tracking-tight text-on-brand sm:text-5xl lg:text-[60px] lg:leading-[1.2]"
                            >
                                {{ t('macroCalculator.hero.accent') }}
                            </span>
                        </div>
                        <p
                            class="mx-auto mt-8 max-w-2xl text-lg leading-relaxed text-ink-muted"
                        >
                            {{ t('macroCalculator.hero.subtitle') }}
                        </p>
                        <p class="mt-6 font-caveat text-2xl font-semibold text-brand">
                            {{ t('macroCalculator.hero.annotation') }}
                        </p>
                    </div>
                </section>

                <!-- Calculator -->
                <section class="pb-16">
                    <div
                        class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_400px] lg:items-start"
                    >
                        <BaseCard tone="surface">
                            <MacroCalculatorForm
                                :calc="calc"
                                :show-unit-toggle="showUnitToggle"
                            />
                        </BaseCard>

                        <div ref="resultRef" class="lg:sticky lg:top-24">
                            <MacroResultSheet :result="result" />
                        </div>
                    </div>
                </section>

                <!-- Inline CTA bar -->
                <section class="pb-16">
                    <div
                        class="flex flex-col gap-5 rounded-[24px] border border-stroke bg-surface px-8 py-6 sm:flex-row sm:items-center sm:justify-between sm:gap-6"
                    >
                        <div>
                            <p class="text-xl font-bold text-ink">
                                {{ t('macroCalculator.inlineCta.headline') }}
                            </p>
                            <p class="mt-1 text-ink-muted">
                                {{ t('macroCalculator.inlineCta.subline') }}
                            </p>
                        </div>
                        <GenerateFitnessPlanModal
                            utm-content="macro_calculator_inline"
                            utm-campaign="macro_calculator"
                            :prefill="planPrefill"
                            #default="{ open }"
                        >
                            <BaseButton
                                size="lg"
                                class="shrink-0"
                                @click="
                                    () => {
                                        onCtaClick('inline');
                                        open();
                                    }
                                "
                            >
                                {{ t('macroCalculator.inlineCta.cta') }}
                                <span aria-hidden="true">&rarr;</span>
                            </BaseButton>
                        </GenerateFitnessPlanModal>
                    </div>
                </section>

                <!-- Method -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <SectionHeader
                        :eyebrow="t('macroCalculator.method.eyebrow')"
                        :title="t('macroCalculator.method.h2')"
                        :subtitle="t('macroCalculator.method.subtitle')"
                    />
                    <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                        <BaseCard
                            v-for="card in methodCards"
                            :key="card"
                            tone="surface"
                        >
                            <h3 class="text-xl font-semibold text-ink">
                                {{
                                    t(
                                        `macroCalculator.method.cards.${card}.title`,
                                    )
                                }}
                            </h3>
                            <p class="mt-3 leading-relaxed text-ink-muted">
                                {{
                                    t(
                                        `macroCalculator.method.cards.${card}.body`,
                                    )
                                }}
                            </p>
                            <p
                                v-if="card === 'bmr'"
                                class="mt-4 rounded-[8px] bg-surface-raised px-4 py-3 font-mono text-sm text-ink"
                            >
                                {{ t('macroCalculator.method.formula') }}
                            </p>
                        </BaseCard>
                    </div>
                </section>

                <!-- SEO prose -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <article class="mx-auto max-w-3xl">
                        <div
                            v-for="section in proseSections"
                            :key="section"
                            class="mt-10 first:mt-0"
                        >
                            <h2 class="text-3xl font-bold text-ink">
                                {{ t(`macroCalculator.content.${section}.h2`) }}
                            </h2>
                            <p class="mt-4 leading-relaxed text-ink-muted">
                                {{
                                    t(`macroCalculator.content.${section}.body`)
                                }}
                            </p>
                        </div>

                        <!-- Worked example -->
                        <div
                            class="mt-10 rounded-[16px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-ink">
                                {{ t('macroCalculator.content.example.title') }}
                            </h3>
                            <p class="mt-3 leading-relaxed text-ink-muted">
                                {{ t('macroCalculator.content.example.body') }}
                            </p>
                        </div>

                        <!-- Disclaimer -->
                        <p class="mt-8 text-sm leading-relaxed text-ink-muted">
                            {{ t('macroCalculator.content.disclaimer') }}
                        </p>

                        <!-- Sources + reviewed date -->
                        <div
                            class="mt-6 border-t border-stroke pt-6 text-sm text-ink-muted"
                        >
                            <p class="font-semibold text-ink">
                                {{ t('macroCalculator.content.sourcesTitle') }}
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
                                            'macroCalculator.content.sources.mifflin',
                                        )
                                    }}
                                </li>
                                <li>
                                    <a
                                        href="https://pubmed.ncbi.nlm.nih.gov/24864135/"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="text-brand underline"
                                        >Helms ER, et al. (2014)</a
                                    >
                                    —
                                    {{
                                        t(
                                            'macroCalculator.content.sources.helms',
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
                                        t('macroCalculator.content.sources.dge')
                                    }}
                                </li>
                            </ul>
                            <p class="mt-4">
                                {{ t('macroCalculator.content.reviewed') }}
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
                        :eyebrow="t('macroCalculator.faq.eyebrow')"
                        :title="t('macroCalculator.faq.heading')"
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
                        {{ t('macroCalculator.relatedTools.heading') }}
                    </h2>
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <a
                            v-for="link in internalLinks"
                            :key="link.id"
                            :href="link.url"
                            class="flex items-center justify-between gap-4 rounded-[16px] border border-stroke bg-surface p-5 transition-colors hover:border-brand"
                        >
                            <span class="font-semibold text-ink">{{
                                t(`macroCalculator.relatedTools.${link.id}`)
                            }}</span>
                            <span class="shrink-0 text-brand">&rarr;</span>
                        </a>
                    </div>

                    <template v-if="relatedArticles.length">
                        <h3 class="mt-10 text-lg font-semibold text-ink">
                            {{ t('macroCalculator.furtherReading.heading') }}
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
                        :eyebrow="t('macroCalculator.upsell.eyebrow')"
                        :title="t('macroCalculator.upsell.h2')"
                        :subtitle="t('macroCalculator.upsell.subtitle')"
                    />
                    <GenerateFitnessPlanModal
                        utm-content="macro_calculator_footer"
                        utm-campaign="macro_calculator"
                        :prefill="planPrefill"
                        #default="{ open }"
                    >
                        <BaseButton
                            size="lg"
                            class="mt-8"
                            @click="
                                () => {
                                    onCtaClick('footer');
                                    open();
                                }
                            "
                        >
                            {{ t('macroCalculator.upsell.cta') }}
                        </BaseButton>
                    </GenerateFitnessPlanModal>
                    <p class="mt-3 text-sm text-ink-muted">
                        {{ t('macroCalculator.upsell.subline') }}
                    </p>
                </section>
            </div>
        </div>
    </GuestLayout>
</template>
