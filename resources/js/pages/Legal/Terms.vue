<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
<template>
    <Head>
        <title>{{ t('legal.terms.meta.title') }}</title>
        <meta name="description" :content="t('legal.terms.meta.description')" />
    </Head>
    <GuestLayout>
        <div
            class="container mx-auto mt-10 flex-1 space-y-5 px-6 text-white md:px-0"
        >
            <h1 class="mb-8 text-3xl font-bold">
                {{ t('legal.terms.title') }}
            </h1>

            <div class="mb-10 max-w-prose space-y-6">
                <div class="space-y-8">
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.scope.title') }}
                        </h2>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.scope.paragraph_1') }}
                        </p>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.scope.paragraph_2') }}
                        </p>
                        <p>
                            {{ t('legal.terms.sections.scope.paragraph_3') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.contract.title') }}
                        </h2>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.contract.paragraph_1') }}
                        </p>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.contract.paragraph_2') }}
                        </p>
                        <p>
                            {{ t('legal.terms.sections.contract.paragraph_3') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.services.title') }}
                        </h2>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.services.paragraph_1') }}
                        </p>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.services.paragraph_2') }}
                        </p>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.services.paragraph_3') }}
                        </p>
                        <p>
                            {{ t('legal.terms.sections.services.paragraph_4') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.prices.title') }}
                        </h2>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.prices.paragraph_1') }}
                        </p>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.prices.paragraph_2') }}
                        </p>
                        <p>
                            {{ t('legal.terms.sections.prices.paragraph_3') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.subscriptions.title') }}
                        </h2>
                        <p class="mb-2">
                            {{
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_1',
                                )
                            }}
                        </p>
                        <ul class="mb-4 ml-6 list-disc space-y-1">
                            <li>
                                {{
                                    t(
                                        'legal.terms.sections.subscriptions.types.monthly',
                                    )
                                }}
                            </li>
                            <li>
                                {{
                                    t(
                                        'legal.terms.sections.subscriptions.types.yearly',
                                    )
                                }}
                            </li>
                        </ul>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_2',
                                )
                            "
                        ></p>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_3',
                                )
                            "
                        ></p>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_4',
                                )
                            "
                        ></p>
                        <ul class="mb-4 ml-6 list-disc space-y-1">
                            <li
                                v-html="
                                    t(
                                        'legal.terms.sections.subscriptions.cancellation.ios',
                                    )
                                "
                            ></li>
                            <li
                                v-html="
                                    t(
                                        'legal.terms.sections.subscriptions.cancellation.android',
                                    )
                                "
                            ></li>
                        </ul>
                        <p class="mb-2">
                            {{
                                t(
                                    'legal.terms.sections.subscriptions.cancellation.note',
                                )
                            }}
                        </p>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_5',
                                )
                            "
                        ></p>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_6',
                                )
                            "
                        ></p>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_7',
                                )
                            "
                        ></p>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_8',
                                )
                            "
                        ></p>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_9',
                                )
                            "
                        ></p>
                        <ul class="mb-4 ml-6 list-disc space-y-1">
                            <li
                                v-html="
                                    t(
                                        'legal.terms.sections.subscriptions.compliance.germany',
                                    )
                                "
                            ></li>
                            <li
                                v-html="
                                    t(
                                        'legal.terms.sections.subscriptions.compliance.uk',
                                    )
                                "
                            ></li>
                            <li
                                v-html="
                                    t(
                                        'legal.terms.sections.subscriptions.compliance.us',
                                    )
                                "
                            ></li>
                        </ul>

                        <p
                            class="mb-2"
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_10',
                                )
                            "
                        ></p>

                        <p
                            v-html="
                                t(
                                    'legal.terms.sections.subscriptions.paragraph_11',
                                )
                            "
                        ></p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.obligations.title') }}
                        </h2>
                        <p class="mb-2">
                            {{
                                t(
                                    'legal.terms.sections.obligations.paragraph_1',
                                )
                            }}
                        </p>
                        <p class="mb-2">
                            {{
                                t(
                                    'legal.terms.sections.obligations.paragraph_2',
                                )
                            }}
                        </p>
                        <p>
                            {{
                                t(
                                    'legal.terms.sections.obligations.paragraph_3',
                                )
                            }}
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.withdrawal.title') }}
                        </h2>
                        <p class="mb-2">
                            {{
                                t('legal.terms.sections.withdrawal.paragraph_1')
                            }}
                        </p>

                        <div class="my-4 rounded bg-dark-surfaces-500 p-6">
                            <h3 class="mb-3 font-semibold">
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.title',
                                    )
                                }}
                            </h3>

                            <p class="mb-2 font-semibold">
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.right_title',
                                    )
                                }}
                            </p>
                            <p class="mb-4">
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.right_text',
                                    )
                                }}
                            </p>

                            <p class="mb-4">
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.period',
                                    )
                                }}
                            </p>

                            <p class="mb-4">
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.how_to',
                                    )
                                }}
                            </p>

                            <p class="mb-2 font-semibold">
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.consequences_title',
                                    )
                                }}
                            </p>
                            <p class="mb-4">
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.consequences_text',
                                    )
                                }}
                            </p>

                            <p class="mb-2 font-semibold">
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.expiry_title',
                                    )
                                }}
                            </p>
                            <p>
                                {{
                                    t(
                                        'legal.terms.sections.withdrawal.notice.expiry_text',
                                    )
                                }}
                            </p>
                        </div>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.liability.title') }}
                        </h2>
                        <p class="mb-2">
                            {{
                                t('legal.terms.sections.liability.paragraph_1')
                            }}
                        </p>
                        <p class="mb-2">
                            {{
                                t('legal.terms.sections.liability.paragraph_2')
                            }}
                        </p>
                        <p class="mb-2">
                            {{
                                t('legal.terms.sections.liability.paragraph_3')
                            }}
                        </p>
                        <p>
                            {{
                                t('legal.terms.sections.liability.paragraph_4')
                            }}
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.copyright.title') }}
                        </h2>
                        <p class="mb-2">
                            {{
                                t('legal.terms.sections.copyright.paragraph_1')
                            }}
                        </p>
                        <p class="mb-2">
                            {{
                                t('legal.terms.sections.copyright.paragraph_2')
                            }}
                        </p>
                        <p>
                            {{
                                t('legal.terms.sections.copyright.paragraph_3')
                            }}
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{
                                t('legal.terms.sections.data_protection.title')
                            }}
                        </h2>
                        <p>
                            {{
                                t(
                                    'legal.terms.sections.data_protection.paragraph_1',
                                )
                            }}
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            {{ t('legal.terms.sections.final.title') }}
                        </h2>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.final.paragraph_1') }}
                        </p>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.final.paragraph_2') }}
                        </p>
                        <p class="mb-2">
                            {{ t('legal.terms.sections.final.paragraph_3') }}
                        </p>
                    </section>

                    <div
                        class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-500"
                    >
                        <p>{{ t('legal.terms.last_updated') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
