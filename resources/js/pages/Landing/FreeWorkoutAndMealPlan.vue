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
const canonical = `${baseUrl}/en/free-workout-and-meal-plan`;

const faqs = [
    {
        question: 'Is the workout and meal plan really free?',
        answer: 'Yes, completely free. You get a full workout plan and meal plan as PDF. No credit card, no account, no hidden costs. fytrr offers a premium subscription for ongoing plans, but the initial plan is free.',
    },
    {
        question: 'Can I customize my diet for allergies?',
        answer: 'Yes. During setup, you can select your dietary preference (vegan, vegetarian, keto, and more) and specify allergies. The AI generates meals that respect your restrictions.',
    },
    {
        question: 'How long is the plan?',
        answer: 'The default plan covers 4 weeks of training and 7 days of meals. This gives you enough structure to build habits and see initial results.',
    },
    {
        question: 'Do I need gym equipment?',
        answer: 'No. You can choose "home training" and get a bodyweight-only workout plan. If you have access to a gym, the plan will include equipment-based exercises for faster progress.',
    },
    {
        question: 'Can I download the plan as PDF?',
        answer: 'Yes. After generation, you receive your complete workout and meal plan as a downloadable PDF. Print it, save it on your phone, or share it with your training partner.',
    },
    {
        question: 'How is this different from other free workout plans?',
        answer: 'Most free plans give you a generic template. fytrr uses AI to personalize everything: your calorie target, macro split, exercise selection, training volume, and meal timing are all calculated based on your body and goals. Plus you get both workout and nutrition in one plan.',
    },
    {
        question: 'What if I do not like a meal in the plan?',
        answer: 'The free PDF plan includes a full week of meals. If you want to swap individual meals while keeping your macros intact, the fytrr app lets you do that with one tap.',
    },
];

const webAppSchema = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'WebApplication',
        name: 'fytrr Free Workout and Meal Plan Generator',
        description:
            'Create a free personalized workout and meal plan. AI-powered, instant PDF download.',
        applicationCategory: 'HealthApplication',
        url: canonical,
        offers: {
            '@type': 'Offer',
            price: '0',
            priceCurrency: 'USD',
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
    <Head title="Free Workout and Meal Plan -- Personalized in 60 Seconds">
        <meta
            name="description"
            content="Get your free workout and meal plan together. AI-powered, personalized for your goals. Covers training, diet, and shopping list. PDF download, no signup."
        />
        <link rel="canonical" :href="canonical" />
        <meta
            property="og:title"
            content="Free Workout and Meal Plan -- Personalized in 60 Seconds"
        />
        <meta
            property="og:description"
            content="Get your free workout and meal plan together. AI-powered, personalized for your goals."
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
                            Free Workout and Meal Plan
                            <span
                                class="bg-linear-to-r from-primary-400 to-primary-500 bg-clip-text text-transparent"
                                >Built for Your Goals</span
                            >
                        </h1>
                        <p class="mt-4 text-lg text-gray-300">
                            Tell us your goal, fitness level, and preferences.
                            Our AI creates a personalized workout and diet plan
                            you can download as PDF. No signup, no cost.
                        </p>
                    </div>
                    <div class="lg:w-1/2">
                        <FormCard>
                            <GenerateFitnessPlanForm
                                :total-days="durationDays"
                                utm-content="landing_workout_meal_plan"
                                utm-campaign="landing_pages"
                            />
                        </FormCard>
                    </div>
                </div>
            </section>

            <!-- Why Combined -->
            <section class="px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        Why You Need a Combined Workout and Diet Plan
                    </h2>
                    <p class="mt-4 leading-relaxed text-gray-300">
                        Training without nutrition is half the equation. You can
                        work out five days a week and still see zero results if
                        your diet is working against you. A calorie-deficit diet
                        makes fat-burning workouts effective. A protein-surplus
                        meal plan makes strength training productive.
                    </p>
                    <p class="mt-4 leading-relaxed text-gray-300">
                        fytrr combines both into one personalized plan. Your
                        exercise and diet plan are matched to each other:
                        training days get more carbs for energy, rest days
                        adjust calories down. Everything is calculated, nothing
                        is generic.
                    </p>
                </div>
            </section>

            <!-- What You Get -->
            <section
                class="bg-dark-surfaces-800 px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        What You Get in Your Free Plan
                    </h2>

                    <div class="mt-8 space-y-8">
                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Your Workout Plan
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    Personalized training schedule based on your
                                    goal, fitness level, and available days
                                </li>
                                <li>
                                    Progressive overload built in so you keep
                                    making progress
                                </li>
                                <li>
                                    Works for
                                    <a
                                        href="/en/free-workout-plan/home"
                                        class="text-primary-400 hover:underline"
                                        >home training</a
                                    >
                                    (no equipment) or gym
                                </li>
                                <li>
                                    Clear exercise instructions with sets, reps,
                                    and rest times
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Your Meal Plan
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    7-day meal plan matched to your training
                                    schedule
                                </li>
                                <li>
                                    Calorie and macro targets calculated for
                                    your body and goal
                                </li>
                                <li>
                                    Shopping list included so you know exactly
                                    what to buy
                                </li>
                                <li>
                                    Accommodates allergies, vegan, vegetarian,
                                    keto, and other diets
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Your PDF Download
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-gray-300"
                            >
                                <li>
                                    Everything in one document: workouts, meals,
                                    shopping list
                                </li>
                                <li>
                                    Print-friendly format you can take to the
                                    gym or kitchen
                                </li>
                                <li>
                                    No account or signup required. Generate,
                                    download, done.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works -->
            <section class="px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        How It Works
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
                                    Tell us your goal
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Weight loss, muscle gain, general fitness,
                                    or endurance. Pick what matters to you.
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
                                    Set your preferences
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Diet type, available equipment, how many
                                    days you want to train. The AI adapts to
                                    you.
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
                                    Get your combined plan as PDF
                                </h3>
                                <p class="mt-1 text-gray-300">
                                    Your personalized workout and meal plan is
                                    ready in 60 seconds. Download it for free.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Who Is This For -->
            <section
                class="bg-dark-surfaces-800 px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="font-display text-2xl font-bold text-white">
                        Who Is This For?
                    </h2>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2">
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Beginners
                            </h3>
                            <p class="mt-2 text-gray-300">
                                A safe starting point with both training and
                                nutrition guidance. No experience needed. Check
                                out our
                                <a
                                    href="/en/free-workout-plan/beginner"
                                    class="text-primary-400 hover:underline"
                                    >beginner workout plan</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Weight Loss
                            </h3>
                            <p class="mt-2 text-gray-300">
                                A calorie-deficit diet paired with fat-burning
                                workouts. See our dedicated
                                <a
                                    href="/en/free-workout-plan/weight-loss"
                                    class="text-primary-400 hover:underline"
                                    >workout plans for weight loss</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Muscle Gain
                            </h3>
                            <p class="mt-2 text-gray-300">
                                A calorie-surplus meal plan paired with
                                progressive strength training. See our
                                <a
                                    href="/en/free-workout-plan/muscle-gain"
                                    class="text-primary-400 hover:underline"
                                    >muscle gain workout plan</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-900 p-6"
                        >
                            <h3
                                class="text-lg font-semibold text-primary-300"
                            >
                                Home Training
                            </h3>
                            <p class="mt-2 text-gray-300">
                                No-equipment workouts with meal plans that do
                                not require special ingredients. See our
                                <a
                                    href="/en/free-workout-plan/home"
                                    class="text-primary-400 hover:underline"
                                    >home workout plan</a
                                >.
                            </p>
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
                        Ready to Start?
                    </h2>
                    <p class="mt-4 text-lg text-gray-300">
                        Create your free workout and meal plan now. 60
                        seconds, no signup, instant PDF.
                    </p>
                    <GenerateFitnessPlanModal
                        utm-content="landing_workout_meal_plan_cta"
                        utm-campaign="landing_pages"
                        #default="{ open }"
                    >
                        <Button
                            @click="open"
                            class="mt-8 rounded-xl bg-primary-500 px-8 py-4 text-lg font-semibold text-dark-surfaces-900 hover:bg-primary-400"
                        >
                            Create Your Free Plan
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
                                href="/en/blog/how-to-create-a-meal-plan"
                                class="text-primary-400 hover:underline"
                                >How to create a meal plan</a
                            >
                            -- Step-by-step guide
                        </li>
                        <li>
                            <a
                                href="/en/ai-workout-plan-generator"
                                class="text-primary-400 hover:underline"
                                >AI workout plan generator</a
                            >
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>
