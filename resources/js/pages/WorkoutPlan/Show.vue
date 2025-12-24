<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import Footer from '@/components/Footer.vue';
import GenerateFitnessPlanForm from '@/components/GenerateFitnessPlanForm.vue';
import Header from '@/components/Header.vue';
import CTASection from '@/components/workoutPlan/CTASection.vue';
import FAQSection from '@/components/workoutPlan/FAQSection.vue';
import Hero from '@/components/workoutPlan/Hero.vue';
import RelatedPlans from '@/components/workoutPlan/RelatedPlans.vue';
import WeekOverview from '@/components/workoutPlan/WeekOverview.vue';

interface Props {
    type: string;
    meta: {
        title: string;
        description: string;
        h1: string;
        intro: string;
        canonical: string;
        keywords?: string[];
    };
    workout: {
        weeks: number;
        workouts_per_week: number;
        duration_minutes: number;
        level: string;
        equipment: string[];
        schedule: Array<{
            day: string;
            focus: string;
            exercises: Array<{
                name: string;
                sets: number;
                reps: string;
                rest: string;
                notes: string;
            }>;
        }>;
        progression: string;
        tips: string[];
    };
    faqs: Array<{
        question: string;
        answer: string;
    }>;
    relatedPlans: Array<{
        type: string;
        title: string;
        description: string;
        url: string;
    }>;
    schema: object;
}

const props = defineProps<Props>();
const showForm = ref(false);

const schemaJson = computed(() => JSON.stringify(props.schema));

/**
 *
 *
 * {
 *   "@type": "Article",
 *   "author": {
 *     "@type": "Person",
 *     "name": "Tobi Lobitz",
 *     "jobTitle": "Founder"
 *   },
 *   "reviewedBy": {
 *     "@type": "Person",
 *     "name": "Dr. Sarah Weber",
 *     "jobTitle": "Sportwissenschaftlerin"
 *   },
 *   "datePublished": "2024-12-24",
 *   "dateModified": "2024-12-24"
 * }
 */

const openForm = () => {
    showForm.value = true;
};

const closeForm = () => {
    showForm.value = false;
};
</script>

<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta name="description" :content="meta.description" />
        <link rel="canonical" :href="meta.canonical" />
        <meta name="keywords" :content="meta.keywords?.join(', ')" />
        <meta property="og:title" :content="meta.title" />
        <meta property="og:description" :content="meta.description" />
        <meta property="og:url" :content="meta.canonical" />
        <meta property="og:type" content="article" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="meta.title" />
        <meta name="twitter:description" :content="meta.description" />

        <component :is="'script'" type="application/ld+json">
            {{ schemaJson }}
        </component>
    </Head>
    <main class="min-h-screen bg-dark-surfaces-900">
        <Header />

        <div class="content-attribution">
            <!-- Primary Author: Du/Dein Team -->
            <div
                class="author-primary"
                itemscope
                itemtype="https://schema.org/Person"
            >
                <img itemprop="image" src="/authors/tobi-lobitz.jpg" />
                <div>
                    <h4 itemprop="name">Tobi Lobitz</h4>
                    <p itemprop="jobTitle">Gründer, fitnessAI.me</p>
                    <p class="text-sm text-gray-400">
                        Software-Entwickler mit Leidenschaft für
                        evidenzbasiertes Training. Erstellt datengetriebene
                        Fitness-Tools.
                    </p>
                </div>
            </div>

            <!-- Medical/Expert Review -->
            <div class="reviewer mt-4 border-t border-gray-700 pt-4">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-primary-300">
                        <!-- Checkmark icon -->
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-white">
                            Fachlich geprüft von:
                        </p>
                        <p class="text-sm text-gray-400">
                            Dr. Sarah Weber, Sportwissenschaftlerin
                        </p>
                    </div>
                </div>
            </div>

            <!-- Methodology Disclosure -->
            <details class="mt-4 text-xs text-gray-500">
                <summary class="cursor-pointer hover:text-gray-400">
                    Über unsere Inhalte
                </summary>
                <p class="mt-2">
                    Dieser Trainingsplan wurde mit KI-Unterstützung erstellt und
                    von zertifizierten Fachleuten überprüft. Alle Empfehlungen
                    basieren auf aktueller Sportforschung.
                </p>
            </details>
        </div>

        <Hero
            :title="meta.h1"
            :intro="meta.intro"
            :workout-details="{
                weeks: workout.weeks,
                workoutsPerWeek: workout.workouts_per_week,
                durationMinutes: workout.duration_minutes,
                level: workout.level,
            }"
            @open-form="openForm"
        />

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <WeekOverview
                :schedule="workout.schedule"
                :progression="workout.progression"
                :tips="workout.tips"
                :equipment="workout.equipment"
            />
        </section>

        <FAQSection :faqs="faqs" />
        <RelatedPlans :plans="relatedPlans" />
        <CTASection @open-form="openForm" />

        <Teleport to="body">
            <div
                v-if="showForm"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                @click.self="closeForm"
            >
                <div
                    class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-dark-surfaces-800 p-6 shadow-2xl"
                >
                    <button
                        @click="closeForm"
                        class="absolute top-4 right-4 text-gray-400 transition hover:text-white"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                    <GenerateFitnessPlanForm
                        :total-days="28"
                        @success="closeForm"
                    />
                </div>
            </div>
        </Teleport>
    </main>
    <Footer />
</template>
