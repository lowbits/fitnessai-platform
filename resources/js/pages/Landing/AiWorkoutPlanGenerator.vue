<script setup lang="ts">
import FormCard from '@/components/FormCard.vue';
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
const canonical = `${baseUrl}/en/ai-workout-plan-generator`;

const faqs = [
    {
        question: 'Is the AI workout plan generator really free?',
        answer: 'Yes. You get a full personalized workout plan as a free PDF. No credit card, no account, no trial period. fytrr offers a premium subscription for ongoing plans and meal tracking, but the initial plan generation is completely free.',
    },
    {
        question: 'What information do I need to provide?',
        answer: 'Your gender, age, height, weight, fitness goal, skill level, available equipment, and how many days per week you want to train. The whole process takes about 60 seconds.',
    },
    {
        question: 'Can I get a meal plan with my workout plan?',
        answer: 'Yes. During setup, you can choose to include a meal plan. The AI will create a nutrition plan matched to your training schedule, with calculated calories, macros, and a shopping list.',
    },
    {
        question: 'Do I need gym equipment?',
        answer: 'No. Select "home training" and the AI generates a bodyweight-only plan. If you have gym access, it will include barbell, dumbbell, and machine exercises for faster results.',
    },
    {
        question: 'How long are the generated workout plans?',
        answer: 'Plans cover 4 weeks of training with progressive overload built in. This gives you enough structure to see real results and build lasting habits.',
    },
    {
        question: 'Is the AI workout plan personalized to me?',
        answer: 'Yes. The AI considers your body stats, fitness level, goal, equipment, and schedule. Two users with different profiles get completely different plans. This is not a template.',
    },
    {
        question: 'Can I regenerate my plan if I do not like it?',
        answer: 'With the free version, you get one plan. The fytrr app lets you regenerate plans anytime and swap individual exercises or meals with one tap.',
    },
    {
        question: 'What format is the download?',
        answer: 'PDF. It includes your complete workout schedule with exercises, sets, reps, rest times, and progressive overload targets. Print-ready and mobile-friendly.',
    },
    {
        question: 'How is this different from ChatGPT workout plans?',
        answer: 'ChatGPT gives you a generic list of exercises. fytrr builds a structured progressive program with exercise selection logic, proper periodization, calculated training volume, and optional nutrition matching. The output is a formatted PDF, not a chat message.',
    },
];

const webAppSchema = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'WebApplication',
        name: 'fytrr AI Workout Plan Generator',
        description:
            'Free AI-powered workout plan generator. Create personalized training plans for any goal.',
        applicationCategory: 'HealthApplication',
        operatingSystem: 'Web',
        url: canonical,
        offers: {
            '@type': 'Offer',
            price: '0',
            priceCurrency: 'USD',
        },
        aggregateRating: {
            '@type': 'AggregateRating',
            ratingValue: '5',
            reviewCount: '6',
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
    <Head
        title="Free AI Workout Plan Generator -- Personalized in 60 Seconds"
    >
        <meta
            name="description"
            content="Free AI workout plan generator. Create your personalized training plan for muscle gain, fat loss, or home workouts. PDF download, no signup needed."
        />
        <link rel="canonical" :href="canonical" />
        <meta
            property="og:title"
            content="Free AI Workout Plan Generator -- Personalized in 60 Seconds"
        />
        <meta
            property="og:description"
            content="Free AI workout plan generator. Create your personalized training plan for any goal."
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
                            Free AI Workout Plan
                            <span
                                class="bg-linear-to-r from-primary-400 to-primary-500 bg-clip-text text-transparent"
                                >Generator</span
                            >
                        </h1>
                        <p class="mt-4 text-lg text-gray-300">
                            Tell us your goal, fitness level, and available
                            equipment. Our AI creates a personalized workout
                            plan you can download as PDF. Free, instant, no
                            signup.
                        </p>
                    </div>
                    <div class="lg:w-1/2">
                        <FormCard>
                            <GenerateFitnessPlanForm
                                :total-days="durationDays"
                                utm-content="landing_ai_generator"
                                utm-campaign="landing_pages"
                            />
                        </FormCard>
                    </div>
                </div>
            </section>

            <!-- What Makes It Different -->
            <section class="px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        What Makes This Workout Plan Generator Different
                    </h2>

                    <div class="mt-8 space-y-8">
                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                AI-Powered Personalization
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    Not a template. The AI adapts to your body,
                                    goals, and schedule.
                                </li>
                                <li>
                                    Built on exercise science and progressive
                                    overload principles.
                                </li>
                                <li>
                                    Every plan is unique. Two users with
                                    different goals get completely different
                                    programs.
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Workout + Meal Plan Together
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    Optional: add a meal plan matched to your
                                    training. Get a
                                    <a
                                        href="/en/free-workout-and-meal-plan"
                                        class="text-primary-400 hover:underline"
                                        >free workout and meal plan</a
                                    >
                                    together.
                                </li>
                                <li>
                                    Calories and macros calculated for your
                                    specific goals.
                                </li>
                                <li>
                                    Training days get more energy, rest days
                                    adjust automatically.
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Free PDF Download, No Signup
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    Generate, download, done. No credit card, no
                                    account creation.
                                </li>
                                <li>
                                    Print-ready format with exercise
                                    instructions, sets, reps, and rest times.
                                </li>
                                <li>
                                    Take it to the gym or follow it at home.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Plans You Can Generate -->
            <section
                class="bg-dark-surfaces-800 px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        Plans You Can Generate
                    </h2>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2">
                        <a
                            href="/en/free-workout-plan/weight-loss"
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6 transition hover:border-primary-500"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Weight Loss Workout Plan
                            </h3>
                            <p class="mt-2 text-gray-300">
                                8-week fat-burning program combining strength
                                and cardio. Sustainable fat loss while
                                preserving muscle.
                            </p>
                        </a>
                        <a
                            href="/en/free-workout-plan/muscle-gain"
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6 transition hover:border-primary-500"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Muscle Gain Workout Plan
                            </h3>
                            <p class="mt-2 text-gray-300">
                                12-week progressive overload program with
                                compound lift focus. For beginners through
                                advanced.
                            </p>
                        </a>
                        <a
                            href="/en/free-workout-plan/home"
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6 transition hover:border-primary-500"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Home Workout Plan (No Equipment)
                            </h3>
                            <p class="mt-2 text-gray-300">
                                8-week bodyweight program. No gym required.
                                Effective for toning and functional strength.
                            </p>
                        </a>
                        <a
                            href="/en/free-workout-plan/beginner"
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6 transition hover:border-primary-500"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Beginner Workout Plan
                            </h3>
                            <p class="mt-2 text-gray-300">
                                6-week safe start program with full guidance.
                                Build confidence before building muscle.
                            </p>
                        </a>
                        <a
                            href="/en/free-workout-plan/strength"
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6 transition hover:border-primary-500 sm:col-span-2"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Strength Training Programs
                            </h3>
                            <p class="mt-2 text-gray-300">
                                10-week compound lift focus for intermediate to
                                advanced. Progressive overload with structured
                                periodization.
                            </p>
                        </a>
                    </div>
                </div>
            </section>

            <!-- How It Works -->
            <section class="px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        How the AI Workout Plan Generator Works
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
                                    Choose your goal
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Fat loss, muscle gain, strength, or general
                                    fitness. The AI builds your plan around what
                                    you want to achieve.
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
                                    Set your details
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Age, fitness level, available equipment, and
                                    how many days you want to train. Every
                                    detail shapes your plan.
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
                                    Get your AI-generated plan as PDF
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Your personalized workout plan is ready in
                                    60 seconds. Download it for free, no signup
                                    needed.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <FAQSection
                :faqs="faqs"
                heading="Frequently Asked Questions"
            />

            <!-- Final CTA -->
            <section class="px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <h2
                        class="font-display text-3xl font-bold text-white"
                    >
                        Ready to Generate Your Plan?
                    </h2>
                    <p class="mt-4 text-lg text-gray-300">
                        Create your free AI workout plan now. 60 seconds, no
                        signup, instant PDF download.
                    </p>
                    <GenerateFitnessPlanModal
                        utm-content="landing_ai_generator_cta"
                        utm-campaign="landing_pages"
                        #default="{ open }"
                    >
                        <Button
                            @click="open"
                            class="mt-8 rounded-xl bg-primary-500 px-8 py-4 text-lg font-semibold text-dark-surfaces-900 hover:bg-primary-400"
                        >
                            Generate Your Free Plan
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
                        More Free Fitness Tools
                    </h2>
                    <ul class="mt-4 space-y-2 text-gray-300">
                        <li>
                            <a
                                href="/en/free-tools/calorie-calculator"
                                class="text-primary-400 hover:underline"
                                >Calorie calculator</a
                            >
                            -- Find out how many calories you need
                        </li>
                        <li>
                            <a
                                href="/en/free-workout-plan"
                                class="text-primary-400 hover:underline"
                                >Browse all free workout plans</a
                            >
                        </li>
                        <li>
                            <a
                                href="/en/free-workout-and-meal-plan"
                                class="text-primary-400 hover:underline"
                                >Free workout and meal plan</a
                            >
                        </li>
                        <li>
                            <a
                                href="/en/blog/how-to-create-a-meal-plan"
                                class="text-primary-400 hover:underline"
                                >How to create a meal plan</a
                            >
                            -- Step-by-step guide
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>
