<script setup lang="ts">
import AppUpsellBanner from '@/components/AppUpsellBanner.vue';
import GenerateFitnessPlanForm from '@/components/GenerateFitnessPlanForm.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import { Button } from '@/components/ui/button';
import FAQSection from '@/components/workoutPlan/FAQSection.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    durationDays: number;
    alternateUrls: Record<string, string>;
}>();

const baseUrl = 'https://fytrr.com';
const canonical = `${baseUrl}/de/persoenlicher-ernaehrungsplan`;

const heroBenefits = [
    'Kostenlos und ohne Anmeldung',
    'Kompletter 7-Tage-Plan als PDF mit Einkaufsliste',
    'Individuell per KI, abgestimmt auf dein Ziel',
];

const faqs = [
    {
        question: 'Ist der KI-Ernährungsplan wirklich kostenlos?',
        answer: 'Ja. Du erstellst deinen Plan kostenlos und lädst ihn als PDF herunter. Ohne Konto, ohne Anmeldung, ohne versteckte Kosten.',
    },
    {
        question: 'Bekomme ich den Ernährungsplan als PDF?',
        answer: 'Ja. Nach der Auswahl deiner Ziele und Vorlieben lädst du deinen kompletten 7-Tage-Plan mit Einkaufsliste direkt als PDF herunter.',
    },
    {
        question: 'Brauche ich eine Anmeldung?',
        answer: 'Nein. Der KI-Ernährungsplan lässt sich ohne Anmeldung erstellen. Für laufende Anpassung, Foto-Tracking und Coach Mona nutzt du die fytrr App.',
    },
    {
        question: 'Eignet sich der Plan zum Abnehmen und für Muskelaufbau?',
        answer: 'Ja. Du wählst dein Ziel (Abnehmen, Muskeln aufbauen oder fitter werden) und der Plan wird auf deinen Kalorienbedarf und deine Makros abgestimmt.',
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
        question: 'Erstellt die KI auch einen Trainingsplan dazu?',
        answer: 'Ja. Passend zu deinem Ernährungsplan bekommst du automatisch auch einen Trainingsplan dazu. Beides wird von der KI aufeinander abgestimmt.',
    },
    {
        question: 'Wie oft sollte ich meinen Ernährungsplan aktualisieren?',
        answer: 'Alle 4 bis 6 Wochen, oder wenn sich dein Gewicht, deine Aktivität oder dein Ziel verändert hat. Mit der fytrr-App kannst du jederzeit einen neuen Plan generieren.',
    },
];

const webAppSchema = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'WebApplication',
        name: 'fytrr KI-Ernährungsplan',
        description:
            'Erstelle deinen KI-Ernährungsplan kostenlos: für Abnehmen, Muskelaufbau oder Sport. Persönlich, als PDF mit Einkaufsliste, ohne Anmeldung.',
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
    <Head title="Persönlicher KI-Ernährungsplan kostenlos erstellen">
        <meta
            name="description"
            content="Erstelle deinen KI-Ernährungsplan kostenlos: für Abnehmen, Muskelaufbau oder Sport. Persönlich, als PDF mit Einkaufsliste, ohne Anmeldung."
        />
        <link rel="canonical" :href="canonical" />
        <meta
            property="og:title"
            content="Persönlicher KI-Ernährungsplan kostenlos erstellen"
        />
        <meta
            property="og:description"
            content="Erstelle deinen KI-Ernährungsplan kostenlos: für Abnehmen, Muskelaufbau oder Sport. Persönlich, als PDF mit Einkaufsliste, ohne Anmeldung."
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
        <div class="theme-v2 bg-canvas text-ink">
            <!-- Hero with Generator Form -->
            <section
                class="mx-auto max-w-[1200px] px-6 py-14 sm:px-8 lg:px-[80px] lg:py-20"
            >
                <div
                    class="grid grid-cols-1 items-center gap-12 lg:grid-cols-[1fr_500px] lg:gap-16"
                >
                    <div class="text-center lg:text-left">
                        <p
                            class="font-grotesk text-sm font-bold tracking-[0.06em] text-brand uppercase"
                        >
                            Kostenlos · Ohne Anmeldung
                        </p>
                        <h1
                            class="mt-4 text-4xl font-extrabold tracking-tight text-balance text-ink sm:text-5xl lg:leading-[1.05]"
                        >
                            KI-Ernährungsplan
                            <span class="text-brand"
                                >kostenlos erstellen, in 60 Sekunden</span
                            >
                        </h1>
                        <p
                            class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-ink-muted lg:mx-0"
                        >
                            Gib dein Ziel an, wähle deine Vorlieben und lade
                            deinen individuellen Ernährungsplan als PDF
                            herunter. KI-basiert, mit Einkaufsliste, ohne
                            Anmeldung.
                        </p>

                        <ul
                            class="mx-auto mt-8 flex max-w-md flex-col gap-3 text-left lg:mx-0"
                        >
                            <li
                                v-for="benefit in heroBenefits"
                                :key="benefit"
                                class="flex items-center gap-3"
                            >
                                <span
                                    class="flex size-6 shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand"
                                >
                                    <svg
                                        class="size-3.5"
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
                                </span>
                                <span class="text-ink-muted">{{ benefit }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="relative mx-auto w-full max-w-md lg:mx-0 lg:max-w-none">
                        <div
                            aria-hidden="true"
                            class="pointer-events-none absolute -inset-6 -z-10 rounded-[40px] bg-brand/10 blur-3xl"
                        />
                        <div
                            class="rounded-[24px] border border-stroke bg-surface p-5 shadow-2xl shadow-black/40 sm:p-6 md:p-8"
                        >
                            <GenerateFitnessPlanForm
                                :total-days="durationDays"
                                utm-content="landing_personal_meal_plan"
                                utm-campaign="landing_pages"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Warum -->
            <section
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        Warum ein persönlicher Ernährungsplan?
                    </h2>
                    <p class="mt-4 leading-relaxed text-ink-muted">
                        Allgemeine Ernährungspläne passen selten zu deinen
                        Zielen, Vorlieben und Alltag. Ein personalisierter Plan
                        berücksichtigt deinen individuellen Kalorienbedarf,
                        deine Makronährstoffverteilung, Allergien und Diätform.
                    </p>
                    <p class="mt-4 leading-relaxed text-ink-muted">
                        fytrr erstellt deinen personalisierten Ernährungsplan
                        mit KI basierend auf deinen Angaben. Kein Raten, kein
                        stundenlanges Recherchieren. In 60 Sekunden hast du
                        einen Plan, der auf dich zugeschnitten ist.
                    </p>
                </div>
            </section>

            <!-- Ernährungsplan mit KI -->
            <section
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        Ernährungsplan mit KI erstellen: so funktioniert es
                    </h2>
                    <p class="mt-4 leading-relaxed text-ink-muted">
                        Ein KI-Ernährungsplan nimmt dir die Rechnerei ab. Statt
                        selbst Kalorien und Makros zusammenzusuchen, gibst du
                        dein Ziel, deine Vorlieben und deinen Alltag an. Die KI
                        berechnet daraus deinen Bedarf und stellt einen
                        7-Tage-Plan zusammen, der zu dir passt.
                    </p>
                    <p class="mt-4 leading-relaxed text-ink-muted">
                        Der Unterschied zu einem Chatbot: Du bekommst keinen
                        Text zum Kopieren, sondern einen fertigen Plan als PDF,
                        inklusive Einkaufsliste. Kein Konto, keine Anmeldung,
                        keine versteckten Kosten.
                    </p>
                    <p class="mt-4 leading-relaxed text-ink-muted">
                        Und der Plan bleibt nicht statisch. In der fytrr App
                        passt sich dein KI-Ernährungsplan an deinen Fortschritt
                        an. Du kannst Mahlzeiten tauschen und Kalorien per Foto
                        tracken. So bleibt dein Plan Woche für Woche stimmig,
                        auch wenn sich dein Ziel oder dein Alltag ändert.
                    </p>
                    <p class="mt-4 leading-relaxed text-ink-muted">
                        Du willst zusätzlich trainieren? Erstell dir passend
                        dazu deinen
                        <a
                            href="/de/kostenloser-trainingsplan"
                            class="text-brand hover:underline"
                            >KI-Trainingsplan kostenlos</a
                        >.
                    </p>
                </div>
            </section>

            <!-- Was enthalten -->
            <section
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        Was dein KI-Ernährungsplan enthält
                    </h2>

                    <div class="mt-8 space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-brand">
                                7-Tage Mahlzeitenplan
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-ink-muted"
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
                            <h3 class="text-lg font-semibold text-brand">
                                Einkaufsliste
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-ink-muted"
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
                            <h3 class="text-lg font-semibold text-brand">
                                PDF Download
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-ink-muted"
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
            <section
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        So erstellst du deinen KI-Ernährungsplan
                    </h2>
                    <div class="mt-8 space-y-6">
                        <div class="flex gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand font-grotesk font-bold text-on-brand"
                            >
                                1
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-ink">
                                    Ziel angeben
                                </h3>
                                <p class="mt-1 text-ink-muted">
                                    Abnehmen, Muskelaufbau oder Gewicht halten.
                                    Wähle, was zu dir passt.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand font-grotesk font-bold text-on-brand"
                            >
                                2
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-ink">
                                    Vorlieben wählen
                                </h3>
                                <p class="mt-1 text-ink-muted">
                                    Diätform, Allergien, Budget. Die KI passt
                                    den Plan an deine Bedürfnisse an.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand font-grotesk font-bold text-on-brand"
                            >
                                3
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-ink">
                                    Ernährungsplan als PDF laden
                                </h3>
                                <p class="mt-1 text-ink-muted">
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
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        Für wen ist der KI-Ernährungsplan?
                    </h2>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2">
                        <div
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-brand">
                                Ernährungsplan zum Abnehmen
                            </h3>
                            <p class="mt-2 text-ink-muted">
                                Kaloriendefizit berechnet, sättigende
                                Mahlzeiten. Kombiniere mit einem
                                <a
                                    href="/de/kostenloser-trainingsplan/abnehmen"
                                    class="text-brand hover:underline"
                                    >Trainingsplan zum Abnehmen</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-brand">
                                Ernährungsplan für Muskelaufbau
                            </h3>
                            <p class="mt-2 text-ink-muted">
                                Kalorienüberschuss und Proteinziele. Passend
                                zum
                                <a
                                    href="/de/kostenloser-trainingsplan/muskelaufbau"
                                    class="text-brand hover:underline"
                                    >Trainingsplan Muskelaufbau</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-brand">
                                Ernährungsplan für Sportler
                            </h3>
                            <p class="mt-2 text-ink-muted">
                                Angepasst an Trainingsintensität.
                                Mahlzeiten-Timing rund ums Training.
                            </p>
                        </div>
                        <div
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-brand">
                                Ernährungsplan für Anfänger
                            </h3>
                            <p class="mt-2 text-ink-muted">
                                Einfache Rezepte, keine exotischen Zutaten.
                                Schau dir auch unseren
                                <a
                                    href="/de/kostenloser-trainingsplan/anfaenger"
                                    class="text-brand hover:underline"
                                    >Trainingsplan für Anfänger</a
                                >
                                an.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- App upsell banner -->
            <AppUpsellBanner class="border-t border-stroke" />

            <!-- FAQ -->
            <FAQSection
                :faqs="faqs"
                heading="Häufige Fragen zum KI-Ernährungsplan"
                class="border-t border-stroke"
            />

            <!-- Final CTA -->
            <section
                class="border-t border-stroke px-4 py-20 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl text-center">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        Bereit loszulegen?
                    </h2>
                    <p class="mt-4 text-lg text-ink-muted">
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
                            class="mt-8 rounded-xl bg-brand px-8 py-4 text-lg font-semibold text-on-brand hover:bg-brand/90"
                        >
                            Jetzt Ernährungsplan erstellen
                        </Button>
                    </GenerateFitnessPlanModal>
                </div>
            </section>

            <!-- Related Links -->
            <section
                class="border-t border-stroke px-4 py-12 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-xl font-bold text-ink">
                        Weitere kostenlose Tools
                    </h2>
                    <ul class="mt-4 space-y-2 text-ink-muted">
                        <li>
                            <a
                                href="/de/kostenlose-tools/kalorienrechner"
                                class="text-brand hover:underline"
                                >Kalorienrechner</a
                            >
                            · Berechne deinen Kalorienbedarf
                        </li>
                        <li>
                            <a
                                href="/de/kostenloser-trainingsplan"
                                class="text-brand hover:underline"
                                >Alle kostenlosen Trainingspläne</a
                            >
                        </li>
                        <li>
                            <a
                                href="/de/blog/ernaehrungsplan-erstellen"
                                class="text-brand hover:underline"
                                >Ernährungsplan erstellen: Die Anleitung</a
                            >
                        </li>
                        <li>
                            <a
                                href="/de/blog/kalorienbedarf-berechnen"
                                class="text-brand hover:underline"
                                >Kalorienbedarf berechnen</a
                            >
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>
