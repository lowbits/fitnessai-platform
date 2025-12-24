<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

import Footer from '@/components/Footer.vue';
import Header from '@/components/Header.vue';
import AuthorBox from '@/components/workoutPlan/AuthorBox.vue';
import CommonMistakes from '@/components/workoutPlan/CommonMistakes.vue';
import FAQSection from '@/components/workoutPlan/FAQSection.vue';
import Hero from '@/components/workoutPlan/Hero.vue';
import RelatedPlans from '@/components/workoutPlan/RelatedPlans.vue';
import WeekOverview from '@/components/workoutPlan/WeekOverview.vue';
import WhyItWorks from '@/components/workoutPlan/WhyItWorks.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';

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
    author: {
        name: string;
        title: string;
        bio: string;
        image: string;
    };
    reviewer?: {
        name: string;
        title: string;
    };
    lastUpdated: string;
    whyItWorks: {
        title: string;
        content: Array<{
            heading: string;
            text: string;
        }>;
    };
    commonMistakes: {
        title: string;
        mistakes: Array<{
            title: string;
            problem: string;
            consequence: string;
            solution: string;
            example: string;
        }>;
    };
    schema: object;
}

const props = defineProps<Props>();

const schemaJson = computed(() => JSON.stringify(props.schema));
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
        <meta
            property="og:image"
            content="https://fitnessai.me/fitness-plan.png"
        />

        <component :is="'script'" type="application/ld+json">
            {{ schemaJson }}
        </component>
    </Head>
    <main class="min-h-screen bg-dark-surfaces-900">
        <Header />

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

        <!-- Author Box Section -->
        <section
            v-if="author"
            class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
        >
            <AuthorBox
                :author="author"
                :reviewer="reviewer"
                :last-updated="lastUpdated"
                :show-disclosure="true"
            />
        </section>

        <!-- Why It Works Section -->
        <WhyItWorks
            v-if="whyItWorks?.content?.length"
            :why-it-works="whyItWorks.content"
        />

        <!-- Workout Details Section -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <WeekOverview
                :schedule="workout.schedule"
                :progression="workout.progression"
                :tips="workout.tips"
                :equipment="workout.equipment"
            />
        </section>

        <!-- Common Mistakes Section -->
        <CommonMistakes
            v-if="commonMistakes?.mistakes?.length"
            :mistakes="commonMistakes.mistakes"
        />

        <FAQSection :faqs="faqs" />
        <RelatedPlans :plans="relatedPlans" />

        <GenerateFitnessPlanModal />
    </main>
    <Footer />
</template>
