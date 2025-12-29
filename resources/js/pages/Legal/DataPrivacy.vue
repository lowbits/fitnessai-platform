<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';

const { t } = useI18n();
</script>
<template>
    <Head>
        <title>{{ t('legal.data_privacy.meta.title') }}</title>
        <meta
            name="description"
            :content="t('legal.data_privacy.meta.description')"
        />
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
                        1. Datenschutz auf einen Blick
                    </h2>

                    <h3 class="mb-2 text-lg font-semibold">
                        Allgemeine Hinweise
                    </h3>
                    <p class="mb-4">
                        Die folgenden Hinweise geben einen einfachen Überblick
                        darüber, was mit Ihren personenbezogenen Daten passiert,
                        wenn Sie diese Website besuchen. Personenbezogene Daten
                        sind alle Daten, mit denen Sie persönlich identifiziert
                        werden können.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Datenerfassung auf dieser Website
                    </h3>
                    <p class="mb-2">
                        <strong
                            >Wer ist verantwortlich für die Datenerfassung auf
                            dieser Website?</strong
                        >
                    </p>
                    <p class="mb-4">
                        Die Datenverarbeitung auf dieser Website erfolgt durch
                        den Websitebetreiber. Dessen Kontaktdaten können Sie dem
                        Impressum dieser Website entnehmen.
                    </p>

                    <p class="mb-2">
                        <strong>Wie erfassen wir Ihre Daten?</strong>
                    </p>
                    <p class="mb-4">
                        Ihre Daten werden zum einen dadurch erhoben, dass Sie
                        uns diese mitteilen (z.B. bei der Erstellung Ihres
                        Trainingsplans). Andere Daten werden automatisch beim
                        Besuch der Website durch unsere IT-Systeme erfasst (z.B.
                        IP-Adresse).
                    </p>

                    <p class="mb-2">
                        <strong>Wofür nutzen wir Ihre Daten?</strong>
                    </p>
                    <p class="mb-4">
                        Ihre Daten werden verwendet, um personalisierte
                        Trainings- und Ernährungspläne mittels KI-Technologie zu
                        erstellen und Ihnen diese per E-Mail zuzustellen.
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">2. Hosting</h2>
                    <p class="mb-2"><strong>Hetzner Online GmbH</strong></p>
                    <p class="mb-4">
                        Wir hosten unsere Website bei Hetzner Online GmbH,
                        Industriestr. 25, 91710 Gunzenhausen, Deutschland.
                    </p>
                    <p class="mb-4">
                        Wenn Sie unsere Website besuchen, erfasst Hetzner
                        verschiedene Logfiles inklusive Ihrer IP-Adressen.
                        Details entnehmen Sie der Datenschutzerklärung von
                        Hetzner:
                        <a
                            href="https://www.hetzner.com/de/rechtliches/datenschutz"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-primary-500 hover:underline"
                            >https://www.hetzner.com/de/rechtliches/datenschutz</a
                        >
                    </p>
                    <p class="mb-2">
                        Die Verwendung von Hetzner erfolgt auf Grundlage von
                        Art. 6 Abs. 1 lit. f DSGVO. Wir haben ein berechtigtes
                        Interesse an einer möglichst zuverlässigen Darstellung
                        unserer Website.
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        3. Allgemeine Hinweise und Pflichtinformationen
                    </h2>

                    <h3 class="mb-2 text-lg font-semibold">Datenschutz</h3>
                    <p class="mb-4">
                        Die Betreiber dieser Seiten nehmen den Schutz Ihrer
                        persönlichen Daten sehr ernst. Wir behandeln Ihre
                        personenbezogenen Daten vertraulich und entsprechend der
                        gesetzlichen Datenschutzvorschriften sowie dieser
                        Datenschutzerklärung.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Hinweis zur verantwortlichen Stelle
                    </h3>
                    <p class="mb-4">
                        Die verantwortliche Stelle für die Datenverarbeitung auf
                        dieser Website ist:
                    </p>
                    <div class="mb-4 rounded bg-dark-surfaces-500 p-4">
                        <p>Tobias Lobitz</p>
                        <p>Annastraße 7</p>
                        <p>40477 Düsseldorf</p>
                        <p>E-Mail: hello@fytrr.com</p>
                    </div>

                    <h3 class="mb-2 text-lg font-semibold">Speicherdauer</h3>
                    <p class="mb-4">
                        Soweit innerhalb dieser Datenschutzerklärung keine
                        speziellere Speicherdauer genannt wurde, verbleiben Ihre
                        personenbezogenen Daten bei uns, bis der Zweck für die
                        Datenverarbeitung entfällt. Wenn Sie ein berechtigtes
                        Löschersuchen geltend machen oder eine Einwilligung zur
                        Datenverarbeitung widerrufen, werden Ihre Daten
                        gelöscht.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Widerruf Ihrer Einwilligung zur Datenverarbeitung
                    </h3>
                    <p class="mb-4">
                        Viele Datenverarbeitungsvorgänge sind nur mit Ihrer
                        ausdrücklichen Einwilligung möglich. Sie können eine
                        bereits erteilte Einwilligung jederzeit widerrufen. Die
                        Rechtmäßigkeit der bis zum Widerruf erfolgten
                        Datenverarbeitung bleibt vom Widerruf unberührt.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Beschwerderecht bei der zuständigen Aufsichtsbehörde
                    </h3>
                    <p class="mb-4">
                        Im Falle von Verstößen gegen die DSGVO steht den
                        Betroffenen ein Beschwerderecht bei einer
                        Aufsichtsbehörde zu. Das Beschwerderecht besteht
                        unbeschadet anderweitiger verwaltungsrechtlicher oder
                        gerichtlicher Rechtsbehelfe.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Recht auf Datenübertragbarkeit
                    </h3>
                    <p class="mb-4">
                        Sie haben das Recht, Daten, die wir auf Grundlage Ihrer
                        Einwilligung oder in Erfüllung eines Vertrags
                        automatisiert verarbeiten, an sich oder an einen Dritten
                        in einem gängigen, maschinenlesbaren Format aushändigen
                        zu lassen.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Auskunft, Löschung und Berichtigung
                    </h3>
                    <p class="mb-4">
                        Sie haben im Rahmen der geltenden gesetzlichen
                        Bestimmungen jederzeit das Recht auf unentgeltliche
                        Auskunft über Ihre gespeicherten personenbezogenen
                        Daten, deren Herkunft und Empfänger und den Zweck der
                        Datenverarbeitung und ggf. ein Recht auf Berichtigung
                        oder Löschung dieser Daten.
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        4. Datenerfassung auf dieser Website
                    </h2>

                    <h3 class="mb-2 text-lg font-semibold">
                        Server-Log-Dateien
                    </h3>
                    <p class="mb-2">
                        Der Provider der Seiten erhebt und speichert automatisch
                        Informationen in sogenannten Server-Log-Dateien:
                    </p>
                    <ul class="mb-4 list-inside list-disc space-y-1">
                        <li>Browsertyp und Browserversion</li>
                        <li>Verwendetes Betriebssystem</li>
                        <li>Referrer URL</li>
                        <li>Hostname des zugreifenden Rechners</li>
                        <li>Uhrzeit der Serveranfrage</li>
                        <li>IP-Adresse</li>
                    </ul>
                    <p class="mb-4">
                        Eine Zusammenführung dieser Daten mit anderen
                        Datenquellen wird nicht vorgenommen. Die Erfassung
                        dieser Daten erfolgt auf Grundlage von Art. 6 Abs. 1
                        lit. f DSGVO.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Anfrage per E-Mail
                    </h3>
                    <p class="mb-4">
                        Wenn Sie uns per E-Mail kontaktieren, wird Ihre Anfrage
                        inklusive aller daraus hervorgehenden personenbezogenen
                        Daten (Name, Anfrage) zum Zwecke der Bearbeitung Ihres
                        Anliegens bei uns gespeichert und verarbeitet. Diese
                        Daten geben wir nicht ohne Ihre Einwilligung weiter.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Registrierung und Nutzung des Services
                    </h3>
                    <p class="mb-4">
                        Bei der Nutzung unseres Services zur Erstellung von
                        Trainings- und Ernährungsplänen erfassen wir folgende
                        Daten:
                    </p>
                    <ul class="mb-4 list-inside list-disc space-y-1">
                        <li>E-Mail-Adresse (zur Zusendung der Pläne)</li>
                        <li>Körperdaten (Gewicht, Größe, Alter, Geschlecht)</li>
                        <li>Fitnessziele und Präferenzen</li>
                        <li>
                            Gesundheitsinformationen (soweit von Ihnen
                            angegeben)
                        </li>
                    </ul>
                    <p class="mb-4">
                        Diese Daten werden ausschließlich zur Erstellung Ihrer
                        personalisierten Pläne verwendet und auf Grundlage Ihrer
                        Einwilligung (Art. 6 Abs. 1 lit. a DSGVO) verarbeitet.
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        5. Externe Dienste
                    </h2>

                    <h3 class="mb-2 text-lg font-semibold">KI-Dienste</h3>
                    <p class="mb-4">
                        Zur Erstellung personalisierter Trainings- und
                        Ernährungspläne nutzen wir KI-basierte Dienste von
                        Drittanbietern (USA/EU). Ihre eingegebenen Daten werden
                        an diese Dienste übermittelt, um die Pläne zu
                        generieren. Die Verarbeitung erfolgt auf Grundlage von
                        Art. 6 Abs. 1 lit. a DSGVO (Ihre Einwilligung) und Art.
                        6 Abs. 1 lit. b DSGVO (Vertragserfüllung).
                    </p>
                    <p class="mb-4">
                        Wir haben mit den Anbietern
                        Auftragsverarbeitungsverträge (AVV) geschlossen. Bei
                        Übermittlung in Drittländer außerhalb der EU erfolgt
                        dies auf Basis von Standardvertragsklauseln.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Resend (E-Mail-Versand)
                    </h3>
                    <p class="mb-4">
                        Wir nutzen Resend zum Versand Ihrer personalisierten
                        Pläne per E-Mail. Dabei wird Ihre E-Mail-Adresse an
                        Resend übermittelt. Weitere Informationen:
                        <a
                            href="https://resend.com/legal/privacy-policy"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-primary-500 hover:underline"
                            >https://resend.com/legal/privacy-policy</a
                        >
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Plausible Analytics
                    </h3>
                    <p class="mb-4">
                        Wir nutzen Plausible Analytics, ein
                        datenschutzfreundlichen Analyse-Tool. Plausible
                        verwendet keine Cookies und erfasst keine
                        personenbezogenen Daten. Die Erfassung erfolgt
                        anonymisiert.
                    </p>

                    <h3 class="mb-2 text-lg font-semibold">
                        Polar.sh (Zahlungsabwicklung)
                    </h3>
                    <p class="mb-4">
                        Für die Zahlungsabwicklung nutzen wir Polar.sh. Bei
                        Zahlungsvorgängen werden Ihre Zahlungsdaten direkt an
                        Polar.sh übermittelt und dort verarbeitet. Wir speichern
                        keine vollständigen Zahlungsdaten. Rechtsgrundlage ist
                        Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung).
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        6. Datenlöschung
                    </h2>
                    <p class="mb-4">
                        Ihre Daten werden gelöscht, sobald sie für die
                        Erreichung des Zweckes ihrer Erhebung nicht mehr
                        erforderlich sind. Sie können jederzeit die Löschung
                        Ihrer Daten per E-Mail an hello@fytrr.com beantragen.
                    </p>
                </section>

                <section>
                    <h2 class="mb-4 text-2xl font-semibold">
                        7. SSL- bzw. TLS-Verschlüsselung
                    </h2>
                    <p class="mb-4">
                        Diese Seite nutzt aus Sicherheitsgründen und zum Schutz
                        der Übertragung vertraulicher Inhalte eine SSL- bzw.
                        TLS-Verschlüsselung. Eine verschlüsselte Verbindung
                        erkennen Sie daran, dass die Adresszeile des Browsers
                        von "http://" auf "https://" wechselt.
                    </p>
                </section>

                <div
                    class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-500"
                >
                    <p>Stand: Dezember 2025</p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
