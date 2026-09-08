<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    status: 'confirmed' | 'expired';
}>();

const { t, locale } = useI18n();

const isConfirmed = computed(() => props.status === 'confirmed');
const homeUrl = computed(() => `/${locale.value}`);
const pageTitle = computed(() =>
    isConfirmed.value
        ? t('newsletterConfirm.confirmedHeading')
        : t('newsletterConfirm.expiredHeading'),
);
</script>

<template>
    <Head :title="pageTitle">
        <meta name="robots" content="noindex" />
    </Head>

    <GuestLayout>
        <div
            class="theme-v2 flex flex-1 items-center justify-center bg-canvas px-6 py-20 text-ink"
        >
            <div class="w-full max-w-md text-center">
                <span
                    class="mx-auto flex size-14 items-center justify-center rounded-full"
                    :class="
                        isConfirmed
                            ? 'bg-brand/10 text-brand'
                            : 'bg-stroke text-ink-muted'
                    "
                >
                    <svg
                        v-if="isConfirmed"
                        class="size-7"
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
                    <svg
                        v-else
                        class="size-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
                        />
                    </svg>
                </span>

                <h1 class="mt-6 text-2xl font-bold text-balance text-ink sm:text-3xl">
                    {{
                        isConfirmed
                            ? t('newsletterConfirm.confirmedHeading')
                            : t('newsletterConfirm.expiredHeading')
                    }}
                </h1>
                <p class="mx-auto mt-3 max-w-sm leading-relaxed text-ink-muted">
                    {{
                        isConfirmed
                            ? t('newsletterConfirm.confirmedText')
                            : t('newsletterConfirm.expiredText')
                    }}
                </p>

                <Link
                    :href="homeUrl"
                    class="mt-8 inline-flex items-center justify-center rounded-xl bg-brand px-7 py-3.5 text-base font-semibold text-on-brand transition-colors hover:bg-brand/90"
                >
                    {{ t('newsletterConfirm.cta') }}
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>
