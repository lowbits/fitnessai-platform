<script setup lang="ts">
import { useGuestCountry } from '@/composables/useGuestCountry';

import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, tm } = useI18n();
const { isUS, isEU } = useGuestCountry();

const getTranslationArray = (key: string): string[] => {
    const value = tm(key);
    return Array.isArray(value) ? value : [];
};
</script>

<template>
    <Head>
        <title>{{ t('legal.disclaimer.meta.title') }}</title>
        <meta
            name="description"
            :content="t('legal.disclaimer.meta.description')"
        />
    </Head>
    <GuestLayout>
        <div class="container px-6 md:px-0 mx-auto mt-10 flex-1 space-y-5 text-white">
            <h1 class="mb-8 text-3xl font-bold">
                {{ t('legal.disclaimer.title') }}
            </h1>

            <div class="mb-10 max-w-prose space-y-6">
                <!-- Warning Box -->
                <div class="mb-8 border-l-4 border-yellow-400 bg-yellow-50 p-6">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg
                                class="h-6 w-6 text-yellow-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-yellow-800">
                                {{ t('legal.disclaimer.warning.title') }}
                            </h2>
                            <p class="mt-2 text-yellow-700">
                                {{ t('legal.disclaimer.warning.description') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <!-- Not Professional Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.not_professional.title',
                                )
                            }}
                        </h2>
                        <p class="mb-4 font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.not_professional.subtitle',
                                )
                            }}
                        </p>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.not_professional.content_1',
                                )
                            }}
                        </p>
                        <p>
                            {{
                                t(
                                    'legal.disclaimer.sections.not_professional.content_2',
                                )
                            }}
                        </p>
                    </section>

                    <!-- 🆕 NOT LICENSED Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.not_licensed.title',
                                )
                            }}
                        </h2>
                        <p class="mb-4 font-semibold text-red-800">
                            {{
                                t(
                                    'legal.disclaimer.sections.not_licensed.content_1',
                                )
                            }}
                        </p>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.not_licensed.content_2',
                                )
                            }}
                        </p>
                        <p>
                            {{
                                t(
                                    'legal.disclaimer.sections.not_licensed.content_3',
                                )
                            }}
                        </p>
                    </section>

                    <!-- Medical Conditions Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.medical_conditions.title',
                                )
                            }}
                        </h2>
                        <div
                            class="mb-4 rounded border border-red-200 bg-red-50 p-4"
                        >
                            <p class="mb-2 font-semibold text-red-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.medical_conditions.warning_title',
                                    )
                                }}
                            </p>
                            <ul
                                class="list-inside list-disc space-y-1 text-red-700"
                            >
                                <li
                                    v-for="(
                                        condition, index
                                    ) in getTranslationArray(
                                        'legal.disclaimer.sections.medical_conditions.conditions',
                                    )"
                                    :key="index"
                                >
                                    {{ condition }}
                                </li>
                            </ul>
                        </div>
                        <p>
                            {{
                                t(
                                    'legal.disclaimer.sections.medical_conditions.warning_note',
                                )
                            }}
                        </p>
                    </section>

                    <!-- 🆕 EMERGENCY Section - SEHR PROMINENT! -->
                    <section>
                        <div
                            class="rounded-lg border-2 border-red-500 bg-red-50 p-6"
                        >
                            <div class="flex items-start gap-3">
                                <span class="text-3xl">{{
                                    t(
                                        'legal.disclaimer.sections.emergency.icon_warning',
                                    )
                                }}</span>
                                <div class="flex-1">
                                    <h2
                                        class="mb-4 text-2xl font-bold text-red-900"
                                    >
                                        {{
                                            t(
                                                'legal.disclaimer.sections.emergency.title',
                                            )
                                        }}
                                    </h2>
                                    <p class="mb-4 font-semibold text-red-800">
                                        {{
                                            t(
                                                'legal.disclaimer.sections.emergency.content_1',
                                            )
                                        }}
                                    </p>
                                    <ul
                                        class="mb-4 list-inside list-disc space-y-1 text-red-700"
                                    >
                                        <li
                                            v-for="(
                                                symptom, index
                                            ) in getTranslationArray(
                                                'legal.disclaimer.sections.emergency.symptoms',
                                            )"
                                            :key="index"
                                        >
                                            {{ symptom }}
                                        </li>
                                    </ul>
                                    <p class="font-semibold text-red-900">
                                        {{
                                            t(
                                                'legal.disclaimer.sections.emergency.content_2',
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- AI Content Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t('legal.disclaimer.sections.ai_content.title')
                            }}
                        </h2>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.ai_content.content_1',
                                )
                            }}
                        </p>
                        <p class="mb-4 font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.ai_content.content_2',
                                )
                            }}
                        </p>
                        <p>
                            {{
                                t(
                                    'legal.disclaimer.sections.ai_content.content_3',
                                )
                            }}
                        </p>
                    </section>

                    <!-- 🆕 NO MEDICAL RELATIONSHIP -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.no_medical_relationship.title',
                                )
                            }}
                        </h2>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.no_medical_relationship.content_1',
                                )
                            }}
                        </p>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.no_medical_relationship.content_2',
                                )
                            }}
                        </p>
                        <p>
                            {{
                                t(
                                    'legal.disclaimer.sections.no_medical_relationship.content_3',
                                )
                            }}
                        </p>
                    </section>

                    <!-- Responsibility Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.responsibility.title',
                                )
                            }}
                        </h2>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.responsibility.content_1',
                                )
                            }}
                        </p>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.responsibility.content_2',
                                )
                            }}
                        </p>
                        <ul class="mb-4 list-inside list-disc space-y-2">
                            <li
                                v-for="(item, index) in getTranslationArray(
                                    'legal.disclaimer.sections.responsibility.items',
                                )"
                                :key="index"
                            >
                                {{ item }}
                            </li>
                        </ul>
                    </section>

                    <!-- 🆕 ASSUMPTION OF RISK -->
                    <section>
                        <div
                            class="rounded border border-red-200 bg-red-50 p-4"
                        >
                            <h2
                                class="mb-4 text-2xl font-semibold text-red-900"
                            >
                                {{
                                    t(
                                        'legal.disclaimer.sections.assumption_of_risk.title',
                                    )
                                }}
                            </h2>
                            <p class="mb-4 text-red-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.assumption_of_risk.content_1',
                                    )
                                }}
                            </p>
                            <p class="mb-2 font-semibold text-red-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.assumption_of_risk.content_2',
                                    )
                                }}
                            </p>
                            <ul
                                class="mb-4 list-inside list-disc space-y-1 text-red-700"
                            >
                                <li
                                    v-for="(risk, index) in getTranslationArray(
                                        'legal.disclaimer.sections.assumption_of_risk.risks',
                                    )"
                                    :key="index"
                                >
                                    {{ risk }}
                                </li>
                            </ul>
                            <p class="font-semibold text-red-900">
                                {{
                                    t(
                                        'legal.disclaimer.sections.assumption_of_risk.content_3',
                                    )
                                }}
                            </p>
                        </div>
                    </section>

                    <!-- Nutrition Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.disclaimer.sections.nutrition.title') }}
                        </h2>
                        <p class="mb-4">
                            {{
                                t('legal.disclaimer.sections.nutrition.content')
                            }}
                        </p>
                        <ul class="mb-4 list-inside list-disc space-y-1">
                            <li
                                v-for="(item, index) in getTranslationArray(
                                    'legal.disclaimer.sections.nutrition.items',
                                )"
                                :key="index"
                            >
                                {{ item }}
                            </li>
                        </ul>
                        <p>
                            {{ t('legal.disclaimer.sections.nutrition.note') }}
                        </p>
                    </section>

                    <!-- Training Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.disclaimer.sections.training.title') }}
                        </h2>
                        <p class="mb-4">
                            {{
                                t('legal.disclaimer.sections.training.content')
                            }}
                        </p>
                        <div
                            class="mb-4 rounded border border-blue-200 bg-blue-50 p-4"
                        >
                            <p class="mb-2 font-semibold text-blue-900">
                                {{
                                    t(
                                        'legal.disclaimer.sections.training.safety_title',
                                    )
                                }}
                            </p>
                            <ul
                                class="list-inside list-disc space-y-1 text-blue-800"
                            >
                                <li
                                    v-for="(tip, index) in getTranslationArray(
                                        'legal.disclaimer.sections.training.tips',
                                    )"
                                    :key="index"
                                >
                                    {{ tip }}
                                </li>
                            </ul>
                        </div>
                    </section>

                    <!-- No Guarantee Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.no_guarantee.title',
                                )
                            }}
                        </h2>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.no_guarantee.content_1',
                                )
                            }}
                        </p>
                        <p>
                            {{
                                t(
                                    'legal.disclaimer.sections.no_guarantee.content_2',
                                )
                            }}
                        </p>
                    </section>

                    <!-- 🆕 NO WARRANTIES -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.no_warranties.title',
                                )
                            }}
                        </h2>
                        <p class="mb-4 font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.no_warranties.content_1',
                                )
                            }}
                        </p>
                        <p class="mb-2">
                            {{
                                t(
                                    'legal.disclaimer.sections.no_warranties.content_2',
                                )
                            }}
                        </p>
                        <ul class="mb-4 list-inside list-disc space-y-1">
                            <li
                                v-for="(item, index) in getTranslationArray(
                                    'legal.disclaimer.sections.no_warranties.items',
                                )"
                                :key="index"
                            >
                                {{ item }}
                            </li>
                        </ul>
                    </section>

                    <!-- Liability Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.disclaimer.sections.liability.title') }}
                        </h2>
                        <p class="mb-4 font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.liability.content_1',
                                )
                            }}
                        </p>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.liability.content_2',
                                )
                            }}
                        </p>
                        <ul class="mb-4 list-inside list-disc space-y-1">
                            <li
                                v-for="(item, index) in getTranslationArray(
                                    'legal.disclaimer.sections.liability.items',
                                )"
                                :key="index"
                            >
                                {{ item }}
                            </li>
                        </ul>
                        <p>
                            {{ t('legal.disclaimer.sections.liability.note') }}
                        </p>
                    </section>

                    <!-- 🆕 INDEMNIFICATION -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.indemnification.title',
                                )
                            }}
                        </h2>
                        <p>
                            {{
                                t(
                                    'legal.disclaimer.sections.indemnification.content',
                                )
                            }}
                        </p>
                    </section>

                    <!-- 🆕 FDA DISCLAIMER (nur für US-User) -->
                    <section v-if="isUS">
                        <div
                            class="border-l-4 border-yellow-400 bg-yellow-50 p-4"
                        >
                            <h2
                                class="mb-4 text-2xl font-semibold text-yellow-900"
                            >
                                {{
                                    t(
                                        'legal.disclaimer.sections.fda_disclaimer.title',
                                    )
                                }}
                            </h2>
                            <p class="mb-2 text-yellow-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.fda_disclaimer.content_1',
                                    )
                                }}
                            </p>
                            <p class="mb-2 text-yellow-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.fda_disclaimer.content_2',
                                    )
                                }}
                            </p>
                            <p class="text-yellow-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.fda_disclaimer.content_3',
                                    )
                                }}
                            </p>
                        </div>
                    </section>

                    <!-- 🆕 JURISDICTION -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t(
                                    'legal.disclaimer.sections.jurisdiction.title',
                                )
                            }}
                        </h2>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.jurisdiction.content_1',
                                )
                            }}
                        </p>
                        <p class="mb-4">
                            {{
                                t(
                                    'legal.disclaimer.sections.jurisdiction.content_2',
                                )
                            }}
                        </p>
                        <p>
                            {{
                                t(
                                    'legal.disclaimer.sections.jurisdiction.content_3',
                                )
                            }}
                        </p>
                    </section>

                    <!-- 🆕 INTERNATIONAL USERS (nur für Non-EU) -->
                    <section v-if="!isEU">
                        <div
                            class="rounded border border-blue-200 bg-blue-50 p-4"
                        >
                            <h2
                                class="mb-4 text-2xl font-semibold text-blue-900"
                            >
                                {{
                                    t(
                                        'legal.disclaimer.sections.international_users.title',
                                    )
                                }}
                            </h2>
                            <p class="mb-4 text-blue-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.international_users.content_1',
                                    )
                                }}
                            </p>
                            <p class="mb-2 text-blue-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.international_users.content_2',
                                    )
                                }}
                            </p>
                            <ul
                                class="mb-4 list-inside list-disc space-y-1 text-blue-700"
                            >
                                <li
                                    v-for="(item, index) in getTranslationArray(
                                        'legal.disclaimer.sections.international_users.items',
                                    )"
                                    :key="index"
                                >
                                    {{ item }}
                                </li>
                            </ul>
                            <p class="text-blue-800">
                                {{
                                    t(
                                        'legal.disclaimer.sections.international_users.content_3',
                                    )
                                }}
                            </p>
                        </div>
                    </section>

                    <!-- Updates Section -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.disclaimer.sections.updates.title') }}
                        </h2>
                        <p>
                            {{ t('legal.disclaimer.sections.updates.content') }}
                        </p>
                    </section>

                    <!-- 🆕 CHANGES -->
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.disclaimer.sections.changes.title') }}
                        </h2>
                        <p class="mb-2">
                            {{
                                t('legal.disclaimer.sections.changes.content_1')
                            }}
                        </p>
                        <p class="mb-2">
                            {{
                                t('legal.disclaimer.sections.changes.content_2')
                            }}
                        </p>
                        <p>
                            {{
                                t('legal.disclaimer.sections.changes.content_3')
                            }}
                        </p>
                    </section>

                    <!-- Confirmation Box -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="rounded bg-dark-surfaces-500 p-6">
                            <p class="mb-2 font-semibold">
                                {{ t('legal.disclaimer.confirmation.title') }}
                            </p>
                            <ul class="list-inside list-disc space-y-1">
                                <li
                                    v-for="(item, index) in getTranslationArray(
                                        'legal.disclaimer.confirmation.items',
                                    )"
                                    :key="index"
                                >
                                    {{ item }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Last Updated -->
                    <div
                        class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-500"
                    >
                        <p>{{ t('legal.disclaimer.last_updated') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
