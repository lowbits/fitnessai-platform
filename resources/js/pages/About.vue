<script setup lang="ts">
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    meta: {
        title: string;
        description: string;
        canonical: string;
        keywords: string[];
        ogImage: string;
        ogImageAlt: string;
    };
    alternateUrls: Record<string, string>;
    schema: object;
    appStoreUrl: string;
    authorImage: string;
}

const props = defineProps<Props>();
const { t } = useI18n();

const schemaJson = computed(() => JSON.stringify(props.schema));
</script>

<template>
    <Head :title="meta.title">
        <meta name="description" :content="meta.description" />
        <link rel="canonical" :href="meta.canonical" />
        <meta name="keywords" :content="meta.keywords.join(', ')" />
        <meta property="og:title" :content="meta.title" />
        <meta property="og:description" :content="meta.description" />
        <meta property="og:url" :content="meta.canonical" />
        <meta property="og:type" content="website" />
        <meta property="og:image" :content="meta.ogImage" />
        <meta property="og:image:alt" :content="meta.ogImageAlt" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="meta.title" />
        <meta name="twitter:description" :content="meta.description" />
        <meta name="twitter:image" :content="meta.ogImage" />
        <link
            v-for="(url, loc) in alternateUrls"
            :key="loc"
            rel="alternate"
            :hreflang="loc"
            :href="url"
        />
        <component :is="'script'" type="application/ld+json">
            {{ schemaJson }}
        </component>
    </Head>

    <GuestLayout>
        <div class="bg-dark-surfaces-900">
            <!-- Hero -->
            <section class="px-4 pt-16 pb-12 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <h1
                        class="font-display text-3xl font-bold text-white sm:text-4xl lg:text-5xl"
                    >
                        {{ t('about.hero.h1') }}
                    </h1>
                    <p class="mt-6 text-lg leading-relaxed text-gray-300">
                        {{ t('about.hero.subline') }}
                    </p>
                </div>
            </section>

            <!-- My Story -->
            <section class="px-4 pb-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2
                        class="font-display text-2xl font-bold text-white sm:text-3xl"
                    >
                        {{ t('about.story.heading') }}
                    </h2>
                    <div class="mt-6 flex flex-col gap-8 sm:flex-row">
                        <img
                            :src="authorImage"
                            alt="Tobias Lobitz — Founder of Fytrr"
                            class="h-48 w-48 shrink-0 self-start rounded-2xl border-2 border-primary-500/20 object-cover sm:h-56 sm:w-56"
                            itemscope
                            itemtype="https://schema.org/Person"
                            itemprop="image"
                        />
                        <div
                            class="space-y-4 text-base leading-relaxed text-gray-300"
                        >
                            <p>{{ t('about.story.p1') }}</p>
                            <p>{{ t('about.story.p2') }}</p>
                            <p>{{ t('about.story.p3') }}</p>
                            <p>{{ t('about.story.p4') }}</p>
                            <p>{{ t('about.story.p5') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Why Fytrr -->
            <section class="px-4 pb-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2
                        class="font-display text-2xl font-bold text-white sm:text-3xl"
                    >
                        {{ t('about.why.heading') }}
                    </h2>
                    <div class="mt-8 space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-primary-400">
                                {{ t('about.why.problemHeading') }}
                            </h3>
                            <p class="mt-2 leading-relaxed text-gray-300">
                                {{ t('about.why.problemText') }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-primary-400">
                                {{ t('about.why.ideaHeading') }}
                            </h3>
                            <p class="mt-2 leading-relaxed text-gray-300">
                                {{ t('about.why.ideaText') }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-primary-400">
                                {{ t('about.why.resultHeading') }}
                            </h3>
                            <p class="mt-2 leading-relaxed text-gray-300">
                                {{ t('about.why.resultText') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section class="px-4 pb-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2
                        class="font-display text-2xl font-bold text-white sm:text-3xl"
                    >
                        {{ t('about.features.heading') }}
                    </h2>
                    <ul class="mt-6 space-y-4">
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-400"
                            ></span>
                            <p class="text-gray-300">
                                <strong class="text-white">{{
                                    t('about.features.workoutPlan')
                                }}</strong>
                                —
                                {{ t('about.features.workoutPlanText') }}
                            </p>
                        </li>
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-400"
                            ></span>
                            <p class="text-gray-300">
                                <strong class="text-white">{{
                                    t('about.features.nutritionPlan')
                                }}</strong>
                                —
                                {{ t('about.features.nutritionPlanText') }}
                            </p>
                        </li>
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-400"
                            ></span>
                            <p class="text-gray-300">
                                <strong class="text-white">{{
                                    t('about.features.swapMeals')
                                }}</strong>
                                —
                                {{ t('about.features.swapMealsText') }}
                            </p>
                        </li>
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-400"
                            ></span>
                            <p class="text-gray-300">
                                <strong class="text-white">{{
                                    t('about.features.tracking')
                                }}</strong>
                                —
                                {{ t('about.features.trackingText') }}
                            </p>
                        </li>
                        <li class="flex items-start gap-3">
                            <span
                                class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-400"
                            ></span>
                            <p class="text-gray-300">
                                <strong class="text-white">{{
                                    t('about.features.aiAdaptation')
                                }}</strong>
                                —
                                {{ t('about.features.aiAdaptationText') }}
                            </p>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Stats -->
            <section class="px-4 pb-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2
                        class="font-display text-2xl font-bold text-white sm:text-3xl"
                    >
                        {{ t('about.stats.heading') }}
                    </h2>
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800 p-6 text-center"
                        >
                            <p
                                class="font-display text-2xl font-bold text-primary-400"
                            >
                                1.100+
                            </p>
                            <p class="mt-1 text-sm text-gray-400">
                                {{ t('about.stats.users') }}
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800 p-6 text-center"
                        >
                            <p
                                class="font-display text-2xl font-bold text-primary-400"
                            >
                                iOS
                            </p>
                            <p class="mt-1 text-sm text-gray-400">App Store</p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800 p-6 text-center"
                        >
                            <p
                                class="font-display text-2xl font-bold text-primary-400"
                            >
                                3,99&nbsp;&euro;
                            </p>
                            <p class="mt-1 text-sm text-gray-400">
                                {{ t('about.stats.pricePerMonth') }}
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800 p-6 text-center"
                        >
                            <p
                                class="font-display text-2xl font-bold text-primary-400"
                            >
                                1
                            </p>
                            <p class="mt-1 text-sm text-gray-400">
                                {{ t('about.stats.developerMission') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="px-4 pb-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <div
                        class="rounded-2xl border border-primary-500/20 bg-primary-500/5 p-8 text-center"
                    >
                        <h2
                            class="font-display text-2xl font-bold text-white sm:text-3xl"
                        >
                            {{ t('about.cta.heading') }}
                        </h2>
                        <GenerateFitnessPlanModal
                            utm-content="about_page"
                            utm-campaign="about"
                            #default="{ open }"
                        >
                            <button
                                @click="open"
                                class="mt-6 inline-flex items-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-base font-semibold text-dark-surfaces-900 transition hover:bg-primary-400"
                            >
                                {{ t('about.cta.button') }}
                            </button>
                        </GenerateFitnessPlanModal>
                        <div class="mt-4">
                            <a
                                :href="appStoreUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm text-gray-400 transition hover:text-primary-300"
                            >
                                {{ t('about.cta.appStoreLink') }} &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>
