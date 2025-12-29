<script setup lang="ts">
import Footer from '@/components/Footer.vue';
import Header from '@/components/Header.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    plans: Array<{
        type: string;
        title: string;
        description: string;
        url: string;
    }>;
    meta: {
        title: string;
        description: string;
        canonical: string;
    };
    alternateUrls: {
        de: string;
        en: string;
    };
    labels: {
        heading: string;
        intro: string;
        viewPlan: string;
        ctaHeading: string;
        ctaText: string;
        ctaButton: string;
    };
}

const props = defineProps<Props>();
</script>

<template>
    <div class="min-h-screen bg-dark-surfaces-900">
        <Head>
            <title>{{ props.meta.title }}</title>
            <meta name="description" :content="props.meta.description" />
            <link rel="canonical" :href="props.meta.canonical" />

            <!-- Open Graph -->
            <meta property="og:type" content="website" />
            <meta property="og:title" :content="props.meta.title" />
            <meta property="og:description" :content="props.meta.description" />
            <meta property="og:url" :content="props.meta.canonical" />

            <!-- Twitter -->
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="props.meta.title" />
            <meta
                name="twitter:description"
                :content="props.meta.description"
            />
            <meta
                property="og:image"
                content="https://fytrr.com/fitness-plan.png"
            />
        </Head>

        <Header />

        <main class="container mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <section class="mb-16 text-center">
                <h1
                    class="text-4xl font-bold text-white md:text-5xl lg:text-6xl"
                >
                    {{ props.labels.heading }}
                </h1>
                <p class="mx-auto mt-4 max-w-3xl text-lg text-gray-300">
                    {{ props.labels.intro }}
                </p>
            </section>

            <!-- Plans Grid -->
            <section class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="plan in props.plans"
                    :key="plan.type"
                    class="group relative overflow-hidden rounded-2xl bg-dark-surfaces-800 p-6 transition-all hover:bg-dark-surfaces-500"
                >
                    <div class="relative z-10">
                        <h2 class="mb-3 text-2xl font-bold text-white">
                            {{ plan.title }}
                        </h2>
                        <p class="mb-6 line-clamp-3 text-gray-300">
                            {{ plan.description }}
                        </p>
                        <Link
                            :href="plan.url"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary-500 px-6 py-3 font-semibold text-white transition hover:bg-primary-400"
                        >
                            {{ props.labels.viewPlan }}
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"
                                />
                            </svg>
                        </Link>
                    </div>

                    <!-- Hover Effect -->
                    <div
                        class="absolute inset-0 z-0 bg-gradient-to-br from-primary-500/10 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
                    />
                </div>
            </section>

            <!-- CTA Section -->
            <section
                class="mt-16 rounded-2xl bg-gradient-to-r from-primary-500/20 to-primary-400/10 p-8 text-center"
            >
                <h2 class="mb-4 text-3xl font-bold text-white">
                    {{ props.labels.ctaHeading }}
                </h2>
                <p class="mx-auto mb-6 max-w-2xl text-gray-300">
                    {{ props.labels.ctaText }}
                </p>

                <GenerateFitnessPlanModal #default="{ open }">
                    <Button @click="open">{{ props.labels.ctaButton }}</Button>
                </GenerateFitnessPlanModal>
            </section>
        </main>

        <Footer />
    </div>
</template>
