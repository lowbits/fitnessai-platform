<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    message: string;
}

defineProps<Props>();
</script>

<template>
    <Head :title="$t('emailVerification.expired.title')" />

    <div
        class="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 p-4"
    >
        <div class="w-full max-w-md text-center">
            <div
                class="rounded-2xl border border-gray-700 bg-gray-800 p-8 shadow-2xl"
            >
                <!-- Warning Icon -->
                <div
                    class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-yellow-500/10"
                >
                    <svg
                        class="h-10 w-10 text-yellow-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="mb-4 text-3xl font-bold text-white">
                    {{ $t('emailVerification.expired.title') }}
                </h1>

                <!-- Message -->
                <p class="mb-8 text-gray-400">
                    {{ $t('emailVerification.expired.description') }}
                </p>

                <!-- Info -->
                <div class="bg-gray-750 mb-6 rounded-lg p-4 text-left">
                    <p class="mb-2 text-sm font-semibold text-gray-300">
                        {{ $t('emailVerification.expired.whatToDo') }}
                    </p>
                    <ul
                        class="list-inside list-disc space-y-1 text-sm text-gray-400"
                    >
                        <li>
                            {{
                                $t('emailVerification.expired.steps.contact', {
                                    email: 'hello@fytrr.com',
                                })
                            }}
                        </li>
                        <li>
                            {{ $t('emailVerification.expired.steps.include') }}
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <Link
                        href="/"
                        class="block rounded-lg bg-gray-700 px-6 py-3 font-medium text-gray-300 transition hover:bg-gray-600"
                    >
                        {{ $t('emailVerification.expired.backHome') }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
