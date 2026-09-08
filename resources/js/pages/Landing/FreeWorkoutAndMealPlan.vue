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
const canonical = `${baseUrl}/en/free-workout-and-meal-plan`;

const heroBenefits = [
    'Free and no sign-up',
    'Workout and meal plan together as one PDF',
    'Personalized by AI to your goal',
];

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
    <Head title="Free Workout and Meal Plan, Personalized in 60 Seconds">
        <meta
            name="description"
            content="Get your free workout and meal plan together. AI-powered, personalized for your goals. Covers training, diet, and shopping list. PDF download, no signup."
        />
        <link rel="canonical" :href="canonical" />
        <meta
            property="og:title"
            content="Free Workout and Meal Plan, Personalized in 60 Seconds"
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
                            Free · No sign-up
                        </p>
                        <h1
                            class="mt-4 text-4xl font-extrabold tracking-tight text-balance text-ink sm:text-5xl lg:leading-[1.05]"
                        >
                            Free workout and meal plan
                            <span class="text-brand">built for your goals</span>
                        </h1>
                        <p
                            class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-ink-muted lg:mx-0"
                        >
                            Tell us your goal, fitness level, and preferences.
                            Our AI creates a personalized workout and diet plan
                            you can download as PDF. No signup, no cost.
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
                                utm-content="landing_workout_meal_plan"
                                utm-campaign="landing_pages"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Why Combined -->
            <section
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        Why You Need a Combined Workout and Diet Plan
                    </h2>
                    <p class="mt-4 leading-relaxed text-ink-muted">
                        Training without nutrition is half the equation. You can
                        work out five days a week and still see zero results if
                        your diet is working against you. A calorie-deficit diet
                        makes fat-burning workouts effective. A protein-surplus
                        meal plan makes strength training productive.
                    </p>
                    <p class="mt-4 leading-relaxed text-ink-muted">
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
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        What You Get in Your Free Plan
                    </h2>

                    <div class="mt-8 space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-brand">
                                Your Workout Plan
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-ink-muted"
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
                                        class="text-brand hover:underline"
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
                            <h3 class="text-lg font-semibold text-brand">
                                Your Meal Plan
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-ink-muted"
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
                            <h3 class="text-lg font-semibold text-brand">
                                Your PDF Download
                            </h3>
                            <ul
                                class="mt-3 list-inside list-disc space-y-2 text-ink-muted"
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
            <section
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        How It Works
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
                                    Tell us your goal
                                </h3>
                                <p class="mt-1 text-ink-muted">
                                    Weight loss, muscle gain, general fitness,
                                    or endurance. Pick what matters to you.
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
                                    Set your preferences
                                </h3>
                                <p class="mt-1 text-ink-muted">
                                    Diet type, available equipment, how many
                                    days you want to train. The AI adapts to
                                    you.
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
                                    Get your combined plan as PDF
                                </h3>
                                <p class="mt-1 text-ink-muted">
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
                class="border-t border-stroke px-4 py-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        Who Is This For?
                    </h2>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2">
                        <div
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-brand">
                                Beginners
                            </h3>
                            <p class="mt-2 text-ink-muted">
                                A safe starting point with both training and
                                nutrition guidance. No experience needed. Check
                                out our
                                <a
                                    href="/en/free-workout-plan/beginner"
                                    class="text-brand hover:underline"
                                    >beginner workout plan</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-brand">
                                Weight Loss
                            </h3>
                            <p class="mt-2 text-ink-muted">
                                A calorie-deficit diet paired with fat-burning
                                workouts. See our dedicated
                                <a
                                    href="/en/free-workout-plan/weight-loss"
                                    class="text-brand hover:underline"
                                    >workout plans for weight loss</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-brand">
                                Muscle Gain
                            </h3>
                            <p class="mt-2 text-ink-muted">
                                A calorie-surplus meal plan paired with
                                progressive strength training. See our
                                <a
                                    href="/en/free-workout-plan/muscle-gain"
                                    class="text-brand hover:underline"
                                    >muscle gain workout plan</a
                                >.
                            </p>
                        </div>
                        <div
                            class="rounded-[20px] border border-stroke bg-surface p-6"
                        >
                            <h3 class="text-lg font-semibold text-brand">
                                Home Training
                            </h3>
                            <p class="mt-2 text-ink-muted">
                                No-equipment workouts with meal plans that do
                                not require special ingredients. See our
                                <a
                                    href="/en/free-workout-plan/home"
                                    class="text-brand hover:underline"
                                    >home workout plan</a
                                >.
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
                heading="Frequently Asked Questions"
                class="border-t border-stroke"
            />

            <!-- Final CTA -->
            <section
                class="border-t border-stroke px-4 py-20 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl text-center">
                    <h2 class="text-3xl font-bold text-balance text-ink sm:text-4xl">
                        Ready to Start?
                    </h2>
                    <p class="mt-4 text-lg text-ink-muted">
                        Create your free workout and meal plan now. 60 seconds,
                        no signup, instant PDF.
                    </p>
                    <GenerateFitnessPlanModal
                        utm-content="landing_workout_meal_plan_cta"
                        utm-campaign="landing_pages"
                        #default="{ open }"
                    >
                        <Button
                            @click="open"
                            class="mt-8 rounded-xl bg-brand px-8 py-4 text-lg font-semibold text-on-brand hover:bg-brand/90"
                        >
                            Create Your Free Plan
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
                        More Free Fitness Tools
                    </h2>
                    <ul class="mt-4 space-y-2 text-ink-muted">
                        <li>
                            <a
                                href="/en/free-tools/calorie-calculator"
                                class="text-brand hover:underline"
                                >Calorie calculator</a
                            >
                            · Find out how many calories you need
                        </li>
                        <li>
                            <a
                                href="/en/free-workout-plan"
                                class="text-brand hover:underline"
                                >Browse all free workout plans</a
                            >
                        </li>
                        <li>
                            <a
                                href="/en/blog/how-to-create-a-meal-plan"
                                class="text-brand hover:underline"
                                >How to create a meal plan</a
                            >
                            · Step-by-step guide
                        </li>
                        <li>
                            <a
                                href="/en/ai-workout-plan-generator"
                                class="text-brand hover:underline"
                                >AI workout plan generator</a
                            >
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>
