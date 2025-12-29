<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    message: string;
}

defineProps<Props>();
</script>

<template>
    <div
        class="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 p-4"
    >
        <div class="w-full max-w-md text-center">
            <div
                class="rounded-2xl border border-gray-700 bg-gray-800 p-8 shadow-2xl"
            >
                <!-- Error Icon -->
                <div
                    class="mb-6 inline-flex h-20 w-20 items-center justify-center rounded-full bg-red-500/10"
                >
                    <svg
                        class="h-10 w-10 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        ></path>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="mb-4 text-3xl font-bold text-white">
                    {{ $t('emailVerification.invalid.title') }}
                </h1>

                <!-- Message -->
                <p class="mb-8 text-gray-400">
                    {{ $t('emailVerification.invalid.description') }}
                </p>

                <!-- Info -->
                <div class="bg-gray-750 mb-6 rounded-lg p-4 text-left">
                    <p class="mb-2 text-sm font-semibold text-gray-300">
                        {{ $t('emailVerification.invalid.possibleReasons') }}
                    </p>
                    <ul class="space-y-1 text-sm text-gray-400">
                        <li>
                            •
                            {{
                                $t(
                                    'emailVerification.invalid.reasons.alreadyVerified',
                                )
                            }}
                        </li>
                        <li>
                            •
                            {{
                                $t(
                                    'emailVerification.invalid.reasons.invalidLink',
                                )
                            }}
                        </li>
                        <li>
                            •
                            {{
                                $t(
                                    'emailVerification.invalid.reasons.alreadyUsed',
                                )
                            }}
                        </li>
                    </ul>

                    <p class="mt-4 mb-2 text-sm font-semibold text-gray-300">
                        {{ $t('emailVerification.invalid.whatToDo') }}
                    </p>
                    <ul class="space-y-1 text-sm text-gray-400">
                        <li>
                            •
                            {{ $t('emailVerification.invalid.steps.tryLogin') }}
                        </li>
                        <li>
                            •
                            {{
                                $t('emailVerification.invalid.steps.contact', {
                                    email: 'hello@fytrr.com',
                                })
                            }}
                        </li>
                    </ul>
                </div>

                <!-- Action Button -->
                <Link
                    href="/"
                    class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-medium text-white transition hover:bg-blue-700"
                >
                    {{ $t('emailVerification.invalid.backHome') }}
                </Link>
            </div>
        </div>
    </div>
</template>
