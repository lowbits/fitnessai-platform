<script setup lang="ts">
import AppUpsellBanner from '@/components/AppUpsellBanner.vue';
import BaseButton from '@/components/Base/BaseButton.vue';
import FaqCard from '@/components/Base/FaqCard.vue';
import SectionHeader from '@/components/Base/SectionHeader.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

interface Term {
    slug: string;
    term: string;
    expansion: string;
    definition: string;
    example: string;
    category: string;
}

interface Category {
    id: string;
    label: string;
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
    hero: { eyebrow: string; h1: string; subtitle: string };
    ui: {
        search_placeholder: string;
        no_results: string;
        all: string;
        example: string;
        related_heading: string;
        filter_label: string;
    };
    categories: Category[];
    terms: Term[];
    faqs: { question: string; answer: string }[];
    cta: { headline: string; button: string; trust: string };
    internalLinks: { id: string; label: string; url: string }[];
}

const props = defineProps<Props>();
const page = usePage<{ footerLinks: { appStoreUrl: string } }>();

const appStoreUrl = computed(() => page.props.footerLinks?.appStoreUrl ?? '#');
const schemaJson = computed(() => props.schema.map((s) => JSON.stringify(s)));

const query = ref('');
const activeCategory = ref<'all' | string>('all');

const categoryIds = computed(() => props.categories.map((c) => c.id));

onMounted(() => {
    const tab = new URLSearchParams(window.location.search).get('tab');
    if (tab && categoryIds.value.includes(tab)) {
        activeCategory.value = tab;
    }
});

watch(activeCategory, (value) => {
    const url = new URL(window.location.href);
    if (value === 'all') {
        url.searchParams.delete('tab');
    } else {
        url.searchParams.set('tab', value);
    }
    window.history.replaceState(window.history.state, '', url);
});

const normalized = (value: string): string =>
    value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');

const filtered = computed<Term[]>(() => {
    const q = normalized(query.value.trim());
    return props.terms.filter((term) => {
        const inCategory =
            activeCategory.value === 'all' ||
            term.category === activeCategory.value;
        if (!inCategory) return false;
        if (!q) return true;
        return normalized(
            `${term.term} ${term.expansion} ${term.definition}`,
        ).includes(q);
    });
});

interface Group {
    id: string;
    label: string;
    items: Term[];
}

const groups = computed<Group[]>(() =>
    props.categories
        .map((category) => ({
            id: category.id,
            label: category.label,
            items: filtered.value.filter(
                (term) => term.category === category.id,
            ),
        }))
        .filter((group) => group.items.length > 0),
);

const hasResults = computed(() => filtered.value.length > 0);

function countFor(id: string): number {
    return props.terms.filter((term) => id === 'all' || term.category === id)
        .length;
}
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
                <section class="pt-14 pb-8 lg:pt-20">
                    <SectionHeader
                        as="h1"
                        class="items-center text-center"
                        :eyebrow="hero.eyebrow"
                        :title="hero.h1"
                        :subtitle="hero.subtitle"
                    />
                </section>

                <!-- Search + category filter (sticky) -->
                <div
                    class="sticky top-0 z-10 -mx-6 bg-canvas/85 px-6 py-4 backdrop-blur sm:-mx-8 sm:px-8 lg:-mx-[120px] lg:px-[120px]"
                >
                    <div class="mx-auto max-w-2xl">
                        <div class="relative">
                            <Search
                                class="pointer-events-none absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-ink-muted"
                            />
                            <input
                                v-model="query"
                                type="search"
                                :placeholder="ui.search_placeholder"
                                :aria-label="ui.search_placeholder"
                                class="w-full rounded-2xl border border-stroke bg-surface py-4 pr-4 pl-12 text-ink transition-colors placeholder:text-ink-muted focus:border-brand focus:outline-none"
                            />
                        </div>
                    </div>

                    <div
                        class="mt-4 flex flex-wrap justify-center gap-2"
                        role="group"
                        :aria-label="ui.filter_label"
                    >
                        <button
                            type="button"
                            :aria-pressed="activeCategory === 'all'"
                            class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
                            :class="
                                activeCategory === 'all'
                                    ? 'border-transparent bg-ink text-canvas'
                                    : 'border-stroke bg-surface text-ink-muted hover:text-ink'
                            "
                            @click="activeCategory = 'all'"
                        >
                            {{ ui.all }}
                            <span class="opacity-60">{{ countFor('all') }}</span>
                        </button>
                        <button
                            v-for="category in categories"
                            :key="category.id"
                            type="button"
                            :aria-pressed="activeCategory === category.id"
                            class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
                            :class="
                                activeCategory === category.id
                                    ? 'border-transparent bg-ink text-canvas'
                                    : 'border-stroke bg-surface text-ink-muted hover:text-ink'
                            "
                            @click="activeCategory = category.id"
                        >
                            {{ category.label }}
                            <span class="opacity-60">{{
                                countFor(category.id)
                            }}</span>
                        </button>
                    </div>
                </div>

                <!-- Terms -->
                <section class="pt-8 pb-16">
                    <p
                        v-if="!hasResults"
                        class="py-16 text-center text-ink-muted"
                    >
                        {{ ui.no_results }}
                    </p>

                    <div
                        v-for="group in groups"
                        :key="group.id"
                        class="mb-14 last:mb-0"
                    >
                        <h2
                            class="text-xs font-semibold tracking-[0.16em] text-ink-muted uppercase"
                        >
                            {{ group.label }}
                        </h2>
                        <div
                            class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <article
                                v-for="term in group.items"
                                :id="term.slug"
                                :key="term.slug"
                                class="scroll-mt-40 rounded-2xl border border-stroke bg-surface p-6 transition-colors hover:border-brand/50"
                            >
                                <h3
                                    class="font-grotesk text-lg font-bold text-ink"
                                >
                                    {{ term.term }}
                                </h3>
                                <p
                                    v-if="term.expansion"
                                    class="mt-0.5 text-sm text-ink-muted"
                                >
                                    {{ term.expansion }}
                                </p>
                                <p class="mt-3 leading-relaxed text-ink">
                                    {{ term.definition }}
                                </p>
                                <p
                                    v-if="term.example"
                                    class="mt-4 rounded-xl bg-surface-raised px-4 py-3 text-sm leading-relaxed text-ink-muted"
                                >
                                    <span
                                        class="mr-1 font-semibold text-brand"
                                        >{{ ui.example }}:</span
                                    >{{ term.example }}
                                </p>
                            </article>
                        </div>
                    </div>
                </section>

                <!-- CTA: get the app -->
                <section class="pb-16">
                    <div
                        class="flex flex-col gap-6 rounded-[24px] border border-brand/40 bg-brand/5 p-8 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <p class="max-w-lg text-2xl font-bold text-ink">
                            {{ cta.headline }}
                        </p>
                        <div class="flex shrink-0 flex-col items-start gap-2">
                            <BaseButton
                                as="a"
                                size="lg"
                                :href="appStoreUrl"
                                target="_blank"
                                rel="noopener"
                            >
                                {{ cta.button }}
                                <span aria-hidden="true">&rarr;</span>
                            </BaseButton>
                            <p class="text-sm text-ink-muted">
                                {{ cta.trust }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- FAQ -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <FaqCard
                            v-for="faq in faqs"
                            :key="faq.question"
                            :question="faq.question"
                            :answer="faq.answer"
                        />
                    </div>
                </section>

                <!-- Related tools -->
                <section class="border-t border-stroke py-16 lg:py-[96px]">
                    <h2 class="text-2xl font-bold text-ink">
                        {{ ui.related_heading }}
                    </h2>
                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <a
                            v-for="link in internalLinks"
                            :key="link.id"
                            :href="link.url"
                            class="flex items-center justify-between gap-4 rounded-[16px] border border-stroke bg-surface p-5 transition-colors hover:border-brand"
                        >
                            <span class="font-semibold text-ink">{{
                                link.label
                            }}</span>
                            <span class="shrink-0 text-brand">&rarr;</span>
                        </a>
                    </div>
                </section>
            </div>

            <AppUpsellBanner class="border-t border-stroke" />
        </div>
    </GuestLayout>
</template>
