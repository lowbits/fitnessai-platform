<script setup lang="ts">
import ConvinceCard from '@/components/ConvinceCard.vue';
import FeatureCard from '@/components/FeatureCard.vue';
import FormCard from '@/components/FormCard.vue';
import GenerateFitnessPlanForm from '@/components/GenerateFitnessPlanForm.vue';
import AtomIcon from '@/components/icons/AtomIcon.vue';
import FingerprintIcon from '@/components/icons/FingerprintIcon.vue';
import FlagIcon from '@/components/icons/FlagIcon.vue';
import GhostIcon from '@/components/icons/GhostIcon.vue';
import ShoppingBagIcon from '@/components/icons/ShoppingBagIcon.vue';
import TableIcon from '@/components/icons/TableIcon.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    durationDays: number;
}>()

const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'WebApplication',
    name: 'FitnessAI',
    description:
        'AI-generated personalized workout plans and nutrition plans for your fitness goals',
    url: 'https://fitnessai.me',
    applicationCategory: 'HealthApplication',
    operatingSystem: 'Web Browser',
    offers: {
        '@type': 'Offer',
        price: '3.99',
        priceCurrency: 'EUR',
    },
};
</script>

<template>
    <Head title="FitnessAI - Personalized AI Workout Plans in 60 Seconds">
        <component :is="'script'" type="application/ld+json">
            {{ JSON.stringify(structuredData) }}
        </component>
    </Head>

    <header class="w-full border-b border-dark-surfaces-25">
        <nav class="container mx-auto px-3 py-4">
            <Link href="/">
                <img
                    class="h-auto w-40"
                    src="/fitness-ai-me-logo.svg"
                    alt="fitnessAi.me logo"
                />
            </Link>
        </nav>
    </header>
    <main>
        <div class="h-full w-full px-6 pb-10 md:px-0">
            <div
                class="container mx-auto flex flex-col justify-between space-y-10 pt-14 md:flex-row md:space-y-0"
            >
                <section class="w-full md:w-1/2 md:pt-20">
                    <article class="max-w-2xl">
                        <h1 class="font-display text-6xl font-bold text-white">
                            Your Personalized
                            <span
                                class="bg-gradient-to-r from-primary-400 via-primary-500 to-white bg-clip-text font-display font-bold tracking-tight text-primary-400 text-transparent"
                                >Workout & Meal</span
                            >
                            Plans in 60 Seconds
                        </h1>

                        <p class="mt-5 text-lg text-white">
                            AI-generated fitness programs tailored to your goal,
                            level, and equipment. Start your transformation
                            today.
                        </p>
                    </article>
                </section>

                <FormCard class="flex flex-1 justify-end">
                    <GenerateFitnessPlanForm :total-days="durationDays" />
                </FormCard>
            </div>

            <div class="container mx-auto mt-20">
                <div class="flex flex-col justify-between gap-4 md:flex-row">
                    <ConvinceCard
                        headline="Based on Latest Research"
                        body="Frequent updates based on science ensure access to the latest training and nutrition recommendations."
                    >
                        <template #icon>
                            <AtomIcon />
                        </template>
                    </ConvinceCard>
                    <ConvinceCard
                        headline="Get Plans Lightning Fast"
                        body="AI analyzes your data instantly, delivering personalized plans faster and more accurately than traditional methods."
                    >
                        <template #icon>
                            <TableIcon />
                        </template>
                    </ConvinceCard>
                    <ConvinceCard
                        headline="Reach Your Goals Faster"
                        body="Achieve your fitness goals with AI-generated workout & nutrition plans that adapt to your progress."
                    >
                        <template #icon>
                            <FlagIcon />
                        </template>
                    </ConvinceCard>
                </div>
            </div>

            <div class="relative mx-auto mt-48">
                <!-- Background Gradient Image -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <img
                        src="/assets/gradient.png"
                        alt=""
                        class="h-auto w-full max-w-7xl object-contain opacity-50"
                    />
                </div>

                <div class="mx-auto max-w-xl">
                    <h2
                        class="text-center text-5xl font-bold tracking-tight text-white"
                    >
                        Start Your AI-Powered Workout Today!
                    </h2>

                    <p
                        class="mt-4 text-center text-base leading-6 font-medium text-white"
                    >
                        Get a complimentary AI-generated workout & nutrition
                        plan designed specifically for you.
                    </p>
                </div>
            </div>

            <section id="features" class="container mx-auto mt-32">
                <div class="mt-20 space-y-5 text-white">
                    <p class="text-sm text-primary-500">Features</p>
                    <h3 class="text-3xl font-bold tracking-tight text-white">
                        Transform Your Fitness with AI-Powered Plans
                    </h3>

                    <p class="max-w-2xl text-lg text-secondary-300">
                        Explore cutting-edge features designed to optimize your
                        workouts, nutrition, and overall progress.
                    </p>
                </div>

                <div
                    class="mt-12 flex flex-col justify-between gap-4 md:flex-row"
                >
                    <FeatureCard
                        headline="Create Individual Plans"
                        body="Design unique workout and nutrition plans tailored to your specific goals and needs."
                        illustration-path="/assets/illustrations/plan-wireframe.svg"
                    >
                        <template #icon>
                            <FingerprintIcon />
                        </template>
                    </FeatureCard>

                    <FeatureCard
                        headline="Shop with Ease"
                        body="Generate shopping lists automatically from your personalized nutrition plan."
                        illustration-path="/assets/illustrations/research-paper.svg"
                        is-preview
                    >
                        <template #icon>
                            <ShoppingBagIcon />
                        </template>
                    </FeatureCard>
                    <FeatureCard
                        headline="Get Instant Advice"
                        body="Receive real-time guidance from your AI Coach for on-the-spot support during workouts."
                        illustration-path="/assets/illustrations/food-intolerances.svg"
                        is-preview
                    >
                        <template #icon>
                            <GhostIcon />
                        </template>
                    </FeatureCard>
                </div>
            </section>

            <section id="faq" class="container mx-auto">
                <div class="mt-32 text-white">
                    <header class="text-center">
                        <h2
                            class="font-display text-3xl font-bold tracking-tight text-white"
                        >
                            Frequently asked questions
                        </h2>
                        <p>
                            If you have anything else you want to ask,
                            <a href="mailto:hello@fitnessai.me"
                                ><strong>reach out to us</strong></a
                            >.
                        </p>
                    </header>

                    <div
                        class="mx-auto mt-10 max-w-5xl divide-y divide-dark-surfaces-25"
                    >
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                How accurate is an AI-generated nutrition plan?
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                The accuracy of an AI-generated nutrition plan
                                depends on the quality of data and algorithms
                                used. With proper inputs, our AI provides highly
                                accurate, personalized recommendations based on
                                your individual needs, goals, and current
                                fitness level. Our system is continuously
                                updated with the latest research to ensure
                                optimal results.
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                Will an AI-generated plan replace the need for a
                                dietitian?
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                No, our AI-generated plans should complement,
                                not replace, professional advice from licensed
                                dietitians or healthcare providers. While our AI
                                provides personalized recommendations and daily
                                guidance, we always recommend consulting with
                                healthcare professionals before making
                                significant dietary or fitness changes,
                                especially if you have specific health
                                conditions.
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                How can I be sure the plan is safe for me?
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                Before starting any new fitness or nutrition
                                program, we recommend consulting with your
                                healthcare provider, especially if you have
                                existing health conditions, injuries, or dietary
                                restrictions. Our AI takes your input into
                                account, but professional medical advice is
                                always recommended for your safety.
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                Can the AI accommodate special diets or
                                restrictions?
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                Yes! Our AI can accommodate special dietary
                                needs including allergies, food preferences,
                                vegetarian/vegan diets, religious restrictions,
                                and more. Simply provide accurate information
                                during the setup process. However, for complex
                                medical conditions or severe allergies, please
                                consult with a healthcare professional to ensure
                                the plan is safe and appropriate for you.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <footer class="bg-dark-surfaces-800 p-8 text-primary-200">
        <nav class="container mx-auto flex justify-between">
            <Link href="/">fitnessai.me</Link>
            <Link href="/imprint">Imprint</Link>
        </nav>
    </footer>
</template>
