<script setup lang="ts">
import Footer from '@/components/Footer.vue';
import Header from '@/components/Header.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';

interface Post {
    slug: string;
    title: string;
    description: string;
    url: string;
    og_image: string | null;
    og_image_alt: string;
    published_at: string;
}

interface Author {
    name: string;
    title: string;
    bio: string;
    image: string;
}

interface Props {
    posts: Post[];
    author: Author;
    meta: {
        title: string;
        description: string;
        canonical: string;
    };
    alternateUrls: Record<string, string>;
    labels: {
        heading: string;
        readMore: string;
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
            <meta property="og:type" content="website" />
            <meta property="og:title" :content="props.meta.title" />
            <meta property="og:description" :content="props.meta.description" />
            <meta property="og:url" :content="props.meta.canonical" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="props.meta.title" />
            <meta
                name="twitter:description"
                :content="props.meta.description"
            />
            <link
                v-for="(url, loc) in props.alternateUrls"
                :key="loc"
                rel="alternate"
                :hreflang="loc"
                :href="url"
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
            </section>

            <!-- Posts Grid -->
            <section class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="post in props.posts"
                    :key="post.slug"
                    :href="post.url"
                    class="group relative overflow-hidden rounded-2xl bg-dark-surfaces-800 transition-all hover:bg-dark-surfaces-500"
                >
                    <img
                        v-if="post.og_image"
                        :src="post.og_image"
                        :alt="post.og_image_alt || post.title"
                        class="aspect-video w-full object-cover"
                    />
                    <div
                        v-else
                        class="bg-dark-surfaces-700 aspect-video w-full"
                    />
                    <div class="p-6">
                        <p class="mb-2 text-sm text-gray-400">
                            {{ post.published_at }}
                        </p>
                        <h2 class="mb-3 text-xl font-bold text-white">
                            {{ post.title }}
                        </h2>
                        <p class="mb-4 line-clamp-3 text-gray-300">
                            {{ post.description }}
                        </p>
                        <span
                            class="inline-flex items-center gap-2 font-semibold text-primary-400 transition group-hover:text-primary-300"
                        >
                            {{ props.labels.readMore }}
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
                        </span>
                    </div>

                    <!-- Hover Effect -->
                    <div
                        class="absolute inset-0 z-0 bg-gradient-to-br from-primary-500/10 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
                    />
                </Link>
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

                <GenerateFitnessPlanModal
                    utm-content="blog_index_cta"
                    utm-campaign="blog_index"
                    #default="{ open }"
                >
                    <Button @click="open">{{ props.labels.ctaButton }}</Button>
                </GenerateFitnessPlanModal>
            </section>
        </main>

        <Footer />
    </div>
</template>
