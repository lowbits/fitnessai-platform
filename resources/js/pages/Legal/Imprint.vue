<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';

const { t } = useI18n();
</script>
<template>
    <Head>
        <title>{{ t('legal.imprint.meta.title') }}</title>
        <meta
            name="description"
            :content="t('legal.imprint.meta.description')"
        />
    </Head>
    <GuestLayout>
        <div
            class="container mx-auto mt-10 flex-1 space-y-5 px-6 text-white md:px-0"
        >
            <h1 class="mb-8 text-3xl font-bold">
                {{ t('legal.imprint.title') }}
            </h1>

            <div class="mb-10 max-w-prose space-y-6">
                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        {{ t('legal.imprint.sections.company.title') }}
                    </h2>
                    <div class="space-y-1">
                        <p class="font-medium">Tobias Lobitz</p>
                        <p>Annastraße 7</p>
                        <p>40477 Düsseldorf</p>
                        <p>{{ t('legal.imprint.sections.company.country') }}</p>
                    </div>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        {{ t('legal.imprint.sections.contact.title') }}
                    </h2>
                    <div class="space-y-1">
                        <p>
                            {{ t('legal.imprint.sections.contact.email') }}:
                            <a
                                href="mailto:hello@fytrr.com"
                                class="text-primary-500 hover:underline"
                                >hello@fytrr.com</a
                            >
                        </p>
                    </div>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        {{ t('legal.imprint.sections.tax.title') }}
                    </h2>
                    <p class="">
                        {{ t('legal.imprint.sections.tax.description') }}:<br />
                        <span class="font-mono">DE358679862</span>
                    </p>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        {{ t('legal.imprint.sections.dispute.title') }}
                    </h2>
                    <p class="" v-html="t('legal.imprint.sections.dispute.description')"></p>
                    <p class="mt-2">
                        {{ t('legal.imprint.sections.dispute.email_note') }}
                    </p>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        {{ t('legal.imprint.sections.consumer_dispute.title') }}
                    </h2>
                    <p class="">
                        {{ t('legal.imprint.sections.consumer_dispute.description') }}
                    </p>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        {{ t('legal.imprint.sections.disclaimer.title') }}
                    </h2>

                    <h3 class="mt-4 mb-2 font-semibold">
                        {{ t('legal.imprint.sections.disclaimer.content.title') }}
                    </h3>
                    <p class="text-sm">
                        {{ t('legal.imprint.sections.disclaimer.content.description') }}
                    </p>

                    <h3 class="mt-4 mb-2 font-semibold">
                        {{ t('legal.imprint.sections.disclaimer.links.title') }}
                    </h3>
                    <p class="text-sm">
                        {{ t('legal.imprint.sections.disclaimer.links.description') }}
                    </p>

                    <h3 class="mt-4 mb-2 font-semibold">
                        {{ t('legal.imprint.sections.disclaimer.copyright.title') }}
                    </h3>
                    <p class="text-sm">
                        {{ t('legal.imprint.sections.disclaimer.copyright.description') }}
                    </p>
                </section>
            </div>
        </div>
    </GuestLayout>
</template>
