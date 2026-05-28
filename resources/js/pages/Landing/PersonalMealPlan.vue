<script setup lang="ts">
import FormCard from '@/components/FormCard.vue';
import GenerateFitnessPlanForm from '@/components/GenerateFitnessPlanForm.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import { Button } from '@/components/ui/button';
import FAQSection from '@/components/workoutPlan/FAQSection.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const props = defineProps<{
    durationDays: number;
    alternateUrls: Record<string, string>;
}>();

const baseUrl = 'https://fytrr.com';
const canonical = `${baseUrl}/de/persoenlicher-ernaehrungsplan`;

const faqs = [
    {
        question: 'Ist der Ernährungsplan wirklich kostenlos?',
        answer: 'Ja, komplett kostenlos. Du bekommst einen vollständigen Ernährungsplan als PDF. Keine Kreditkarte, kein Account, keine versteckten Kosten.',
    },
    {
        question: 'Kann ich Allergien und Unverträglichkeiten angeben?',
        answer: 'Ja. Du kannst deine Diätform (vegan, vegetarisch, keto etc.) und Allergien angeben. Die KI generiert Mahlzeiten, die deine Einschränkungen berücksichtigen.',
    },
    {
        question: 'Wie viele Kalorien enthält der Plan?',
        answer: 'Die Kalorien werden individuell berechnet. Basierend auf deinem Gewicht, deiner Größe, deinem Alter und deinem Aktivitätslevel berechnet die KI deinen Tagesbedarf und passt den Plan an dein Ziel an.',
    },
    {
        question: 'Kann ich den Plan als PDF herunterladen?',
        answer: 'Ja. Nach der Erstellung bekommst du deinen Ernährungsplan als PDF. Drucke ihn aus oder speichere ihn auf deinem Smartphone.',
    },
    {
        question: 'Erstellt die KI auch einen Trainingsplan dazu?',
        answer: 'Ja. Bei der Erstellung kannst du wählen, ob du einen kombinierten Trainings- und Ernährungsplan möchtest. Beides wird aufeinander abgestimmt.',
    },
    {
        question: 'Muss ich mich registrieren?',
        answer: 'Nein. Du brauchst nur eine E-Mail-Adresse, an die wir deinen fertigen Plan senden. Kein Passwort, kein Account, keine Registrierung.',
    },
    {
        question: 'Wie oft sollte ich meinen Ernährungsplan aktualisieren?',
        answer: 'Alle 4 bis 6 Wochen, oder wenn sich dein Gewicht, deine Aktivität oder dein Ziel verändert hat. Mit der Fytrr-App kannst du jederzeit einen neuen Plan generieren.',
    },
];

const webAppSchema = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'WebApplication',
        name: 'Fytrr Persönlicher Ernährungsplan Generator',
        description:
            'Erstelle deinen persönlichen Ernährungsplan kostenlos mit KI.',
        applicationCategory: 'HealthApplication',
        url: canonical,
        inLanguage: 'de',
        offers: {
            '@type': 'Offer',
            price: '0',
            priceCurrency: 'EUR',
        },
    }),
);

const faqSchema = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'FAQPage',
        mainEntity: faqs.map((faq) => ({
            '@type': 'Question',
            name: faq.question,
            acceptedAnswer: {
                '@type': 'Answer',
                text: faq.answer,
            },
        })),
    }),
);
</script>

<template>
    <Head title="Persönlicher Ernährungsplan kostenlos erstellen">
        <meta
            name="description"
            content="Erstelle deinen persönlichen Ernährungsplan kostenlos mit KI. Für Abnehmen, Muskelaufbau oder Sport. Als PDF mit Einkaufsliste, ohne Anmeldung."
        />
        <link rel="canonical" :href="canonical" />
        <meta
            property="og:title"
            content="Persönlicher Ernährungsplan kostenlos erstellen"
        />
        <meta
            property="og:description"
            content="Erstelle deinen persönlichen Ernährungsplan kostenlos mit KI. Für Abnehmen, Muskelaufbau oder Sport."
        />
        <meta property="og:url" :content="canonical" />
        <meta property="og:type" content="website" />
        <link
            v-for="(url, loc) in alternateUrls"
            :key="loc"
            rel="alternate"
            :hreflang="loc"
            :href="url"
        />
        <component :is="'script'" type="application/ld+json">
            {{ webAppSchema }}
        </component>
        <component :is="'script'" type="application/ld+json">
            {{ faqSchema }}
        </component>
    </Head>

    <GuestLayout>
        <div class="bg-dark-surfaces-900">
            <!-- Hero with Generator Form -->
            <section class="px-4 pt-12 pb-8 sm:px-6 lg:px-8">
                <div
                    class="mx-auto flex max-w-6xl flex-col gap-10 lg:flex-row lg:items-start"
                >
                    <div class="lg:w-1/2 lg:pt-8">
                        <h1
                            class="font-display text-3xl font-bold text-white sm:text-4xl lg:text-5xl"
                        >
                            Persönlicher Ernährungsplan
                            <span
                                class="bg-linear-to-r from-primary-400 to-primary-500 bg-clip-text text-transparent"
                                >kostenlos erstellen in 60 Sekunden</span
                            >
                        </h1>
                        <p class="mt-4 text-lg text-gray-300">
                            Gib dein Ziel an, wähle deine Vorlieben und lade
                            deinen individuellen Ernährungsplan als PDF
                            herunter. KI-basiert, mit Einkaufsliste, ohne
                            Anmeldung.
                        </p>
                    </div>
                    <div class="lg:w-1/2">
                        <FormCard>
                            <GenerateFitnessPlanForm
                                :total-days="durationDays"
                                utm-content="landing_personal_meal_plan"
                                utm-campaign="landing_pages"
                            />
                        </FormCard>
                    </div>
                </div>
            </section>

            <!-- Warum -->
            <section class="px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        Warum ein persönlicher Ernährungsplan?
                    </h2>
                    <p class="mt-4 leading-relaxed text-gray-300">
                        Allgemeine Ernährungspläne passen selten zu deinen
                        Zielen, Vorlieben und Alltag. Ein personalisierter Plan
                        berücksichtigt deinen individuellen Kalorienbedarf,
                        deine Makronährstoffverteilung, Allergien und Diätform.
                    </p>
                    <p class="mt-4 leading-relaxed text-gray-300">
                        Fytrr erstellt deinen personalisierten Ernährungsplan
                        mit KI basierend auf deinen Angaben. Kein Raten, kein
                        stundenlanges Recherchieren. In 60 Sekunden hast du
                        einen Plan, der auf dich zugeschnitten ist.
                    </p>
                </div>
            </section>

            <!-- Was enthalten -->
            <section
                class="bg-dark-surfaces-800 px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        Was dein Ernährungsplan enthält
                    </h2>

                    <div class="mt-8 space-y-8">
                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                7-Tage Mahlzeitenplan
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    Frühstück, Mittagessen, Abendessen und
                                    Snacks für jeden Tag
                                </li>
                                <li>
                                    Kalorien und Makros pro Mahlzeit berechnet
                                </li>
                                <li>
                                    Angepasst an deine Diätform: vegan,
                                    vegetarisch, keto, low-carb und mehr
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Einkaufsliste
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    Automatisch generiert aus deinem
                                    Mahlzeitenplan
                                </li>
                                <li>
                                    Sortiert nach Kategorien für schnelles
                                    Einkaufen
                                </li>
                                <li>
                                    Keine exotischen Zutaten, alles im
                                    Supermarkt erhältlich
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                PDF Download
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    Alles in einem Dokument: Mahlzeiten,
                                    Nährwerte, Einkaufsliste
                                </li>
                                <li>Druckfertig für Kühlschrank oder Küche</li>
                                <li>
                                    Ohne Anmeldung oder Registrierung.
                                    Erstellen, herunterladen, loslegen.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- So funktioniert's -->
            <section class="px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        So erstellst du deinen Ernährungsplan
                    </h2>
                    <div class="mt-8 space-y-6">
                        <div class="flex gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-500 font-bold text-dark-surfaces-900"
                            >
                                1
                            </div>
                            <div>
                                <h3
                                    class="text-lg font-semibold text-white"
                                >
                                    Ziel angeben
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Abnehmen, Muskelaufbau oder Gewicht halten.
                                    Wähle, was zu dir passt.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-500 font-bold text-dark-surfaces-900"
                            >
                                2
                            </div>
                            <div>
                                <h3
                                    class="text-lg font-semibold text-white"
                                >
                                    Vorlieben wählen
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Diätform, Allergien, Budget. Die KI passt
                                    den Plan an deine Bedürfnisse an.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-500 font-bold text-dark-surfaces-900"
                            >
                                3
                            </div>
                            <div>
                                <h3
                                    class="text-lg font-semibold text-white"
                                >
                                    Ernährungsplan als PDF laden
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Dein persönlicher Ernährungsplan ist in 60
                                    Sekunden fertig. Kostenlos herunterladen.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Für wen -->
            <section
                class="bg-dark-surfaces-800 px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        Für wen ist der Ernährungsplan?
                    </h2>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2">
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Ernährungsplan zum Abnehmen
                            </h3>
                            <p class="mt-2 text-gray-300">
                                Kaloriendefizit berechnet, sättigende
                                Mahlzeiten. Kombiniere mit einem
                                <a
                                    href="/de/kostenloser-trainingsplan/abnehmen"
                                    class="text-primary-400 hover:underline"
                                    >Trainingsplan zum Abnehmen</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Ernährungsplan für Muskelaufbau
                            </h3>
                            <p class="mt-2 text-gray-300">
                                Kalorienüberschuss und Proteinziele. Passend
                                zum
                                <a
                                    href="/de/kostenloser-trainingsplan/muskelaufbau"
                                    class="text-primary-400 hover:underline"
                                    >Trainingsplan Muskelaufbau</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Ernährungsplan für Sportler
                            </h3>
                            <p class="mt-2 text-gray-300">
                                Angepasst an Trainingsintensität.
                                Mahlzeiten-Timing rund ums Training.
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Ernährungsplan für Anfänger
                            </h3>
                            <p class="mt-2 text-gray-300">
                                Einfache Rezepte, keine exotischen Zutaten.
                                Schau dir auch unseren
                                <a
                                    href="/de/kostenloser-trainingsplan/anfaenger"
                                    class="text-primary-400 hover:underline"
                                    >Trainingsplan für Anfänger</a
                                >
                                an.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <FAQSection :faqs="faqs" heading="Häufige Fragen zum Ernährungsplan" />

            <!-- Final CTA -->
            <section class="px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <h2
                        class="font-display text-3xl font-bold text-white"
                    >
                        Bereit loszulegen?
                    </h2>
                    <p class="mt-4 text-lg text-gray-300">
                        Erstelle jetzt deinen persönlichen Ernährungsplan. 60
                        Sekunden, kostenlos, sofort als PDF.
                    </p>
                    <GenerateFitnessPlanModal
                        utm-content="landing_personal_meal_plan_cta"
                        utm-campaign="landing_pages"
                        #default="{ open }"
                    >
                        <Button
                            @click="open"
                            class="mt-8 rounded-xl bg-primary-500 px-8 py-4 text-lg font-semibold text-dark-surfaces-900 hover:bg-primary-400"
                        >
                            Jetzt Ernährungsplan erstellen
                        </Button>
                    </GenerateFitnessPlanModal>
                </div>
            </section>

            <!-- Related Links -->
            <section
                class="border-t border-dark-surfaces-500 px-4 py-12 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2
                        class="font-display text-xl font-bold text-white"
                    >
                        Weitere kostenlose Tools
                    </h2>
                    <ul class="mt-4 space-y-2 text-gray-300">
                        <li>
                            <a
                                href="/de/kostenlose-tools/kalorienrechner"
                                class="text-primary-400 hover:underline"
                                >Kalorienrechner</a
                            >
                            -- Berechne deinen Kalorienbedarf
                        </li>
                        <li>
                            <a
                                href="/de/kostenloser-trainingsplan"
                                class="text-primary-400 hover:underline"
                                >Alle kostenlosen Trainingspläne</a
                            >
                        </li>
                        <li>
                            <a
                                href="/de/blog/ernaehrungsplan-erstellen"
                                class="text-primary-400 hover:underline"
                                >Ernährungsplan erstellen: Die Anleitung</a
                            >
                        </li>
                        <li>
                            <a
                                href="/de/blog/kalorienbedarf-berechnen"
                                class="text-primary-400 hover:underline"
                                >Kalorienbedarf berechnen</a
                            >
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>
