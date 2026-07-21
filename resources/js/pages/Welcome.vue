<script setup lang="ts">
import AppSection from '@/components/AppSection.vue';
import ConvinceCard from '@/components/ConvinceCard.vue';
import FeatureCard from '@/components/FeatureCard.vue';
import Footer from '@/components/Footer.vue';
import FormCard from '@/components/FormCard.vue';
import GenerateFitnessPlanForm from '@/components/GenerateFitnessPlanForm.vue';
import Header from '@/components/Header.vue';
import AtomIcon from '@/components/icons/AtomIcon.vue';
import FingerprintIcon from '@/components/icons/FingerprintIcon.vue';
import FlagIcon from '@/components/icons/FlagIcon.vue';
import GhostIcon from '@/components/icons/GhostIcon.vue';
import ProgressivePlanIcon from '@/components/icons/ProgressivePlanIcon.vue';
import ShoppingBagIcon from '@/components/icons/ShoppingBagIcon.vue';
import TableIcon from '@/components/icons/TableIcon.vue';
import TrackingIcon from '@/components/icons/TrackingIcon.vue';
import { useSelectedLanguage } from '@/composables/useSelectedLanguage';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const page = usePage();
const props = defineProps<{
    durationDays: number;
    blogPosts: Array<{ title: string; description: string; url: string; image: string | null; imageAlt: string }>;
}>();

const { t } = useI18n();
const { language } = useSelectedLanguage();

const footerLinks = computed(
    () =>
        page.props.footerLinks as {
            workoutPlans: Record<string, { url: string; label: string }>;
            indexUrl: string;
        },
);

const APP_STORE_URL =
    'https://apps.apple.com/app/fytrr-ki-personal-trainer/id6757151695';

const structuredData = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'WebApplication',
    name: t('welcome.meta.structured_data.web_app_name'),
    description: t('welcome.meta.structured_data.description'),
    url: `https://fytrr.com${page.url}`,
    applicationCategory: 'HealthApplication',
    operatingSystem: 'Web Browser',
    offers: {
        '@type': 'AggregateOffer',
        lowPrice: '0',
        highPrice: '39.99',
        priceCurrency: 'EUR',
        offerCount: '3',
        offers: [
            {
                '@type': 'Offer',
                name: t('welcome.meta.structured_data.free_plan'),
                price: '0',
                priceCurrency: 'EUR',
                description: t(
                    'welcome.meta.structured_data.free_plan_description',
                ),
            },
            {
                '@type': 'Offer',
                name: t('welcome.meta.structured_data.monthly_plan'),
                price: '3.99',
                priceCurrency: 'EUR',
            },
            {
                '@type': 'Offer',
                name: t('welcome.meta.structured_data.annual_plan'),
                price: '39.99',
                priceCurrency: 'EUR',
            },
        ],
    },
    inLanguage: language.value === 'de' ? 'de-DE' : 'en-GB',
    author: {
        '@type': 'Organization',
        name: 'fytrr',
        url: 'https://fytrr.com',
    },
}));

const mobileAppData = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'MobileApplication',
    name: t('welcome.meta.structured_data.mobile_app_name'),
    description: t('welcome.meta.structured_data.description'),
    url: APP_STORE_URL,
    downloadUrl: APP_STORE_URL,
    applicationCategory: 'HealthApplication',
    operatingSystem: 'iOS',
    offers: {
        '@type': 'Offer',
        price: '0',
        priceCurrency: 'EUR',
        description: t('welcome.meta.structured_data.free_trial'),
    },
    inLanguage: language.value === 'de' ? 'de-DE' : 'en-GB',
    author: {
        '@type': 'Organization',
        name: 'fytrr',
        url: 'https://fytrr.com',
    },
}));

const reviewData = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: 'fytrr',
    applicationCategory: 'HealthApplication',
    operatingSystem: 'iOS',
    aggregateRating: {
        '@type': 'AggregateRating',
        ratingValue: '5',
        bestRating: '5',
        ratingCount: '6',
    },
    inLanguage: language.value === 'de' ? 'de-DE' : 'en-GB',
    review: [
        {
            '@type': 'Review',
            author: { '@type': 'Person', name: 'olele-dbld' },
            datePublished: '2026-03-28',
            reviewRating: { '@type': 'Rating', ratingValue: '5', bestRating: '5' },
            name: 'Die beste App zum zunehmen oder abnehmen',
            reviewBody:
                'Ich hatte immer Probleme mit zunehmen, mit fytrr habe ich es endlich geschafft. Man bekommt jeden Tag einen klaren Ernährungsplan und Trainingsplan.',
        },
        {
            '@type': 'Review',
            author: { '@type': 'Person', name: 'Rüblikuchen' },
            datePublished: '2026-03-27',
            reviewRating: { '@type': 'Rating', ratingValue: '5', bestRating: '5' },
            name: 'Super hilfreich',
            reviewBody:
                'Das neue Update hat die App so viel übersichtlicher gemacht. Und dass man das Workout jetzt direkt während des Trainings bearbeiten kann, ist ein absoluter Game Changer!',
        },
        {
            '@type': 'Review',
            author: { '@type': 'Person', name: 'russainboy' },
            datePublished: '2026-02-06',
            reviewRating: { '@type': 'Rating', ratingValue: '5', bestRating: '5' },
            name: 'Sehr gute App!',
            reviewBody:
                'Die KI passt die Trainingspläne intelligent an mein Fitnesslevel und meine Ziele an. Die Übungen sind verständlich erklärt, die Motivation bleibt hoch und der Fortschritt ist gut nachvollziehbar.',
        },
        {
            '@type': 'Review',
            author: { '@type': 'Person', name: 'Benedikt Kuhlmann' },
            datePublished: '2026-02-05',
            reviewRating: { '@type': 'Rating', ratingValue: '5', bestRating: '5' },
            name: 'Tolle App',
            reviewBody:
                'Seit etwa 30 Tagen teste ich diese App und bin sehr zufrieden. Sie hat mir geholfen, Gewicht zu verlieren und meine Fitness zu verbessern.',
        },
        {
            '@type': 'Review',
            author: { '@type': 'Person', name: 'Sh0owzy' },
            datePublished: '2026-02-04',
            reviewRating: { '@type': 'Rating', ratingValue: '5', bestRating: '5' },
            name: 'Top App',
            reviewBody:
                'Habe in wenigen Minuten einen personalisierten Trainingsplan erstellt bekommen.',
        },
        {
            '@type': 'Review',
            author: { '@type': 'Person', name: 'Jazzilalala' },
            datePublished: '2026-01-30',
            reviewRating: { '@type': 'Rating', ratingValue: '5', bestRating: '5' },
            name: 'Motivation pur',
            reviewBody:
                'Das Training ist abwechslungsreich und das obwohl ich nur zu Hause trainiere. Ich bin jetzt schon seit über einem Monat dabei und schon fast süchtig nach dem Training!',
        },
    ],
}));

const faqData = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: [
        {
            '@type': 'Question',
            name: t('welcome.faq.accuracy.question'),
            acceptedAnswer: {
                '@type': 'Answer',
                text: t('welcome.faq.accuracy.answer'),
            },
        },
        {
            '@type': 'Question',
            name: t('welcome.faq.dietitian.question'),
            acceptedAnswer: {
                '@type': 'Answer',
                text: t('welcome.faq.dietitian.answer'),
            },
        },
        {
            '@type': 'Question',
            name: t('welcome.faq.safety.question'),
            acceptedAnswer: {
                '@type': 'Answer',
                text: t('welcome.faq.safety.answer'),
            },
        },
        {
            '@type': 'Question',
            name: t('welcome.faq.restrictions.question'),
            acceptedAnswer: {
                '@type': 'Answer',
                text: t('welcome.faq.restrictions.answer'),
            },
        },
        {
            '@type': 'Question',
            name: t('welcome.faq.free.question'),
            acceptedAnswer: {
                '@type': 'Answer',
                text: t('welcome.faq.free.answer'),
            },
        },
        {
            '@type': 'Question',
            name: t('welcome.faq.cost.question'),
            acceptedAnswer: {
                '@type': 'Answer',
                text: t('welcome.faq.cost.answer'),
            },
        },
        {
            '@type': 'Question',
            name: t('welcome.faq.app.question'),
            acceptedAnswer: {
                '@type': 'Answer',
                text: t('welcome.faq.app.answer'),
            },
        },
    ],
}));
</script>

<template>
    <Head :title="$t('welcome.meta.title')">
        <meta name="description" :content="$t('welcome.meta.description')" />
        <link
            rel="canonical"
            :href="`https://fytrr.com/${$t('welcome.meta.locale')}`"
        />

        <!-- Open Graph -->
        <meta property="og:type" :content="$t('welcome.meta.og.type')" />
        <meta property="og:title" :content="$t('welcome.meta.title_short')" />
        <meta
            property="og:description"
            :content="$t('welcome.meta.description_social')"
        />
        <meta
            property="og:url"
            :content="`https://fytrr.com/${$t('welcome.meta.locale')}`"
        />
        <meta
            property="og:image"
            content="https://fytrr.com/fitness-plan.png"
        />
        <meta
            property="og:site_name"
            :content="$t('welcome.meta.og.site_name')"
        />

        <!-- Twitter -->
        <meta name="twitter:card" :content="$t('welcome.meta.twitter.card')" />
        <meta name="twitter:title" :content="$t('welcome.meta.title')" />
        <meta
            name="twitter:description"
            :content="$t('welcome.meta.description_social')"
        />
        <meta
            name="twitter:image"
            content="https://fytrr.com/fitness-plan.png"
        />
        <meta
            name="apple-itunes-app"
            content="app-id=6757151695, app-argument=https://fytrr.com/"
        />

        <!-- Structured Data -->
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(structuredData) }}
        </component>
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(mobileAppData) }}
        </component>
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(faqData) }}
        </component>
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(reviewData) }}
        </component>
    </Head>

    <Header />
    <main>
        <div class="h-full w-full overflow-hidden px-6 pb-10 md:px-0">
            <div
                class="container mx-auto flex flex-col justify-between space-y-10 pt-14 md:flex-row md:space-y-0"
            >
                <section class="w-full md:w-1/2 md:pt-20">
                    <article class="max-w-2xl">
                        <h1
                            class="font-display text-4xl font-bold wrap-break-word text-white md:text-6xl"
                        >
                            {{ $t('welcome.hero.title') }}
                            <span
                                class="bg-linear-to-r from-primary-400 via-primary-500 to-white bg-clip-text font-display font-bold tracking-tight text-primary-400 text-transparent"
                                >{{ $t('welcome.hero.titleHighlight') }}</span
                            >
                            {{ $t('welcome.hero.titleEnd') }}
                        </h1>

                        <p class="mt-5 text-lg text-white">
                            {{ $t('welcome.hero.subtitle') }}
                        </p>
                    </article>
                </section>

                <div class="relative flex flex-1">
                    <div
                        class="absolute inset-0 z-0 -mt-20 -mr-32 bg-[url('/assets/gradient_form_optimized.png')] bg-contain bg-center bg-no-repeat opacity-50 xl:-mt-10 xl:-mr-64 2xl:-mr-[400px]"
                        aria-hidden="true"
                    />
                    <FormCard class="flex flex-1 justify-end">
                        <GenerateFitnessPlanForm
                            :total-days="durationDays"
                            utm-content="homepage_hero"
                        />
                    </FormCard>
                </div>
            </div>

            <div class="container mx-auto mt-24">
                <div class="flex flex-col justify-between gap-8 md:flex-row">
                    <ConvinceCard
                        :headline="$t('welcome.convince.research.headline')"
                        :body="$t('welcome.convince.research.body')"
                    >
                        <template #icon>
                            <AtomIcon />
                        </template>
                    </ConvinceCard>
                    <ConvinceCard
                        :headline="$t('welcome.convince.fast.headline')"
                        :body="$t('welcome.convince.fast.body')"
                    >
                        <template #icon>
                            <TableIcon />
                        </template>
                    </ConvinceCard>
                    <ConvinceCard
                        :headline="$t('welcome.convince.goals.headline')"
                        :body="$t('welcome.convince.goals.body')"
                    >
                        <template #icon>
                            <FlagIcon />
                        </template>
                    </ConvinceCard>
                </div>
            </div>

            <div class="relative mx-auto mt-48">
                <!-- Background Gradient Image -->
                <div
                    class="pointer-events-none absolute inset-0 z-0 flex items-center justify-center"
                >
                    <img
                        src="/assets/gradient-optimized.webp"
                        width="1230"
                        height="785"
                        fetchpriority="low"
                        alt=""
                        class="h-auto w-full max-w-7xl object-contain opacity-50"
                        loading="lazy"
                        decoding="async"
                    />
                </div>

                <div class="z-10 mx-auto max-w-xl">
                    <h2
                        id="how-it-works"
                        class="text-center text-5xl font-bold tracking-tight text-white"
                    >
                        {{ $t('welcome.cta.title') }}
                    </h2>

                    <p
                        class="mt-4 text-center text-base leading-6 font-medium text-white"
                    >
                        {{ $t('welcome.cta.subtitle') }}
                    </p>
                </div>
            </div>

            <!-- Health Disclaimer -->
            <p class="mx-auto mt-8 max-w-2xl text-center text-xs leading-relaxed text-gray-500">
                {{ $t('welcome.disclaimer') }}
            </p>

            <section id="features" class="container mx-auto mt-32">
                <div class="mt-20 space-y-5 text-white">
                    <p class="text-sm text-primary-500">
                        {{ $t('welcome.features.label') }}
                    </p>
                    <h3 class="text-3xl font-bold tracking-tight text-white">
                        {{ $t('welcome.features.title') }}
                    </h3>

                    <p class="max-w-2xl text-lg text-secondary-300">
                        {{ $t('welcome.features.subtitle') }}
                    </p>
                </div>

                <div
                    class="mt-12 grid grid-cols-3 grid-rows-2 justify-between gap-8 md:flex-row"
                >
                    <FeatureCard
                        :headline="$t('welcome.features.plans.headline')"
                        :body="$t('welcome.features.plans.body')"
                        illustration-path="/assets/illustrations/plan-wireframe.svg"
                        illustration-alt="AI-generated personalized workout and meal plan interface"
                    >
                        <template #icon>
                            <FingerprintIcon />
                        </template>
                    </FeatureCard>
                    <FeatureCard
                        :headline="
                            $t('welcome.features.food_intolerances.headline')
                        "
                        :body="$t('welcome.features.food_intolerances.body')"
                        illustration-path="/assets/illustrations/food-intolerances.svg"
                        illustration-alt="Dietary restriction options including vegan, keto and allergy-free meal planning"
                        is-preview
                        preview-label="In App"
                    >
                        <template #icon>
                            <GhostIcon />
                        </template>
                    </FeatureCard>
                    <FeatureCard
                        :headline="
                            $t('welcome.features.progressive_plans.headline')
                        "
                        :body="$t('welcome.features.progressive_plans.body')"
                        illustration-path="/assets/illustrations/progressive-plans.png"
                        illustration-alt="Progressive overload workout plan adjusting intensity weekly"
                        is-preview
                        preview-label="In App"
                    >
                        <template #icon>
                            <ProgressivePlanIcon />
                        </template>
                    </FeatureCard>
                    <FeatureCard
                        :headline="$t('welcome.features.coach.headline')"
                        :body="$t('welcome.features.coach.body')"
                        illustration-path="/assets/illustrations/messenger-wireframe.png"
                        illustration-alt="Mona AI fitness coach chat interface answering nutrition questions"
                        is-preview
                        preview-label="In App"
                    >
                        <template #icon>
                            <GhostIcon />
                        </template>
                    </FeatureCard>
                    <FeatureCard
                        :headline="$t('welcome.features.tracking.headline')"
                        :body="$t('welcome.features.tracking.body')"
                        illustration-path="/assets/illustrations/tracking.png"
                        illustration-alt="Workout and nutrition progress tracking dashboard"
                        is-preview
                        preview-label="In App"
                    >
                        <template #icon>
                            <TrackingIcon />
                        </template>
                    </FeatureCard>
                    <FeatureCard
                        :headline="$t('welcome.features.shopping.headline')"
                        :body="$t('welcome.features.shopping.body')"
                        illustration-path="/assets/illustrations/shopping-cart.png"
                        illustration-alt="AI-generated grocery shopping list from personalized meal plan"
                        is-preview
                        preview-label="In App"
                    >
                        <template #icon>
                            <ShoppingBagIcon />
                        </template>
                    </FeatureCard>
                </div>
            </section>

            <section id="app" class="container mx-auto mt-32">
                <AppSection
                    :app-store-url="APP_STORE_URL"
                    qr-code="/assets/download-on-app-store-qr-code.png"
                />
            </section>

            <section id="testimonials" class="container mx-auto mt-32">
                <div class="text-center">
                    <p class="text-sm text-primary-500">
                        {{ $t('welcome.testimonials.label') }}
                    </p>
                    <h2
                        class="mt-3 font-display text-3xl font-bold tracking-tight text-white md:text-4xl"
                    >
                        {{ $t('welcome.testimonials.title') }}
                    </h2>
                    <p
                        class="mx-auto mt-4 max-w-2xl text-lg text-secondary-300"
                    >
                        {{ $t('welcome.testimonials.subtitle') }}
                    </p>
                </div>

                <div
                    class="mt-12 flex snap-x snap-mandatory gap-6 overflow-x-auto pb-4 [scrollbar-width:none] [-webkit-overflow-scrolling:touch] [&::-webkit-scrollbar]:hidden"
                >
                    <article
                        v-for="r in 6"
                        :key="r"
                        class="flex w-80 shrink-0 snap-start flex-col rounded-2xl border border-dark-surfaces-25 bg-dark-surfaces-900 p-8 md:w-96"
                    >
                        <div class="flex items-center gap-1 text-yellow-400">
                            <svg
                                v-for="n in 5"
                                :key="n"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="h-5 w-5"
                            >
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-white">
                            {{ $t(`welcome.testimonials.review${r}.title`) }}
                        </h3>
                        <p
                            class="mt-2 flex-1 text-sm leading-relaxed text-secondary-300"
                        >
                            {{ $t(`welcome.testimonials.review${r}.body`) }}
                        </p>
                        <div class="mt-4 text-sm text-secondary-300">
                            <span class="font-medium text-white">
                                {{ $t(`welcome.testimonials.review${r}.author`) }}
                            </span>
                            &middot;
                            {{ $t(`welcome.testimonials.review${r}.date`) }}
                        </div>
                    </article>
                </div>
            </section>

            <!-- From Our Blog -->
            <section
                id="blog"
                v-if="props.blogPosts.length"
                class="container mx-auto mt-32"
            >
                <header class="text-center">
                    <h2
                        class="font-display text-3xl font-bold tracking-tight text-white"
                    >
                        {{ $t('welcome.blog.title') }}
                    </h2>
                    <p class="mt-2 text-gray-400">
                        {{ $t('welcome.blog.subtitle') }}
                    </p>
                </header>
                <div
                    class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3"
                >
                    <Link
                        v-for="post in props.blogPosts"
                        :key="post.url"
                        :href="post.url"
                        class="overflow-hidden rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800 transition hover:border-primary-500/30"
                    >
                        <img
                            v-if="post.image"
                            :src="post.image"
                            :alt="post.imageAlt"
                            width="600"
                            height="315"
                            loading="lazy"
                            decoding="async"
                            class="aspect-[1.91/1] w-full object-cover"
                        />
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-white">
                                {{ post.title }}
                            </h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-400">
                                {{ post.description }}
                            </p>
                            <span
                                class="mt-4 inline-block text-sm font-medium text-primary-400"
                            >
                                {{ $t('welcome.blog.readMore') }} &rarr;
                            </span>
                        </div>
                    </Link>
                </div>
            </section>

            <section id="faq" class="container mx-auto">
                <div class="mt-32 text-white">
                    <header class="text-center">
                        <h2
                            class="font-display text-3xl font-bold tracking-tight text-white"
                        >
                            {{ $t('welcome.faq.title') }}
                        </h2>
                        <p>
                            {{ $t('welcome.faq.contact') }}
                            <a href="mailto:hello@fytrr.com"
                                ><strong>{{
                                    $t('welcome.faq.contactLink')
                                }}</strong></a
                            >.
                        </p>
                    </header>

                    <div
                        class="mx-auto mt-10 max-w-5xl divide-y divide-dark-surfaces-25"
                    >
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                {{ $t('welcome.faq.accuracy.question') }}
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                {{ $t('welcome.faq.accuracy.answer') }}
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                {{ $t('welcome.faq.dietitian.question') }}
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                {{ $t('welcome.faq.dietitian.answer') }}
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                {{ $t('welcome.faq.safety.question') }}
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                {{ $t('welcome.faq.safety.answer') }}
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                {{ $t('welcome.faq.restrictions.question') }}
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                {{ $t('welcome.faq.restrictions.answer') }}
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                {{ $t('welcome.faq.free.question') }}
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                {{ $t('welcome.faq.free.answer') }}
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                {{ $t('welcome.faq.cost.question') }}
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                {{ $t('welcome.faq.cost.answer') }}
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                {{ $t('welcome.faq.app.question') }}
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                {{ $t('welcome.faq.app.answer') }}

                                <a
                                    :href="APP_STORE_URL"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="font-medium text-primary-25 underline transition-colors hover:text-primary-300"
                                >
                                    {{ $t('welcome.faq.app.download_link') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <nav
            aria-label="Popular workout plans"
            class="container mx-auto mt-28 px-6 md:px-0"
        >
            <div
                class="mx-auto flex max-w-3xl flex-col items-center gap-5 rounded-3xl border border-dark-surfaces-25 bg-dark-surfaces-900/50 px-6 py-8"
            >
                <p
                    class="text-xs font-semibold tracking-wider text-primary-500 uppercase"
                >
                    {{ $t('internalLinks.popularHeading') }}
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <Link
                        :href="footerLinks.workoutPlans.weight_loss?.url"
                        class="rounded-full border border-dark-surfaces-25 bg-dark-surfaces-800 px-4 py-2 text-sm font-medium text-secondary-200 transition hover:border-primary-500/40 hover:text-white"
                        >{{ $t('internalLinks.weightLossAnchor') }}</Link
                    >
                    <Link
                        :href="footerLinks.workoutPlans.home?.url"
                        class="rounded-full border border-dark-surfaces-25 bg-dark-surfaces-800 px-4 py-2 text-sm font-medium text-secondary-200 transition hover:border-primary-500/40 hover:text-white"
                        >{{ $t('internalLinks.homeAnchor') }}</Link
                    >
                    <Link
                        :href="footerLinks.workoutPlans.muscle_gain?.url"
                        class="rounded-full border border-dark-surfaces-25 bg-dark-surfaces-800 px-4 py-2 text-sm font-medium text-secondary-200 transition hover:border-primary-500/40 hover:text-white"
                        >{{ $t('internalLinks.muscleGainAnchor') }}</Link
                    >
                    <Link
                        :href="footerLinks.indexUrl"
                        class="inline-flex items-center gap-1 px-4 py-2 text-sm font-semibold text-primary-400 transition hover:text-primary-300"
                        >{{ $t('internalLinks.allPlans') }} &rarr;</Link
                    >
                </div>
            </div>
        </nav>
    </main>
    <Footer />
</template>
