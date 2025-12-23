<script setup lang="ts">
import ConvinceCard from '@/components/ConvinceCard.vue';
import FeatureCard from '@/components/FeatureCard.vue';
import FormCard from '@/components/FormCard.vue';
import GenerateFitnessPlanForm from '@/components/GenerateFitnessPlanForm.vue';
import AtomIcon from '@/components/icons/AtomIcon.vue';
import FingerprintIcon from '@/components/icons/FingerprintIcon.vue';
import FlagIcon from '@/components/icons/FlagIcon.vue';
import GhostIcon from '@/components/icons/GhostIcon.vue';
import ShoppingBagIcon from '@/components/icons/ShoppingBagIcon.vue';
import TableIcon from '@/components/icons/TableIcon.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps<{
    durationDays: number;
}>();

const {t} = useI18n()

const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'WebApplication',
    name: 'FitnessAI',
    description:
        t('welcome.meta.structured_data.description'),
    url: 'https://fitnessai.me',
    applicationCategory: 'HealthApplication',
    operatingSystem: 'Web Browser',
    offers: {
        '@type': 'Offer',
        price: '3.99',
        priceCurrency: 'EUR',
    },
};
</script>

<template>
    <Head :title="$t('welcome.meta.title')">
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(structuredData) }}
        </component>
    </Head>

    <header class="w-full border-b border-dark-surfaces-25">
        <nav class="container mx-auto px-3 py-4">
            <Link href="/">
                <img
                    class="h-auto w-40"
                    src="/fitness-ai-me-logo.svg"
                    alt="fitnessAi.me logo"
                />
            </Link>
        </nav>
    </header>
    <main>
        <div class="h-full w-full px-6 pb-10 md:px-0">
            <div
                class="container mx-auto flex flex-col justify-between space-y-10 pt-14 md:flex-row md:space-y-0"
            >
                <section class="w-full md:w-1/2 md:pt-20">
                    <article class="max-w-2xl">
                        <h1 class="font-display text-4xl md:text-6xl font-bold text-white wrap-break-word">
                            {{ $t('welcome.hero.title') }}
                            <span
                                class="bg-gradient-to-r from-primary-400 via-primary-500 to-white bg-clip-text font-display font-bold tracking-tight text-primary-400 text-transparent"
                                >{{ $t('welcome.hero.titleHighlight') }}</span
                            >
                            {{ $t('welcome.hero.titleEnd') }}
                        </h1>

                        <p class="mt-5 text-lg text-white">
                            {{ $t('welcome.hero.subtitle') }}
                        </p>
                    </article>
                </section>

                <FormCard class="flex flex-1 justify-end">
                    <GenerateFitnessPlanForm :total-days="durationDays" />
                </FormCard>
            </div>

            <div class="container mx-auto mt-20">
                <div class="flex flex-col justify-between gap-4 md:flex-row">
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
                <div class="absolute inset-0 flex items-center justify-center">
                    <img
                        src="/assets/gradient.png"
                        alt=""
                        class="h-auto w-full max-w-7xl object-contain opacity-50"
                    />
                </div>

                <div class="mx-auto max-w-xl">
                    <h2
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

            <section id="features" class="container mx-auto mt-32">
                <div class="mt-20 space-y-5 text-white">
                    <p class="text-sm text-primary-500">{{ $t('welcome.features.label') }}</p>
                    <h3 class="text-3xl font-bold tracking-tight text-white">
                        {{ $t('welcome.features.title') }}
                    </h3>

                    <p class="max-w-2xl text-lg text-secondary-300">
                        {{ $t('welcome.features.subtitle') }}
                    </p>
                </div>

                <div
                    class="mt-12 flex flex-col justify-between gap-4 md:flex-row"
                >
                    <FeatureCard
                        :headline="$t('welcome.features.plans.headline')"
                        :body="$t('welcome.features.plans.body')"
                        illustration-path="/assets/illustrations/plan-wireframe.svg"
                    >
                        <template #icon>
                            <FingerprintIcon />
                        </template>
                    </FeatureCard>

                    <FeatureCard
                        :headline="$t('welcome.features.shopping.headline')"
                        :body="$t('welcome.features.shopping.body')"
                        illustration-path="/assets/illustrations/research-paper.svg"
                        is-preview
                    >
                        <template #icon>
                            <ShoppingBagIcon />
                        </template>
                    </FeatureCard>
                    <FeatureCard
                        :headline="$t('welcome.features.coach.headline')"
                        :body="$t('welcome.features.coach.body')"
                        illustration-path="/assets/illustrations/food-intolerances.svg"
                        is-preview
                    >
                        <template #icon>
                            <GhostIcon />
                        </template>
                    </FeatureCard>
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
                            <a href="mailto:hello@fitnessai.me"
                                ><strong>{{ $t('welcome.faq.contactLink') }}</strong></a
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
                    </div>
                </div>
            </section>
        </div>
    </main>
    <footer class="bg-dark-surfaces-800 p-8 text-primary-200">
        <nav class="container mx-auto flex justify-between">
            <Link href="/">fitnessai.me</Link>
            <Link href="/imprint">{{ $t('welcome.footer.imprint') }}</Link>
        </nav>
    </footer>
</template>
