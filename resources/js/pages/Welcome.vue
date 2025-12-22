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

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Welcome">
        <title>AI-Generated Workout and Diet Plans | Get Fit</title>
        <meta
            name="description"
            content="Get in shape with our AI-generated workout and nutrition plans. Our unique algorithm creates personalized plans to help you reach your fitness goals."
        />
        <meta
            name="keywords"
            content="AI, workout, nutrition, fitness, personalization, health, plans"
        />
        <meta
            property="og:title"
            content="AI-Generated Workout and Nutrition Plans | Get Fit with our AI"
        />
        <meta
            property="og:description"
            content="Get in shape with our AI-generated workout and nutrition plans. Our unique algorithm creates personalized plans to help you reach your fitness goals."
        />
        <!-- <meta property="og:image" content="https://example.com/images/fitness-plan.jpg">
         <meta property="og:url" content="https://example.com">-->
        <meta property="og:site_name" content="Fitness with AI" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:site" content="@fitnessai.me" />
        <meta
            name="twitter:title"
            content="AI-Generated Workout and Nutrition Plans | Get Fit with our AI"
        />
        <meta
            name="twitter:description"
            content="Get in shape with our AI-generated workout and nutrition plans. Our unique algorithm creates personalized plans to help you reach your fitness goals."
        />
        <!-- <meta name="twitter:image" content="https://example.com/images/fitness-plan.jpg"> -->
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
                            Get Your personalized
                            <span
                                class="bg-gradient-to-r from-primary-400 via-primary-500 to-white bg-clip-text font-display font-bold tracking-tight text-primary-400 text-transparent"
                                >Workout- & Diet</span
                            >
                            Plan in Seconds
                        </h1>

                        <p class="mt-5 text-lg text-white">
                            Embrace AI technology for a customized workout &
                            nutrition plan, tailored to your goals and needs.
                        </p>
                    </article>
                </section>

                <FormCard class="flex flex-1 justify-end">
                    <div v-if="false" class="min-h-[400px] text-white">
                        <h3 class="text-2xl">🥳 Your Order is placed!</h3>
                        <p class="mt-4">
                            Thank you for your order. We are pleased to inform
                            you that it has been processed and is currently
                            being prepared for shipment. We will send you an
                            email once your order is ready. It can take up to
                            <strong>15 minutes</strong>.
                            <br />
                            <br />
                            <span class="mt-3 font-semibold"
                                >Please also check your spam directory.</span
                            >
                            <br />
                            <br />
                            If you have any further questions or concerns,
                            please do not hesitate to contact us.
                        </p>

                        <p class="mt-3">
                            <a
                                class="font-bold text-green-500"
                                href="mailto:hello@fitnessai.me"
                                >hello@fitnessai.me</a
                            >.
                        </p>

                        <p class="mt-3">
                            Thank you for choosing our service and we look
                            forward to serving you again in the future.
                        </p>

                        <p class="mt-5">
                            Best regards,<br />
                            Tobi<br />
                        </p>
                    </div>

                    <GenerateFitnessPlanForm v-else />
                </FormCard>
            </div>

            <div class="container mx-auto mt-20">
                <div class="flex flex-col justify-between gap-4 md:flex-row">
                    <ConvinceCard
                        headline="Rely on latest research"
                        body="Frequent updates based on science ensure access to the latest dietary recommendations."
                    >
                        <template #icon>
                            <AtomIcon />
                        </template>
                    </ConvinceCard>
                    <ConvinceCard
                        headline="Get plans lightning fast"
                        body="AI analyzes data fast, offering you personalized plans, often more efficient than human dietitians."
                    >
                        <template #icon>
                            <TableIcon />
                        </template>
                    </ConvinceCard>
                    <ConvinceCard
                        headline="Rearch you goals"
                        body="Achieve goals faster with AI-generated workout & nutrition plans, always tailored to your current status."
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

                <div class="max-w-xl mx-auto">
                    <h2
                        class="text-center text-5xl font-bold tracking-tight text-white"
                    >
                        Start your AI-Powered Workout Today!
                    </h2>

                    <p
                        class="mt-4 text-center text-base leading-6 font-medium text-white"
                    >
                        Boost your progress with a complimentary AI-crafted
                        workout & nutrition plan designed just for you.
                    </p>
                </div>
            </div>

            <section id="features" class="container mx-auto mt-32">
                <div class="mt-20 space-y-5 text-white">
                    <p class="text-sm text-primary-500">Features</p>
                    <h3 class="text-3xl font-bold tracking-tight text-white">
                        Transform Your Fitness with AI-Powered Solutions
                    </h3>

                    <p class="max-w-2xl text-lg text-secondary-300">
                        Explore the cutting-edge features of our AI-Fitness
                        Tool, designed to optimize your workouts, nutrition, and
                        overall well-being.
                    </p>
                </div>

                <div
                    class="mt-12 flex flex-col justify-between gap-4 md:flex-row"
                >
                    <FeatureCard
                        body="Design unique plans, tailored to your specific needs and goals."
                        headline="Create individual plans"
                        illustration-path="/assets/illustrations/plan-wireframe.svg"
                    >
                        <template #icon>
                            <FingerprintIcon />
                        </template>
                    </FeatureCard>

                    <FeatureCard
                        body="Generate shopping lists effortlessly from your personalized nutrition plan."
                        headline="Shop with ease"
                        illustration-path="/assets/illustrations/research-paper.svg"
                        is-preview
                    >
                        <template #icon>
                            <ShoppingBagIcon />
                        </template>
                    </FeatureCard>
                    <FeatureCard
                        body="Receive instant guidance from your AI Coach for on-the-spot support."
                        headline="Get instant advice"
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
                            class="text-white font-display text-3xl font-bold tracking-tight"
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
                                How accurate is an AI generated nutrition plan?
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                The accuracy of an AI generated nutrition plan
                                can vary depending on the quality of the data
                                and algorithm used to generate it. However, with
                                proper data and techniques, an AI generated
                                nutrition plan can be highly accurate and
                                provide personalized recommendations based on
                                your individual needs and goals.
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                Will an AI generated nutrition plan replace the
                                need for a dietitian?
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                No, an AI generated nutrition plan should not
                                replace the advice of a licensed dietitian. It
                                is always best to consult with a healthcare
                                professional before making drastic changes to
                                your diet. However, an AI generated nutrition
                                plan can provide additional support and
                                personalized recommendations.
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                How can I be sure that the plan is safe for me?
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                Before using an AI generated nutrition plan, it
                                is important to consult with a healthcare
                                professional to ensure that it is appropriate
                                for your individual needs and health conditions.
                            </p>
                        </div>
                        <div class="py-3">
                            <h3 class="text-xl font-medium text-primary-25">
                                Is an AI generated nutrition plan suitable for
                                special diets?
                            </h3>
                            <p
                                class="text-md mt-2 leading-5 text-secondary-100"
                            >
                                Yes, an AI generated nutrition plan can take
                                into account special dietary needs such as
                                allergies, food preferences and even religious
                                restrictions. However, it is important to
                                provide the correct information when creating
                                the plan and to consult with healthcare
                                professional to ensure that it is safe and
                                appropriate for you.
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
