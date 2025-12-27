<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';

const { t } = useI18n();
</script>
<template>
    <Head>
        <title>{{ t('legal.terms.meta.title') }}</title>
        <meta
            name="description"
            :content="t('legal.terms.meta.description')"
        />
    </Head>
    <GuestLayout>
        <div class="container mx-auto mt-10 flex-1 space-y-5 text-white">
            <h1 class="mb-8 text-3xl font-bold">
                {{ t('legal.terms.title') }}
            </h1>

            <div class="mb-10 max-w-prose space-y-6">
                <div class="space-y-8">
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 1 Geltungsbereich
                        </h2>
                        <p class="mb-2">
                            (1) Diese Allgemeinen Geschäftsbedingungen
                            (nachfolgend "AGB") gelten für alle Verträge über
                            die Nutzung der Plattform FitnessAI (nachfolgend
                            "Plattform"), die zwischen Tobias Lobitz (nachfolgend
                            "Anbieter") und dem Nutzer (nachfolgend "Kunde")
                            geschlossen werden.
                        </p>
                        <p class="mb-2">
                            (2) Die Plattform bietet einen Service zur
                            automatisierten Erstellung personalisierter
                            Trainings- und Ernährungspläne mittels künstlicher
                            Intelligenz.
                        </p>
                        <p>
                            (3) Abweichende Bedingungen des Kunden werden nicht
                            anerkannt, es sei denn, der Anbieter stimmt ihrer
                            Geltung ausdrücklich schriftlich zu.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 2 Vertragsschluss
                        </h2>
                        <p class="mb-2">
                            (1) Der Vertrag kommt durch die Nutzung der
                            Plattform und die Anforderung eines Trainings- oder
                            Ernährungsplans zustande.
                        </p>
                        <p class="mb-2">
                            (2) Mit der Nutzung der Plattform akzeptiert der
                            Kunde diese AGB sowie die Datenschutzerklärung.
                        </p>
                        <p>
                            (3) Die Vertragsdurchführung und Kontaktaufnahme
                            erfolgen in deutscher oder englischer Sprache.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 3 Leistungsumfang
                        </h2>
                        <p class="mb-2">
                            (1) Der Anbieter stellt eine Web-Plattform zur
                            Verfügung, über die Kunden personalisierte
                            Trainings- und Ernährungspläne erstellen lassen
                            können.
                        </p>
                        <p class="mb-2">
                            (2) Die Pläne werden auf Basis der vom Kunden
                            angegebenen Daten (Gewicht, Größe, Alter,
                            Fitnessziele etc.) mittels KI-Technologie erstellt
                            und per E-Mail im PDF-Format zugestellt.
                        </p>
                        <p class="mb-2">
                            (3) Der Anbieter schuldet keine bestimmte
                            Verfügbarkeit der Plattform. Der Anbieter ist
                            berechtigt, die Plattform aus technischen oder
                            wartungsbedingten Gründen vorübergehend außer
                            Betrieb zu nehmen.
                        </p>
                        <p>
                            (4) Der Anbieter behält sich vor, die Leistungen
                            jederzeit zu erweitern, zu verändern oder
                            einzustellen.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 4 Preise und Zahlung
                        </h2>
                        <p class="mb-2">
                            (1) Die jeweils gültigen Preise sind auf der Website
                            ersichtlich und verstehen sich inklusive der
                            gesetzlichen Umsatzsteuer.
                        </p>
                        <p class="mb-2">
                            (2) Die Zahlung erfolgt über den auf der Plattform
                            angebotenen Zahlungsdienstleister (Polar.sh).
                        </p>
                        <p>
                            (3) Der Anbieter behält sich vor, die Preise
                            jederzeit anzupassen. Bereits getätigte Zahlungen
                            sind von Preisänderungen nicht betroffen.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 5 Pflichten des Kunden
                        </h2>
                        <p class="mb-2">
                            (1) Der Kunde verpflichtet sich, wahrheitsgemäße
                            Angaben zu machen. Falsche oder irreführende Angaben
                            können zu fehlerhaften oder ungeeigneten Plänen
                            führen.
                        </p>
                        <p class="mb-2">
                            (2) Der Kunde ist selbst verantwortlich für die
                            Umsetzung der erstellten Pläne und muss seine
                            körperliche Verfassung sowie eventuelle
                            gesundheitliche Einschränkungen berücksichtigen.
                        </p>
                        <p>
                            (3) Der Kunde darf die Plattform nicht
                            missbräuchlich nutzen oder gegen geltendes Recht
                            verstoßen.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 6 Widerrufsrecht
                        </h2>
                        <p class="mb-2">
                            (1) Verbrauchern steht ein gesetzliches
                            Widerrufsrecht zu.
                        </p>

                        <div class="my-4 rounded bg-dark-surfaces-500 p-6">
                            <h3 class="mb-3 font-semibold">
                                Widerrufsbelehrung
                            </h3>

                            <p class="mb-2 font-semibold">Widerrufsrecht</p>
                            <p class="mb-4">
                                Sie haben das Recht, binnen vierzehn Tagen ohne
                                Angabe von Gründen diesen Vertrag zu widerrufen.
                            </p>

                            <p class="mb-4">
                                Die Widerrufsfrist beträgt vierzehn Tage ab dem
                                Tag des Vertragsabschlusses.
                            </p>

                            <p class="mb-4">
                                Um Ihr Widerrufsrecht auszuüben, müssen Sie uns
                                (Tobias Lobitz, Annastraße 7, E-Mail: hello@fitnessai.me)
                                mittels einer eindeutigen Erklärung (z.B. per
                                E-Mail) über Ihren Entschluss, diesen Vertrag zu
                                widerrufen, informieren.
                            </p>

                            <p class="mb-2 font-semibold">
                                Folgen des Widerrufs
                            </p>
                            <p class="mb-4">
                                Wenn Sie diesen Vertrag widerrufen, haben wir
                                Ihnen alle Zahlungen, die wir von Ihnen erhalten
                                haben, unverzüglich und spätestens binnen
                                vierzehn Tagen ab dem Tag zurückzuzahlen, an dem
                                die Mitteilung über Ihren Widerruf dieses
                                Vertrags bei uns eingegangen ist.
                            </p>

                            <p class="mb-2 font-semibold">
                                Vorzeitiges Erlöschen des Widerrufsrechts
                            </p>
                            <p>
                                Das Widerrufsrecht erlischt bei einem Vertrag
                                zur Erbringung von Dienstleistungen, wenn der
                                Unternehmer die Dienstleistung vollständig
                                erbracht hat und mit der Ausführung erst
                                begonnen hat, nachdem der Verbraucher dazu seine
                                ausdrückliche Zustimmung gegeben hat und
                                gleichzeitig seine Kenntnis davon bestätigt hat,
                                dass er sein Widerrufsrecht bei vollständiger
                                Vertragserfüllung verliert.
                            </p>
                        </div>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 7 Haftung und Gewährleistung
                        </h2>
                        <p class="mb-2">
                            (1) Der Anbieter haftet nach den gesetzlichen
                            Bestimmungen für Vorsatz und grobe Fahrlässigkeit
                            sowie für die Verletzung von Leben, Körper oder
                            Gesundheit.
                        </p>
                        <p class="mb-2">
                            (2) Im Übrigen haftet der Anbieter nur bei der
                            Verletzung wesentlicher Vertragspflichten
                            (Kardinalpflichten). In diesem Fall ist die Haftung
                            auf den vertragstypischen, vorhersehbaren Schaden
                            begrenzt.
                        </p>
                        <p class="mb-2">
                            (3) Die Haftung nach dem Produkthaftungsgesetz
                            bleibt unberührt.
                        </p>
                        <p>
                            (4) Eine weitergehende Haftung ist ausgeschlossen.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 8 Urheberrecht und Nutzungsrechte
                        </h2>
                        <p class="mb-2">
                            (1) Alle durch den Anbieter erstellten Inhalte und
                            Werke auf dieser Plattform unterliegen dem deutschen
                            Urheberrecht.
                        </p>
                        <p class="mb-2">
                            (2) Die erstellten Trainings- und Ernährungspläne
                            sind ausschließlich für den persönlichen,
                            nicht-kommerziellen Gebrauch des Kunden bestimmt.
                        </p>
                        <p>
                            (3) Eine Weitergabe, Vervielfältigung oder
                            kommerzielle Nutzung der Pläne ist ohne
                            ausdrückliche Zustimmung des Anbieters nicht
                            gestattet.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 9 Datenschutz
                        </h2>
                        <p>
                            Der Anbieter verarbeitet personenbezogene Daten des
                            Kunden gemäß den geltenden Datenschutzbestimmungen.
                            Einzelheiten ergeben sich aus der
                            Datenschutzerklärung, die auf der Website einsehbar
                            ist.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            § 10 Schlussbestimmungen
                        </h2>
                        <p class="mb-2">
                            (1) Es gilt das Recht der Bundesrepublik Deutschland
                            unter Ausschluss des UN-Kaufrechts.
                        </p>
                        <p class="mb-2">
                            (2) Ist der Kunde Kaufmann, juristische Person des
                            öffentlichen Rechts oder öffentlich-rechtliches
                            Sondervermögen, ist ausschließlicher Gerichtsstand
                            für alle Streitigkeiten aus Vertragsverhältnissen
                            zwischen dem Kunden und dem Anbieter der Sitz des
                            Anbieters.
                        </p>
                        <p class="mb-2">
                            (3) Sollten einzelne Bestimmungen dieser AGB
                            unwirksam sein oder werden, bleibt die Wirksamkeit
                            der übrigen Bestimmungen hiervon unberührt.
                        </p>
                    </section>

                    <div
                        class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-500"
                    >
                        <p>Stand: Dezember 2024</p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
