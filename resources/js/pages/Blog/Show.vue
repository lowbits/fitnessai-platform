<script setup lang="ts">
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import AuthorBox from '@/components/workoutPlan/AuthorBox.vue';
import FAQSection from '@/components/workoutPlan/FAQSection.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface Section {
    heading: string;
    content: string;
}

interface FAQ {
    question: string;
    answer: string;
}

interface Author {
    name: string;
    title: string;
    bio: string;
    image: string;
}

interface Article {
    title: string;
    description: string;
    h1: string;
    keywords: string[];
    internal_slug: string;
    published_at: string;
    last_updated_at: string;
    intro: string;
    sections: Section[];
    faqs: FAQ[];
    calorie_calculator_slug?: string;
    seo_image?: string;
    seo_image_alt?: string;
}

interface Props {
    meta: {
        title: string;
        description: string;
        canonical: string;
        keywords: string[];
        ogImage?: string;
    };
    article: Article;
    author: Author;
    alternateUrls: Record<string, string>;
    schema: object[];
    publishedAt: string;
    lastUpdatedAt: string;
}

const props = defineProps<Props>();

const { t, locale } = useI18n();

const schemaJson = computed(() => props.schema.map((s) => JSON.stringify(s)));

const calculatorUrl = computed(
    () => `/${locale.value}/${props.article.calorie_calculator_slug}`,
);
</script>

<template>
    <Head :title="meta.title">
        <meta name="description" :content="meta.description" />
        <link rel="canonical" :href="meta.canonical" />
        <meta
            v-if="meta.keywords?.length"
            name="keywords"
            :content="meta.keywords.join(', ')"
        />
        <meta property="og:title" :content="meta.title" />
        <meta property="og:description" :content="meta.description" />
        <meta property="og:url" :content="meta.canonical" />
        <meta property="og:type" content="article" />
        <meta v-if="meta.ogImage" property="og:image" :content="meta.ogImage" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="meta.title" />
        <meta name="twitter:description" :content="meta.description" />
        <meta
            v-if="meta.ogImage"
            name="twitter:image"
            :content="meta.ogImage"
        />
        <meta
            v-if="meta.ogImageAlt"
            property="og:image:alt"
            :content="meta.ogImageAlt"
        />
        <link
            v-for="(url, loc) in alternateUrls"
            :key="loc"
            rel="alternate"
            :hreflang="loc"
            :href="url"
        />

        <component
            v-for="(schema, i) in schemaJson"
            :key="i"
            :is="'script'"
            type="application/ld+json"
        >
            {{ schema }}
        </component>
    </Head>

    <GuestLayout>
        <div class="bg-dark-surfaces-900">
            <!-- Hero -->
            <section class="px-4 pt-12 pb-8 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <p class="text-sm font-medium text-primary-400">
                        {{ t('blog.label') }}
                    </p>
                    <h1
                        data-speakable="headline"
                        class="mt-2 font-display text-3xl font-bold text-white sm:text-4xl lg:text-5xl"
                    >
                        {{ article.h1 }}
                    </h1>
                    <div
                        class="mt-4 flex items-center gap-4 text-sm text-gray-400"
                    >
                        <span>{{ author.name }}</span>
                        <span>&middot;</span>
                        <time :datetime="article.published_at">
                            {{ publishedAt }}
                        </time>
                    </div>
                </div>
            </section>

            <!-- Featured Image -->
            <section
                v-if="article.seo_image"
                class="px-4 pb-8 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <img
                        :src="article.seo_image"
                        :alt="article.seo_image_alt ?? article.h1"
                        class="w-full rounded-2xl"
                        loading="eager"
                    />
                </div>
            </section>

            <!-- Article Content -->
            <section class="px-4 pb-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <article class="prose prose-invert max-w-none">
                        <!-- Intro -->
                        <p
                            data-speakable="summary"
                            class="text-lg leading-relaxed text-gray-300"
                        >
                            {{ article.intro }}
                        </p>

                        <!-- Calculator CTA inline -->
                        <div
                            v-if="article.calorie_calculator_slug"
                            class="my-8 rounded-xl border border-primary-500/20 bg-primary-500/5 p-5"
                        >
                            <p class="text-sm font-medium text-white">
                                {{ t('blog.calculatorCta') }}
                            </p>
                            <a
                                :href="calculatorUrl"
                                class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-primary-400 transition hover:text-primary-300"
                            >
                                {{ t('blog.calculatorCtaLink') }}
                                &rarr;
                            </a>
                        </div>

                        <!-- Sections -->
                        <template
                            v-for="(section, i) in article.sections"
                            :key="i"
                        >
                            <h2
                                class="mt-10 font-display text-2xl font-bold text-white"
                            >
                                {{ section.heading }}
                            </h2>
                            <p class="leading-relaxed text-gray-300">
                                {{ section.content }}
                            </p>
                        </template>
                    </article>

                    <!-- App CTA -->
                    <div
                        class="mt-12 rounded-2xl border border-primary-500/20 bg-primary-500/5 p-8 text-center"
                    >
                        <p class="text-lg font-semibold text-white">
                            {{ t('blog.appCtaHeadline') }}
                        </p>
                        <p class="mt-2 text-sm text-gray-300">
                            {{ t('blog.appCtaText') }}
                        </p>
                        <GenerateFitnessPlanModal
                            utm-content="blog_article"
                            :utm-campaign="`blog_${article.internal_slug}`"
                            #default="{ open }"
                        >
                            <button
                                @click="open"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-base font-semibold text-dark-surfaces-900 transition hover:bg-primary-400"
                            >
                                {{ t('blog.appCtaButton') }}
                            </button>
                        </GenerateFitnessPlanModal>
                    </div>

                    <!-- Author Box -->
                    <div class="mt-12">
                        <AuthorBox
                            :author="author"
                            :last-updated="lastUpdatedAt"
                            :show-disclosure="true"
                        />
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <FAQSection
                v-if="article.faqs.length"
                :faqs="article.faqs"
                :heading="t('blog.faqHeading')"
                class="rounded-2xl"
            />

            <!-- Sources -->
            <section
                v-if="article.sources?.length"
                class="mx-auto max-w-3xl px-4 pb-16 sm:px-6 lg:px-8"
            >
                <h2
                    class="mb-4 text-lg font-semibold text-white"
                >
                    {{ t('blog.sourcesHeading') }}
                </h2>
                <ol class="list-decimal space-y-2 pl-5 text-sm text-gray-400">
                    <li v-for="(source, i) in article.sources" :key="i">
                        <a
                            v-if="source.url"
                            :href="source.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="underline transition hover:text-primary-300"
                        >
                            {{ source.label }}
                        </a>
                        <span v-else>{{ source.label }}</span>
                    </li>
                </ol>
            </section>
        </div>
    </GuestLayout>
</template>
