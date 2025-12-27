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
                        Angaben gemäß § 5 TMG
                    </h2>
                    <div class="space-y-1">
                        <p class="font-medium">Tobias Lobitz</p>
                        <p>Annastraße 7</p>
                        <p>40477 Düsseldorf</p>
                    </div>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">Kontakt</h2>
                    <div class="space-y-1">
                        <p>
                            E-Mail:
                            <a
                                href="mailto:hello@fitnessai.me"
                                class="text-primary-500 hover:underline"
                                >hello@fitnessai.me</a
                            >
                        </p>
                    </div>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">Umsatzsteuer-ID</h2>
                    <p class="">
                        Umsatzsteuer-Identifikationsnummer gemäß § 27 a
                        Umsatzsteuergesetz:<br />
                        <span class="font-mono">DE358679862</span>
                    </p>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        EU-Streitschlichtung
                    </h2>
                    <p class="">
                        Die Europäische Kommission stellt eine Plattform zur
                        Online-Streitbeilegung (OS) bereit:
                        <a
                            href="https://ec.europa.eu/consumers/odr/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-primary-500 hover:underline"
                            >https://ec.europa.eu/consumers/odr/</a
                        >
                    </p>
                    <p class="mt-2">
                        Unsere E-Mail-Adresse finden Sie oben im Impressum.
                    </p>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        Verbraucherstreitbeilegung
                    </h2>
                    <p class="">
                        Wir sind nicht bereit oder verpflichtet, an
                        Streitbeilegungsverfahren vor einer
                        Verbraucherschlichtungsstelle teilzunehmen.
                    </p>
                </section>

                <section>
                    <h2 class="mb-3 text-xl font-semibold">
                        Haftungsausschluss
                    </h2>

                    <h3 class="mt-4 mb-2 font-semibold">Haftung für Inhalte</h3>
                    <p class="text-sm">
                        Die Inhalte unserer Seiten wurden mit größter Sorgfalt
                        erstellt. Für die Richtigkeit, Vollständigkeit und
                        Aktualität der Inhalte können wir jedoch keine Gewähr
                        übernehmen. Als Diensteanbieter sind wir gemäß § 7 Abs.1
                        TMG für eigene Inhalte auf diesen Seiten nach den
                        allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10
                        TMG sind wir als Diensteanbieter jedoch nicht
                        verpflichtet, übermittelte oder gespeicherte fremde
                        Informationen zu überwachen oder nach Umständen zu
                        forschen, die auf eine rechtswidrige Tätigkeit
                        hinweisen.
                    </p>

                    <h3 class="mt-4 mb-2 font-semibold">Haftung für Links</h3>
                    <p class="text-sm">
                        Unser Angebot enthält Links zu externen Websites
                        Dritter, auf deren Inhalte wir keinen Einfluss haben.
                        Deshalb können wir für diese fremden Inhalte auch keine
                        Gewähr übernehmen. Für die Inhalte der verlinkten Seiten
                        ist stets der jeweilige Anbieter oder Betreiber der
                        Seiten verantwortlich.
                    </p>

                    <h3 class="mt-4 mb-2 font-semibold">Urheberrecht</h3>
                    <p class="text-sm">
                        Die durch die Seitenbetreiber erstellten Inhalte und
                        Werke auf diesen Seiten unterliegen dem deutschen
                        Urheberrecht. Die Vervielfältigung, Bearbeitung,
                        Verbreitung und jede Art der Verwertung außerhalb der
                        Grenzen des Urheberrechtes bedürfen der schriftlichen
                        Zustimmung des jeweiligen Autors bzw. Erstellers.
                    </p>
                </section>
            </div>
        </div>
    </GuestLayout>
</template>
