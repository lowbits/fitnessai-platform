<script setup lang="ts">
import BaseButton from '@/components/Base/BaseButton.vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    status: number;
    title: string;
    message: string;
    labels: {
        home: string;
        app: string;
        appPitch: string;
        quicklinks: string;
        calorie: string;
        calorieDesc: string;
        workout: string;
        workoutDesc: string;
    };
    links: {
        home: string;
        calorie: string;
        workout: string;
        appStore: string;
        badge: string;
    };
}

defineProps<Props>();
</script>

<template>
    <Head :title="`${status} — ${title}`">
        <meta name="robots" content="noindex, follow" />
    </Head>

    <div class="theme-v2 flex min-h-screen flex-col bg-canvas text-ink">
        <header class="mx-auto w-full max-w-[1200px] px-6 py-6 sm:px-8">
            <a :href="links.home" class="inline-block shrink-0">
                <img
                    src="/fytrr-logo.svg"
                    alt="fytrr"
                    width="883"
                    height="303"
                    class="h-auto w-24"
                />
            </a>
        </header>

        <main
            class="relative flex flex-1 flex-col items-center justify-center overflow-hidden px-6 py-16 text-center"
        >
            <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-x-0 -top-24 -z-10 mx-auto h-[420px] max-w-3xl rounded-full bg-brand/10 blur-3xl"
            />

            <p
                class="font-grotesk text-[96px] leading-none font-extrabold tracking-tight text-brand sm:text-[128px]"
            >
                {{ status }}
            </p>

            <h1
                class="mt-6 text-3xl font-bold text-balance text-ink sm:text-4xl"
            >
                {{ title }}
            </h1>
            <p
                class="mx-auto mt-4 max-w-md text-lg leading-relaxed text-ink-muted"
            >
                {{ message }}
            </p>

            <BaseButton as="a" size="lg" :href="links.home" class="mt-8">
                {{ labels.home }}
                <span aria-hidden="true">&rarr;</span>
            </BaseButton>

            <div class="mt-12 w-full max-w-md">
                <p
                    class="font-grotesk text-xs font-bold tracking-[0.08em] text-ink-muted uppercase"
                >
                    {{ labels.quicklinks }}
                </p>
                <div class="mt-4 flex flex-col gap-3">
                    <a
                        :href="links.calorie"
                        class="group flex items-center gap-4 rounded-[18px] border border-stroke bg-surface p-4 text-left transition-all hover:-translate-y-0.5 hover:border-brand hover:bg-surface-raised"
                    >
                        <span
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[13px] bg-brand/10 text-brand transition-colors group-hover:bg-brand group-hover:text-on-brand"
                        >
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="4"
                                    y="2"
                                    width="16"
                                    height="20"
                                    rx="2"
                                />
                                <path
                                    d="M8 6h8M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14v4M8 18h4"
                                />
                            </svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-semibold text-ink">{{
                                labels.calorie
                            }}</span>
                            <span
                                class="block truncate text-sm text-ink-muted"
                                >{{ labels.calorieDesc }}</span
                            >
                        </span>
                        <svg
                            class="shrink-0 text-ink-muted transition-all group-hover:translate-x-0.5 group-hover:text-brand"
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </a>
                    <a
                        :href="links.workout"
                        class="group flex items-center gap-4 rounded-[18px] border border-stroke bg-surface p-4 text-left transition-all hover:-translate-y-0.5 hover:border-brand hover:bg-surface-raised"
                    >
                        <span
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[13px] bg-brand/10 text-brand transition-colors group-hover:bg-brand group-hover:text-on-brand"
                        >
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M6.5 6.5v11M17.5 6.5v11M4 9v6M20 9v6M6.5 12h11"
                                />
                            </svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block font-semibold text-ink">{{
                                labels.workout
                            }}</span>
                            <span
                                class="block truncate text-sm text-ink-muted"
                                >{{ labels.workoutDesc }}</span
                            >
                        </span>
                        <svg
                            class="shrink-0 text-ink-muted transition-all group-hover:translate-x-0.5 group-hover:text-brand"
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </a>
                </div>
            </div>

            <a
                :href="links.appStore"
                target="_blank"
                rel="noopener"
                class="mt-8 flex w-full max-w-md flex-col items-center gap-5 rounded-[24px] border border-brand/40 bg-brand/5 p-6 text-center transition-colors hover:border-brand sm:flex-row sm:justify-between sm:text-left"
            >
                <span>
                    <span class="block text-lg font-bold text-ink">{{
                        labels.app
                    }}</span>
                    <span class="mt-1 block text-sm text-ink-muted">{{
                        labels.appPitch
                    }}</span>
                </span>
                <img
                    :src="links.badge"
                    :alt="labels.app"
                    width="140"
                    height="46"
                    class="h-12 w-auto shrink-0"
                />
            </a>
        </main>
    </div>
</template>
