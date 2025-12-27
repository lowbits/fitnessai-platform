<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
</script>
<template>
    <GuestLayout>
        <div class="container mx-auto mt-10 flex-1 space-y-5 text-white">
            <h1 class="mb-8 text-3xl font-bold">
                Haftungsausschluss & Gesundheitshinweise
            </h1>

            <div class="mb-10 max-w-prose space-y-6">
                <div class="mb-8 border-l-4 border-yellow-400 bg-yellow-50 p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
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
                                Wichtiger Hinweis
                            </h2>
                            <p class="mt-2 text-yellow-700">
                                Bitte lesen Sie diese Hinweise sorgfältig, bevor
                                Sie die Plattform nutzen.
                            </p>
                        </div>
                    </div>
                </div>


                <div class="space-y-8 ">
                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            Kein Ersatz für professionelle Beratung
                        </h2>
                        <p class="mb-4 font-semibold">
                            Die über FitnessAI bereitgestellten Trainings- und
                            Ernährungspläne ersetzen NICHT die Beratung durch
                            qualifizierte Fachkräfte wie Ärzte,
                            Ernährungsberater, Personal Trainer oder andere
                            Gesundheitsexperten.
                        </p>
                        <p class="mb-4">
                            Die Inhalte dienen ausschließlich zu allgemeinen
                            Informationszwecken. Sie stellen keine medizinische
                            Diagnose, Behandlung oder professionelle
                            Gesundheitsberatung dar.
                        </p>
                        <p>
                            Konsultieren Sie immer einen Arzt oder
                            qualifizierten Gesundheitsdienstleister, bevor Sie
                            mit einem neuen Trainings- oder Ernährungsprogramm
                            beginnen.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            Medizinische Vorerkrankungen & Risiken
                        </h2>
                        <div
                            class="mb-4 rounded border border-red-200 bg-red-50 p-4"
                        >
                            <p class="mb-2 font-semibold text-red-800">
                                Konsultieren Sie unbedingt einen Arzt, wenn Sie:
                            </p>
                            <ul
                                class="list-inside list-disc space-y-1 text-red-700"
                            >
                                <li>
                                    An Herz-Kreislauf-Erkrankungen leiden oder
                                    litten
                                </li>
                                <li>
                                    Diabetes oder Stoffwechselstörungen haben
                                </li>
                                <li>
                                    Gelenkprobleme, Rückenschmerzen oder
                                    Verletzungen haben
                                </li>
                                <li>Schwanger sind oder stillen</li>
                                <li>Essstörungen haben oder hatten</li>
                                <li>
                                    Bluthochdruck oder andere chronische
                                    Erkrankungen haben
                                </li>
                                <li>
                                    Medikamente einnehmen, die durch Sport oder
                                    Ernährung beeinflusst werden können
                                </li>
                                <li>
                                    Über 40 Jahre alt sind und lange nicht
                                    trainiert haben
                                </li>
                                <li>
                                    Sich unsicher über Ihren Gesundheitszustand
                                    sind
                                </li>
                            </ul>
                        </div>
                        <p>
                            Das Ignorieren gesundheitlicher Probleme kann zu
                            schweren Verletzungen oder gesundheitlichen
                            Komplikationen führen.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            KI-generierte Inhalte
                        </h2>
                        <p class="mb-4">
                            Die Trainings- und Ernährungspläne werden mittels
                            Künstlicher Intelligenz (KI) erstellt. Obwohl wir
                            uns bemühen, qualitativ hochwertige Empfehlungen zu
                            liefern, können KI-Systeme Fehler machen oder
                            ungeeignete Vorschläge generieren.
                        </p>
                        <p class="mb-4 font-semibold">
                            Die KI kennt nicht Ihre vollständige medizinische
                            Geschichte, aktuelle Beschwerden oder individuellen
                            körperlichen Einschränkungen.
                        </p>
                        <p>
                            Nutzen Sie Ihren gesunden Menschenverstand und hören
                            Sie auf Ihren Körper. Wenn etwas schmerzhaft ist
                            oder sich falsch anfühlt, STOPPEN Sie sofort.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            Eigenverantwortung
                        </h2>
                        <p class="mb-4">
                            Sie tragen die volle Verantwortung für die Umsetzung
                            der bereitgestellten Pläne. Sie entscheiden selbst,
                            ob und wie Sie die Empfehlungen befolgen.
                        </p>
                        <p class="mb-4">
                            Der Anbieter übernimmt keine Verantwortung für:
                        </p>
                        <ul class="mb-4 list-inside list-disc space-y-2">
                            <li>Verletzungen, die durch Training entstehen</li>
                            <li>
                                Gesundheitliche Probleme durch
                                Ernährungsumstellungen
                            </li>
                            <li>
                                Allergische Reaktionen auf empfohlene
                                Lebensmittel
                            </li>
                            <li>Unerwünschte Gewichtsveränderungen</li>
                            <li>
                                Verschlechterung bestehender Gesundheitsprobleme
                            </li>
                            <li>
                                Fehlerhafte oder unvollständige KI-Empfehlungen
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            Ernährungshinweise
                        </h2>
                        <p class="mb-4">
                            Ernährungspläne sind allgemeine Vorschläge und
                            berücksichtigen möglicherweise nicht:
                        </p>
                        <ul class="mb-4 list-inside list-disc space-y-1">
                            <li>
                                Lebensmittelallergien oder Unverträglichkeiten
                            </li>
                            <li>
                                Spezifische diätetische Anforderungen aufgrund
                                von Erkrankungen
                            </li>
                            <li>Medikamenten-Nahrungsmittel-Interaktionen</li>
                            <li>Individuelle Stoffwechselbesonderheiten</li>
                            <li>
                                Kulturelle oder religiöse
                                Ernährungsanforderungen
                            </li>
                        </ul>
                        <p>
                            Informieren Sie sich über die vorgeschlagenen
                            Lebensmittel und prüfen Sie, ob diese für Sie
                            geeignet sind.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            Trainingshinweise
                        </h2>
                        <p class="mb-4">
                            Trainingsübungen können bei falscher Ausführung zu
                            Verletzungen führen.
                        </p>
                        <div
                            class="mb-4 rounded border border-blue-200 bg-blue-50 p-4"
                        >
                            <p class="mb-2 font-semibold text-blue-900">
                                Wichtige Sicherheitshinweise:
                            </p>
                            <ul
                                class="list-inside list-disc space-y-1 text-blue-800"
                            >
                                <li>
                                    Beginnen Sie langsam und steigern Sie die
                                    Intensität schrittweise
                                </li>
                                <li>
                                    Lernen Sie die korrekte Übungsausführung
                                    (ggf. mit Trainer)
                                </li>
                                <li>
                                    Verwenden Sie angemessene Gewichte -
                                    Ego-Lifting führt zu Verletzungen
                                </li>
                                <li>Wärmen Sie sich vor dem Training auf</li>
                                <li>Achten Sie auf ausreichende Erholung</li>
                                <li>Stoppen Sie bei Schmerzen sofort</li>
                                <li>
                                    Trainieren Sie nicht krank oder übermüdet
                                </li>
                            </ul>
                        </div>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            Keine Garantie für Ergebnisse
                        </h2>
                        <p class="mb-4">
                            Der Anbieter gibt keine Garantie oder Zusicherung
                            für bestimmte Ergebnisse (Gewichtsverlust,
                            Muskelaufbau, Leistungssteigerung etc.).
                        </p>
                        <p>
                            Individuelle Ergebnisse variieren stark abhängig von
                            Faktoren wie Genetik, Ausgangszustand, Konsistenz,
                            Schlaf, Stress und vielen weiteren Variablen.
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            Haftungsausschluss
                        </h2>
                        <p class="mb-4 font-semibold">
                            Der Anbieter haftet nicht für Schäden jeglicher Art
                            (einschließlich, aber nicht beschränkt auf direkte,
                            indirekte, zufällige oder Folgeschäden), die aus der
                            Nutzung oder der Unfähigkeit zur Nutzung der
                            bereitgestellten Inhalte entstehen.
                        </p>
                        <p class="mb-4">Dies gilt insbesondere für:</p>
                        <ul class="mb-4 list-inside list-disc space-y-1">
                            <li>Gesundheitliche Schäden oder Verletzungen</li>
                            <li>Verschlechterung bestehender Erkrankungen</li>
                            <li>
                                Ausbleibende oder nicht erwartete
                                Trainingsergebnisse
                            </li>
                            <li>
                                Unerwünschte Nebenwirkungen von
                                Ernährungsumstellungen
                            </li>
                            <li>Fehler in den KI-generierten Empfehlungen</li>
                        </ul>
                        <p>
                            Die gesetzlichen Haftungsbeschränkungen bleiben
                            unberührt (siehe AGB).
                        </p>
                    </section>

                    <section>
                        <h2 class="mb-4 text-2xl font-semibold">
                            Aktualisierungen
                        </h2>
                        <p>
                            Dieser Haftungsausschluss kann jederzeit
                            aktualisiert werden. Die jeweils aktuelle Version
                            ist auf der Website einsehbar.
                        </p>
                    </section>

                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="rounded bg-dark-surfaces-500 p-6">
                            <p class="mb-2 font-semibold">
                                Durch die Nutzung von FitnessAI bestätigen Sie,
                                dass Sie:
                            </p>
                            <ul
                                class="list-inside list-disc space-y-1 "
                            >
                                <li>
                                    Diese Hinweise vollständig gelesen und
                                    verstanden haben
                                </li>
                                <li>Die Risiken verstehen und akzeptieren</li>
                                <li>
                                    Die volle Verantwortung für Ihre Gesundheit
                                    übernehmen
                                </li>
                                <li>
                                    Im Zweifelsfall professionelle Beratung
                                    einholen werden
                                </li>
                            </ul>
                        </div>
                    </div>

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
