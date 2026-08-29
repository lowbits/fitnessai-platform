<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
<template>
    <Head>
        <title>{{ t('legal.data_privacy.meta.title') }}</title>
        <meta
            name="description"
            :content="t('legal.data_privacy.meta.description')"
        />
        <meta name="robots" content="noindex,follow" />
    </Head>
    <GuestLayout>
        <div
            class="text-pron container mx-auto mt-10 flex-1 space-y-5 px-6 text-white md:px-0"
        >
            <h1 class="mb-8 text-3xl font-bold">
                {{ t('legal.data_privacy.title') }}
            </h1>

            <div class="mb-10 max-w-prose space-y-8">
                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        {{ t('legal.data_privacy.sections.overview.title') }}
                    </h2>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.overview.general.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.overview.general.description',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.overview.data_collection.title',
                            )
                        }}
                    </h3>
                    <p class="mb-2">
                        <strong>{{
                            t(
                                'legal.data_privacy.sections.overview.data_collection.who_responsible',
                            )
                        }}</strong>
                    </p>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.overview.data_collection.who_answer',
                            )
                        }}
                    </p>

                    <p class="mb-2">
                        <strong>{{
                            t(
                                'legal.data_privacy.sections.overview.data_collection.how_collect',
                            )
                        }}</strong>
                    </p>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.overview.data_collection.how_answer',
                            )
                        }}
                    </p>

                    <p class="mb-2">
                        <strong>{{
                            t(
                                'legal.data_privacy.sections.overview.data_collection.why_use',
                            )
                        }}</strong>
                    </p>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.overview.data_collection.why_answer',
                            )
                        }}
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        {{ t('legal.data_privacy.sections.hosting.title') }}
                    </h2>
                    <p class="mb-2">
                        <strong>{{
                            t('legal.data_privacy.sections.hosting.provider')
                        }}</strong>
                    </p>
                    <p class="mb-4">
                        {{
                            t('legal.data_privacy.sections.hosting.description')
                        }}
                    </p>
                    <p
                        class="mb-4"
                        v-html="
                            t('legal.data_privacy.sections.hosting.details')
                        "
                    ></p>
                    <p class="mb-2">
                        {{
                            t('legal.data_privacy.sections.hosting.legal_basis')
                        }}
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        {{
                            t('legal.data_privacy.sections.general_info.title')
                        }}
                    </h2>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.data_protection.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.data_protection.description',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.responsible_party.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.responsible_party.description',
                            )
                        }}
                    </p>
                    <div class="mb-4 rounded bg-dark-surfaces-500 p-4">
                        <p>Tobias Lobitz</p>
                        <p>Annastraße 7</p>
                        <p>40477 Düsseldorf</p>
                        <p>
                            {{
                                t(
                                    'legal.data_privacy.sections.general_info.responsible_party.email',
                                )
                            }}: hello@fytrr.com
                        </p>
                    </div>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.storage_duration.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.storage_duration.description',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.consent_withdrawal.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.consent_withdrawal.description',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.complaint_right.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.complaint_right.description',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.data_portability.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.data_portability.description',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.access_rights.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.general_info.access_rights.description',
                            )
                        }}
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.title',
                            )
                        }}
                    </h2>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.server_logs.title',
                            )
                        }}
                    </h3>
                    <p class="mb-2">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.server_logs.description',
                            )
                        }}
                    </p>
                    <ul class="mb-4 list-inside list-disc space-y-1">
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.server_logs.items.browser',
                                )
                            }}
                        </li>
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.server_logs.items.os',
                                )
                            }}
                        </li>
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.server_logs.items.referrer',
                                )
                            }}
                        </li>
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.server_logs.items.hostname',
                                )
                            }}
                        </li>
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.server_logs.items.time',
                                )
                            }}
                        </li>
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.server_logs.items.ip',
                                )
                            }}
                        </li>
                    </ul>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.server_logs.legal_note',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.email_inquiry.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.email_inquiry.description',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.registration.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.registration.description',
                            )
                        }}
                    </p>
                    <ul class="mb-4 list-inside list-disc space-y-1">
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.registration.items.email',
                                )
                            }}
                        </li>
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.registration.items.body_data',
                                )
                            }}
                        </li>
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.registration.items.goals',
                                )
                            }}
                        </li>
                        <li>
                            {{
                                t(
                                    'legal.data_privacy.sections.data_collection.registration.items.health',
                                )
                            }}
                        </li>
                    </ul>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.data_collection.registration.legal_note',
                            )
                        }}
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.title',
                            )
                        }}
                    </h2>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.ai.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.ai.description',
                            )
                        }}
                    </p>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.ai.withdrawal',
                            )
                        }}
                    </p>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.ai.contracts',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.apple_health.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.apple_health.description',
                            )
                        }}
                    </p>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.apple_health.sharing',
                            )
                        }}
                    </p>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.apple_health.legal',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.posthog.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.posthog.description',
                            )
                        }}
                    </p>
                    <p
                        class="mb-4"
                        v-html="
                            t(
                                'legal.data_privacy.sections.external_services.posthog.legal',
                            )
                        "
                    ></p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.resend.title',
                            )
                        }}
                    </h3>
                    <p
                        class="mb-4"
                        v-html="
                            t(
                                'legal.data_privacy.sections.external_services.resend.description',
                            )
                        "
                    ></p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.plausible.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.plausible.description',
                            )
                        }}
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.polar.title',
                            )
                        }}
                    </h3>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.external_services.polar.description',
                            )
                        }}
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        {{
                            t('legal.data_privacy.sections.data_deletion.title')
                        }}
                    </h2>
                    <p class="mb-4">
                        {{
                            t(
                                'legal.data_privacy.sections.data_deletion.description',
                            )
                        }}
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        {{ t('legal.data_privacy.sections.ssl.title') }}
                    </h2>
                    <p class="mb-4">
                        {{ t('legal.data_privacy.sections.ssl.description') }}
                    </p>
                </section>

                <div
                    class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-500"
                >
                    <p>{{ t('legal.data_privacy.last_updated') }}</p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
