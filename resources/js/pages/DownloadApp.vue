<script setup lang="ts">
import AppStoreDownload from '@/components/AppStoreDownload.vue';
import { Button } from '@/components/ui/button';
import FAQSection from '@/components/workoutPlan/FAQSection.vue';
import { useDeepLink } from '@/composables/useDeepLink';
import { useTracking } from '@/composables/useTracking';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import {
    ArrowLeftRight,
    Camera,
    Dumbbell,
    HeartPulse,
    MessageCircle,
    Salad,
} from 'lucide-vue-next';
import { computed, onMounted, type Component } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    userName: string | null;
    bodyGoal: string | null;
    setPasswordUrl: string | null;
    setPasswordDeepLink: string | null;
    appStoreUrl: string;
    isMobile: boolean;
    appStoreQrCode: string | null;
    setPasswordQrCode: string | null;
    openAppQrCode: string | null;
    utmSource?: string;
    utmMedium?: string;
    utmCampaign?: string;
    isAccountReady: boolean;
    schema: object;
    reviews: { title: string; body: string; author: string; date: string }[];
}

const props = defineProps<Props>();

const page = usePage<{
    footerLinks: {
        calorieCalculatorUrl: string;
        macroCalculatorUrl: string;
        indexUrl: string;
        blogUrl: string;
    };
}>();

const DEEP_LINK_URL = 'fytrr://';

const { t, tm, rt, locale } = useI18n();

const galleryScreens = computed(() => [
    {
        src: `/assets/images/app/fytrr-kalorien-tracker-${locale.value}.webp`,
        caption: t('downloadApp.gallery.tracking'),
    },
    {
        src: `/assets/images/app/fytrr-trainingsplan-${locale.value}.webp`,
        caption: t('downloadApp.gallery.workout'),
    },
    {
        src: `/assets/images/app/fytrr-fortschritt-${locale.value}.webp`,
        caption: t('downloadApp.gallery.progress'),
    },
]);

const toolLinks = computed(() => [
    {
        label: t('downloadApp.tools.calorie'),
        url: page.props.footerLinks.calorieCalculatorUrl,
    },
    {
        label: t('downloadApp.tools.macro'),
        url: page.props.footerLinks.macroCalculatorUrl,
    },
    {
        label: t('downloadApp.tools.workout'),
        url: page.props.footerLinks.indexUrl,
    },
    { label: t('downloadApp.tools.blog'), url: page.props.footerLinks.blogUrl },
]);
const canonicalUrl = computed(() => `https://fytrr.com/${locale.value}/app`);
const ogImage = 'https://fytrr.com/assets/images/og/fytrr-app.webp';
const appScreenshot = computed(
    () => `/assets/images/app/fytrr-ki-fitness-app-${locale.value}.webp`,
);

const { trackEvent } = useTracking();
const { openApp } = useDeepLink(props.appStoreUrl);

const { copy: copyDeepLink, copied: deepLinkCopied } = useClipboard({
    legacy: true,
    source: DEEP_LINK_URL,
});

const utmProps = computed(() => ({
    utm_source: props.utmSource,
    utm_medium: props.utmMedium,
    utm_campaign: props.utmCampaign,
    device: props.isMobile ? 'mobile' : 'desktop',
}));

const handleAppStoreClick = () => {
    trackEvent('Download App - App Store Click', utmProps.value);
};

const handleOpenAppClick = () => {
    trackEvent('Download App - Open App Click', utmProps.value);
    openApp('', {
        utm_source: props.utmSource ?? 'web',
        utm_medium: props.utmMedium ?? 'download_app',
        utm_campaign: props.utmCampaign ?? 'open_app',
    });
};

const handleCopyDeepLink = () => {
    copyDeepLink(DEEP_LINK_URL);
    trackEvent('Download App - Copy Deep Link', utmProps.value);
};

const handleActivateClick = () => {
    trackEvent('Download App - Activate Click', utmProps.value);
};

const faqs = computed(() => {
    const items = tm('downloadApp.faq.items');
    return (items as { question: string; answer: string }[]).map((item) => ({
        question: rt(item.question),
        answer: rt(item.answer),
    }));
});

const featureIcons: Component[] = [
    Dumbbell,
    Salad,
    Camera,
    ArrowLeftRight,
    HeartPulse,
    MessageCircle,
];

const features = computed(() => {
    const items = tm('downloadApp.features.items');
    return (items as { title: string; text: string }[]).map((item, i) => ({
        title: rt(item.title),
        text: rt(item.text),
        icon: featureIcons[i] ?? Dumbbell,
    }));
});

const steps = computed(() => {
    const items = tm('downloadApp.how.steps');
    return (items as { title: string; text: string }[]).map((item) => ({
        title: rt(item.title),
        text: rt(item.text),
    }));
});

const faqSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.value.map((f) => ({
        '@type': 'Question',
        name: f.question,
        acceptedAnswer: { '@type': 'Answer', text: f.answer },
    })),
}));

const schemaGraphs = computed(() =>
    [props.schema, faqSchema.value].map((s) => JSON.stringify(s)),
);

onMounted(() => {
    trackEvent('Download App - Page View', utmProps.value);
});
</script>

<template>
    <Head :title="t('downloadApp.meta.title')">
        <meta name="description" :content="t('downloadApp.meta.description')" />
        <link rel="canonical" :href="canonicalUrl" />
        <meta property="og:title" :content="t('downloadApp.meta.title')" />
        <meta
            property="og:description"
            :content="t('downloadApp.meta.description')"
        />
        <meta property="og:url" :content="canonicalUrl" />
        <meta property="og:type" content="website" />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('downloadApp.meta.title')" />
        <meta
            name="twitter:description"
            :content="t('downloadApp.meta.description')"
        />
        <meta name="twitter:image" :content="ogImage" />
        <component
            v-for="(graph, i) in schemaGraphs"
            :key="i"
            :is="'script'"
            type="application/ld+json"
        >
            {{ graph }}
        </component>
    </Head>

    <GuestLayout>
        <div class="theme-v2 flex-1 bg-canvas text-ink">
            <div class="mx-auto max-w-[1200px] px-6 sm:px-8 lg:px-[80px]">
                <!-- Hero -->
                <section
                    class="grid grid-cols-1 items-center gap-12 py-14 lg:grid-cols-[1fr_420px] lg:gap-16 lg:py-20"
                >
                    <div class="text-center lg:text-left">
                        <p
                            class="font-grotesk text-sm font-bold tracking-[0.06em] text-brand uppercase"
                        >
                            {{ t('downloadApp.hero.eyebrow') }}
                        </p>
                        <h1
                            class="mt-4 text-4xl font-extrabold tracking-tight text-balance text-ink sm:text-5xl lg:text-[56px] lg:leading-[1.03]"
                        >
                            {{ t('downloadApp.hero.heading') }}
                        </h1>
                        <p
                            class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-ink-muted lg:mx-0"
                        >
                            {{ t('downloadApp.hero.subheading') }}
                        </p>

                        <div
                            class="mt-6 flex items-center justify-center gap-2 lg:justify-start"
                        >
                            <div class="flex text-brand">
                                <svg
                                    v-for="n in 5"
                                    :key="n"
                                    class="size-5"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M12 2l2.9 6.26 6.9.6-5.2 4.52 1.56 6.76L12 17.27l-6.16 3.87L7.4 14.38 2.2 9.86l6.9-.6z"
                                    />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-ink-muted">
                                {{ t('downloadApp.hero.rating') }}
                            </span>
                        </div>

                        <!-- Activation: step 1 label -->
                        <p
                            v-if="setPasswordUrl"
                            class="mt-8 text-sm font-semibold text-brand"
                        >
                            {{ t('downloadApp.step1.label') }}:
                            {{ t('downloadApp.step1.text') }}
                        </p>

                        <div
                            class="mt-8 flex flex-col items-center gap-3 lg:items-start"
                        >
                            <AppStoreDownload
                                :app-store-url="appStoreUrl"
                                :qr-code="appStoreQrCode"
                                @click="handleAppStoreClick"
                            />
                            <p class="text-sm text-ink-muted">
                                {{ t('downloadApp.hero.trust') }}
                            </p>
                        </div>

                        <!-- Activation: already installed -->
                        <div
                            v-if="isAccountReady && !setPasswordUrl"
                            class="mt-8 border-t border-stroke pt-6"
                        >
                            <p class="mb-3 text-sm font-semibold text-brand">
                                {{ t('downloadApp.alreadyInstalled') }}
                            </p>
                            <Button
                                v-if="isMobile"
                                class="w-full sm:w-auto"
                                size="lg"
                                @click="handleOpenAppClick"
                            >
                                {{ t('downloadApp.openApp') }}
                            </Button>
                            <div
                                v-else-if="openAppQrCode"
                                class="flex items-center justify-center gap-6 lg:justify-start"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        class="rounded-xl border border-paper-line bg-paper p-2"
                                    >
                                        <img
                                            :src="openAppQrCode"
                                            alt="QR Code - Open fytrr"
                                            class="h-28 w-28"
                                        />
                                    </div>
                                    <span
                                        class="mt-1.5 text-[10px] text-ink-muted"
                                    >
                                        {{ t('downloadApp.desktop.scanQr') }}
                                    </span>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <div class="h-6 w-px bg-stroke"></div>
                                    <span class="text-[10px] text-ink-muted">
                                        {{ t('downloadApp.desktop.or') }}
                                    </span>
                                    <div class="h-6 w-px bg-stroke"></div>
                                </div>
                                <div class="flex flex-col items-center gap-2">
                                    <Button
                                        class="relative"
                                        :class="{
                                            'text-transparent!': deepLinkCopied,
                                        }"
                                        variant="secondary"
                                        @click="handleCopyDeepLink"
                                    >
                                        <span
                                            v-if="deepLinkCopied"
                                            class="absolute inset-0 flex items-center justify-center text-brand"
                                        >
                                            {{
                                                t('downloadApp.desktop.copied')
                                            }}
                                        </span>
                                        {{ t('downloadApp.desktop.copyLink') }}
                                    </Button>
                                    <span class="text-[10px] text-ink-muted">
                                        {{ t('downloadApp.desktop.copyHint') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Activation: step 2 (set password) -->
                        <div
                            v-if="setPasswordUrl"
                            class="mt-8 border-t border-stroke pt-6"
                        >
                            <p class="mb-3 text-sm font-semibold text-brand">
                                {{ t('downloadApp.step2.label') }}:
                                {{ t('downloadApp.step2.text') }}
                            </p>
                            <a
                                v-if="isMobile"
                                :href="setPasswordDeepLink ?? setPasswordUrl"
                                @click="handleActivateClick"
                            >
                                <Button
                                    variant="outline"
                                    size="lg"
                                    class="w-full sm:w-auto"
                                >
                                    {{ t('downloadApp.step2.cta') }}
                                </Button>
                            </a>
                            <div
                                v-else-if="setPasswordQrCode"
                                class="flex justify-center lg:justify-start"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        class="rounded-xl border border-paper-line bg-paper p-2"
                                    >
                                        <img
                                            :src="setPasswordQrCode"
                                            alt="QR Code - Activate"
                                            class="h-28 w-28"
                                        />
                                    </div>
                                    <span
                                        class="mt-1.5 text-[10px] text-ink-muted"
                                    >
                                        {{ t('downloadApp.desktop.scanQr') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Phone mockup -->
                    <div class="relative flex justify-center lg:justify-end">
                        <div
                            aria-hidden="true"
                            class="pointer-events-none absolute inset-0 -z-10 m-auto h-[420px] w-[420px] rounded-full bg-brand/15 blur-3xl"
                        />
                        <img
                            :src="appScreenshot"
                            :alt="t('downloadApp.hero.imageAlt')"
                            width="640"
                            height="1324"
                            class="w-[260px] drop-shadow-2xl sm:w-[300px] lg:w-[360px]"
                            fetchpriority="high"
                        />
                    </div>
                </section>

                <!-- Features -->
                <section class="border-t border-stroke py-16 lg:py-[88px]">
                    <h2
                        class="text-3xl font-bold text-balance text-ink sm:text-4xl"
                    >
                        {{ t('downloadApp.features.heading') }}
                    </h2>
                    <p class="mt-4 max-w-2xl leading-relaxed text-ink-muted">
                        {{ t('downloadApp.features.intro') }}
                    </p>
                    <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <span
                                class="flex h-12 w-12 items-center justify-center rounded-[14px] bg-brand/10 text-brand"
                            >
                                <component :is="feature.icon" class="size-6" />
                            </span>
                            <h3 class="mt-5 text-lg font-semibold text-ink">
                                {{ feature.title }}
                            </h3>
                            <p class="mt-2 leading-relaxed text-ink-muted">
                                {{ feature.text }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Screenshot gallery -->
                <section class="border-t border-stroke py-16 lg:py-[88px]">
                    <h2 class="text-3xl font-bold text-ink sm:text-4xl">
                        {{ t('downloadApp.gallery.heading') }}
                    </h2>
                    <div class="mt-12 grid grid-cols-1 gap-10 sm:grid-cols-3">
                        <figure
                            v-for="screen in galleryScreens"
                            :key="screen.src"
                            class="flex flex-col items-center text-center"
                        >
                            <img
                                :src="screen.src"
                                :alt="screen.caption"
                                width="500"
                                height="1034"
                                class="w-[220px] drop-shadow-2xl sm:w-full sm:max-w-[260px]"
                                loading="lazy"
                            />
                            <figcaption
                                class="mt-5 text-sm font-medium text-ink-muted"
                            >
                                {{ screen.caption }}
                            </figcaption>
                        </figure>
                    </div>
                </section>

                <!-- How it works -->
                <section class="border-t border-stroke py-16 lg:py-[88px]">
                    <h2 class="text-3xl font-bold text-ink sm:text-4xl">
                        {{ t('downloadApp.how.heading') }}
                    </h2>
                    <div class="mt-12 grid gap-6 md:grid-cols-3">
                        <div
                            v-for="(step, i) in steps"
                            :key="step.title"
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <span
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand font-grotesk font-bold text-on-brand"
                                >{{ i + 1 }}</span
                            >
                            <h3 class="mt-4 text-lg font-semibold text-ink">
                                {{ step.title }}
                            </h3>
                            <p class="mt-2 leading-relaxed text-ink-muted">
                                {{ step.text }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Reviews -->
                <section class="border-t border-stroke py-16 lg:py-[88px]">
                    <h2 class="text-3xl font-bold text-ink sm:text-4xl">
                        {{ t('downloadApp.reviews.heading') }}
                    </h2>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex text-brand">
                            <svg
                                v-for="n in 5"
                                :key="n"
                                class="size-5"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    d="M12 2l2.9 6.26 6.9.6-5.2 4.52 1.56 6.76L12 17.27l-6.16 3.87L7.4 14.38 2.2 9.86l6.9-.6z"
                                />
                            </svg>
                        </div>
                        <span class="text-sm text-ink-muted">
                            {{ t('downloadApp.reviews.subheading') }}
                        </span>
                    </div>
                    <div class="mt-10 grid gap-5 md:grid-cols-3">
                        <figure
                            v-for="review in reviews"
                            :key="review.author"
                            class="flex flex-col rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <div class="flex text-brand">
                                <svg
                                    v-for="n in 5"
                                    :key="n"
                                    class="size-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M12 2l2.9 6.26 6.9.6-5.2 4.52 1.56 6.76L12 17.27l-6.16 3.87L7.4 14.38 2.2 9.86l6.9-.6z"
                                    />
                                </svg>
                            </div>
                            <p class="mt-4 font-semibold text-ink">
                                {{ review.title }}
                            </p>
                            <blockquote
                                class="mt-2 flex-1 leading-relaxed text-ink-muted"
                            >
                                {{ review.body }}
                            </blockquote>
                            <figcaption class="mt-4 text-sm text-ink-muted">
                                {{ review.author }}
                            </figcaption>
                        </figure>
                    </div>
                </section>

                <!-- Pricing -->
                <section class="border-t border-stroke py-16 lg:py-[88px]">
                    <div
                        class="flex flex-col items-center gap-6 rounded-[24px] border border-brand/40 bg-brand/5 p-8 text-center lg:p-12"
                    >
                        <h2 class="text-3xl font-bold text-ink sm:text-4xl">
                            {{ t('downloadApp.pricing.heading') }}
                        </h2>
                        <p
                            class="max-w-xl text-lg leading-relaxed text-ink-muted"
                        >
                            {{ t('downloadApp.pricing.text') }}
                        </p>
                        <AppStoreDownload
                            :app-store-url="appStoreUrl"
                            :qr-code="appStoreQrCode"
                            @click="handleAppStoreClick"
                        />
                    </div>
                </section>

                <!-- Internal links -->
                <section class="border-t border-stroke py-16 lg:py-[88px]">
                    <h2 class="text-2xl font-bold text-ink">
                        {{ t('downloadApp.tools.heading') }}
                    </h2>
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <a
                            v-for="tool in toolLinks"
                            :key="tool.url"
                            :href="tool.url"
                            class="flex items-center justify-between gap-4 rounded-[16px] border border-stroke bg-surface p-5 font-semibold text-ink transition-colors hover:border-brand"
                        >
                            {{ tool.label }}
                            <span class="shrink-0 text-brand" aria-hidden="true"
                                >&rarr;</span
                            >
                        </a>
                    </div>
                </section>

                <FAQSection
                    :faqs="faqs"
                    :heading="t('downloadApp.faq.heading')"
                    class="border-t border-stroke"
                />
            </div>
        </div>
    </GuestLayout>
</template>
