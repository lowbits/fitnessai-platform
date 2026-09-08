<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, tm, rt, locale } = useI18n();

const benefits = computed(() =>
    (tm('appUpsellBanner.benefits') as unknown[]).map((item) => rt(item as string)),
);

const appUrl = computed(() => `/${locale.value}/app`);
const phoneImage = computed(
    () => `/assets/images/app/fytrr-ki-fitness-app-${locale.value}.webp`,
);
</script>

<template>
    <section
        class="mx-auto max-w-[1200px] px-6 py-14 sm:px-8 lg:px-[80px] lg:py-20"
    >
        <div
            class="relative overflow-hidden rounded-[28px] border border-stroke bg-surface"
        >
            <div
                aria-hidden="true"
                class="pointer-events-none absolute -top-24 -right-24 h-[360px] w-[360px] rounded-full bg-brand/15 blur-3xl"
            />
            <div
                class="relative grid items-center gap-10 p-8 sm:p-10 lg:grid-cols-[1fr_300px] lg:gap-16 lg:p-14"
            >
                <div class="text-center lg:text-left">
                    <p
                        class="font-grotesk text-sm font-bold tracking-[0.06em] text-brand uppercase"
                    >
                        {{ t('appUpsellBanner.eyebrow') }}
                    </p>
                    <h2
                        class="mt-4 text-3xl font-bold text-balance text-ink sm:text-4xl"
                    >
                        {{ t('appUpsellBanner.heading') }}
                    </h2>
                    <p
                        class="mx-auto mt-4 max-w-xl leading-relaxed text-ink-muted lg:mx-0"
                    >
                        {{ t('appUpsellBanner.subheading') }}
                    </p>

                    <ul
                        class="mx-auto mt-8 flex max-w-md flex-col gap-3 text-left lg:mx-0"
                    >
                        <li
                            v-for="benefit in benefits"
                            :key="benefit"
                            class="flex items-center gap-3"
                        >
                            <span
                                class="flex size-6 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand"
                            >
                                <svg
                                    class="size-3.5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0l-3.5-3.5a1 1 0 1 1 1.4-1.4l2.8 2.79 6.8-6.79a1 1 0 0 1 1.4 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                            <span class="text-ink">{{ benefit }}</span>
                        </li>
                    </ul>

                    <a
                        :href="appUrl"
                        class="mt-8 inline-flex items-center justify-center gap-2 rounded-xl bg-brand px-7 py-3.5 text-base font-semibold text-on-brand transition-colors hover:bg-brand/90"
                    >
                        {{ t('appUpsellBanner.cta') }}
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"
                            />
                        </svg>
                    </a>
                </div>

                <div class="relative flex justify-center lg:justify-end">
                    <div
                        aria-hidden="true"
                        class="pointer-events-none absolute inset-0 -z-10 m-auto h-[320px] w-[320px] max-w-full rounded-full bg-brand/10 blur-3xl"
                    />
                    <img
                        :src="phoneImage"
                        :alt="t('appUpsellBanner.imageAlt')"
                        class="w-[240px] max-w-full drop-shadow-2xl sm:w-[280px]"
                        loading="lazy"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
