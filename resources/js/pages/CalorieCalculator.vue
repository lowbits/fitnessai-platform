<script setup lang="ts">
import LabeledInput from '@/components/form/LabeledInput.vue';
import NumberInput from '@/components/form/NumberInput.vue';
import SelectInput from '@/components/form/SelectInput.vue';
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import FAQSection from '@/components/workoutPlan/FAQSection.vue';
import { Button } from '@/components/ui/button';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';

type PageProps = {
    footerLinks: { appStoreUrl: string };
    currentLocale: string;
};

interface Props {
    meta: {
        title: string;
        description: string;
        canonical: string;
    };
    alternateUrls: Record<string, string>;
    schema: object[];
    relatedArticles: { url: string; title: string; description: string }[];
}

const props = defineProps<Props>();

const { t } = useI18n();
const page = usePage<PageProps>();
const locale = computed(() => page.props.currentLocale);

const schemaJson = computed(() =>
    props.schema.map((s) => JSON.stringify(s)),
);

// Calculator state
const form = reactive({
    gender: 'male' as 'male' | 'female',
    age: '' as string,
    weight: '' as string,
    height: '' as string,
    activity: '1.55' as string,
    goal: 'maintain' as 'lose' | 'maintain' | 'gain',
});

const hasCalculated = ref(false);

// Parse form values
const age = computed(() => parseFloat(form.age) || 0);
const weight = computed(() => parseFloat(form.weight) || 0);
const height = computed(() => parseFloat(form.height) || 0);

// Mifflin-St Jeor formula
const bmr = computed(() => {
    if (!age.value || !weight.value || !height.value) return 0;
    if (form.gender === 'male') {
        return 10 * weight.value + 6.25 * height.value - 5 * age.value + 5;
    }
    return 10 * weight.value + 6.25 * height.value - 5 * age.value - 161;
});

const tdee = computed(() => {
    return Math.round(bmr.value * parseFloat(form.activity));
});

const goalCalories = computed(() => {
    if (form.goal === 'lose') return Math.round(tdee.value - 400);
    if (form.goal === 'gain') return Math.round(tdee.value + 300);
    return tdee.value;
});

// Macros (balanced split)
const macros = computed(() => {
    const cal = goalCalories.value;
    if (form.goal === 'lose') {
        const protein = Math.round((cal * 0.35) / 4);
        const fat = Math.round((cal * 0.3) / 9);
        const carbs = Math.round((cal * 0.35) / 4);
        return { protein, fat, carbs };
    }
    if (form.goal === 'gain') {
        const protein = Math.round((cal * 0.3) / 4);
        const fat = Math.round((cal * 0.25) / 9);
        const carbs = Math.round((cal * 0.45) / 4);
        return { protein, fat, carbs };
    }
    const protein = Math.round((cal * 0.3) / 4);
    const fat = Math.round((cal * 0.3) / 9);
    const carbs = Math.round((cal * 0.4) / 4);
    return { protein, fat, carbs };
});

const isValid = computed(
    () => age.value > 0 && weight.value > 0 && height.value > 0,
);

const calculate = () => {
    if (isValid.value) {
        hasCalculated.value = true;
    }
};

const faqs = computed(() => {
    return (
        props.schema[1] as {
            mainEntity: {
                name: string;
                acceptedAnswer: { text: string };
            }[];
        }
    ).mainEntity.map((q) => ({
        question: q.name,
        answer: q.acceptedAnswer.text,
    }));
});

const comparisonFeatures = computed(() => [
    { key: 'dailyCalories', calculator: true, fytrr: true },
    { key: 'macroSplit', calculator: true, fytrr: true },
    { key: 'mealPlan', calculator: false, fytrr: true },
    { key: 'swapMeals', calculator: false, fytrr: true },
    { key: 'shoppingList', calculator: false, fytrr: true },
    { key: 'workoutPlan', calculator: false, fytrr: true },
    { key: 'progressAdapt', calculator: false, fytrr: true },
]);
</script>

<template>
    <Head :title="meta.title">
        <meta name="description" :content="meta.description" />
        <link rel="canonical" :href="meta.canonical" />
        <meta property="og:title" :content="meta.title" />
        <meta property="og:description" :content="meta.description" />
        <meta property="og:url" :content="meta.canonical" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="meta.title" />
        <meta name="twitter:description" :content="meta.description" />
        <link
            v-for="(url, loc) in alternateUrls"
            :key="loc"
            rel="alternate"
            :hreflang="loc"
            :href="url"
        />

        <component
            v-for="(schema, i) in schemaJson"
            :key="i"
            :is="'script'"
            type="application/ld+json"
        >
            {{ schema }}
        </component>
    </Head>

    <GuestLayout>
        <div class="bg-dark-surfaces-900">
            <!-- Hero -->
            <section class="px-4 pt-12 pb-8 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <h1
                        class="font-display text-3xl font-bold text-white sm:text-4xl lg:text-5xl"
                    >
                        {{ t('calorieCalculator.hero.h1') }}
                    </h1>
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-300">
                        {{ t('calorieCalculator.hero.subtitle') }}
                    </p>
                </div>
            </section>

            <!-- Calculator -->
            <section class="px-4 pb-16 sm:px-6 lg:px-8">
                <div
                    class="mx-auto max-w-2xl rounded-2xl border border-dark-surfaces-500 bg-dark-surfaces-800 p-6 sm:p-8"
                >
                    <!-- Gender -->
                    <div class="mb-6">
                        <label class="mb-2 block text-primary-25">
                            {{ t('calorieCalculator.form.gender') }}
                        </label>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-xl border p-2 text-sm font-medium transition md:p-4"
                                :class="
                                    form.gender === 'male'
                                        ? 'border-primary-200 bg-linear-to-tr from-transparent to-primary-300/5 text-white'
                                        : 'border-dark-surfaces-25 text-secondary-200'
                                "
                                @click="form.gender = 'male'"
                            >
                                {{ t('calorieCalculator.form.male') }}
                            </button>
                            <button
                                type="button"
                                class="flex-1 rounded-xl border p-2 text-sm font-medium transition md:p-4"
                                :class="
                                    form.gender === 'female'
                                        ? 'border-primary-200 bg-linear-to-tr from-transparent to-primary-300/5 text-white'
                                        : 'border-dark-surfaces-25 text-secondary-200'
                                "
                                @click="form.gender = 'female'"
                            >
                                {{ t('calorieCalculator.form.female') }}
                            </button>
                        </div>
                    </div>

                    <!-- Age, Weight, Height -->
                    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <LabeledInput
                            :label="t('calorieCalculator.form.age')"
                            name="age"
                        >
                            <NumberInput
                                id="age"
                                name="age"
                                :placeholder="t('calorieCalculator.form.agePlaceholder')"
                                v-model="form.age"
                                type="number"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                min="14"
                                max="100"
                            />
                        </LabeledInput>
                        <LabeledInput
                            :label="t('calorieCalculator.form.weight')"
                            name="weight"
                        >
                            <NumberInput
                                id="weight"
                                name="weight"
                                :placeholder="t('calorieCalculator.form.weightPlaceholder')"
                                v-model="form.weight"
                                suffix="kg"
                                type="number"
                                inputmode="decimal"
                                step="0.5"
                                min="30"
                                max="300"
                            />
                        </LabeledInput>
                        <LabeledInput
                            :label="t('calorieCalculator.form.height')"
                            name="height"
                        >
                            <NumberInput
                                id="height"
                                name="height"
                                :placeholder="t('calorieCalculator.form.heightPlaceholder')"
                                v-model="form.height"
                                suffix="cm"
                                type="number"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                min="100"
                                max="250"
                            />
                        </LabeledInput>
                    </div>

                    <!-- Activity Level -->
                    <LabeledInput
                        :label="t('calorieCalculator.form.activity')"
                        name="activity"
                        class="mb-6"
                    >
                        <SelectInput
                            id="activity"
                            name="activity"
                            v-model="form.activity"
                        >
                            <option value="1.2">{{ t('calorieCalculator.form.sedentary') }}</option>
                            <option value="1.375">{{ t('calorieCalculator.form.light') }}</option>
                            <option value="1.55">{{ t('calorieCalculator.form.moderate') }}</option>
                            <option value="1.725">{{ t('calorieCalculator.form.active') }}</option>
                            <option value="1.9">{{ t('calorieCalculator.form.veryActive') }}</option>
                        </SelectInput>
                    </LabeledInput>

                    <!-- Goal -->
                    <div class="mb-8">
                        <label class="mb-2 block text-primary-25">
                            {{ t('calorieCalculator.form.goal') }}
                        </label>
                        <div class="flex gap-3">
                            <button
                                v-for="g in (['lose', 'maintain', 'gain'] as const)"
                                :key="g"
                                type="button"
                                class="flex-1 rounded-xl border p-2 text-sm font-medium transition md:p-4"
                                :class="
                                    form.goal === g
                                        ? 'border-primary-200 bg-linear-to-tr from-transparent to-primary-300/5 text-white'
                                        : 'border-dark-surfaces-25 text-secondary-200'
                                "
                                @click="form.goal = g"
                            >
                                {{ t(`calorieCalculator.form.${g}`) }}
                            </button>
                        </div>
                    </div>

                    <!-- Calculate Button -->
                    <Button
                        size="lg"
                        class="w-full"
                        :disabled="!isValid"
                        @click="calculate"
                    >
                        {{ t('calorieCalculator.form.calculate') }}
                    </Button>

                    <!-- Trust element -->
                    <p class="mt-3 text-center text-xs text-gray-400">
                        {{ t('calorieCalculator.form.trust') }}
                    </p>

                    <!-- Results (pre-reserved space to avoid CLS) -->
                    <div
                        class="mt-8 min-h-[280px]"
                        :class="{ 'opacity-0': !hasCalculated }"
                    >
                        <div v-if="hasCalculated" class="space-y-6">
                            <!-- Main Result -->
                            <div
                                class="rounded-xl border border-primary-500/30 bg-primary-500/5 p-6 text-center"
                            >
                                <p
                                    class="text-sm font-medium text-gray-400"
                                >
                                    {{
                                        t(
                                            'calorieCalculator.result.dailyNeeds',
                                        )
                                    }}
                                </p>
                                <p
                                    class="mt-1 text-5xl font-bold text-primary-400"
                                >
                                    {{ goalCalories.toLocaleString() }}
                                </p>
                                <p
                                    class="mt-1 text-sm text-gray-400"
                                >
                                    {{
                                        t(
                                            'calorieCalculator.result.kcalPerDay',
                                        )
                                    }}
                                </p>
                            </div>

                            <!-- Breakdown -->
                            <div class="grid grid-cols-3 gap-4">
                                <div
                                    class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                >
                                    <p class="text-xs text-gray-400">
                                        {{
                                            t(
                                                'calorieCalculator.result.bmr',
                                            )
                                        }}
                                    </p>
                                    <p
                                        class="mt-1 text-lg font-semibold text-white"
                                    >
                                        {{
                                            Math.round(
                                                bmr,
                                            ).toLocaleString()
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        kcal
                                    </p>
                                </div>
                                <div
                                    class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                >
                                    <p class="text-xs text-gray-400">
                                        {{
                                            t(
                                                'calorieCalculator.result.tdee',
                                            )
                                        }}
                                    </p>
                                    <p
                                        class="mt-1 text-lg font-semibold text-white"
                                    >
                                        {{ tdee.toLocaleString() }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        kcal
                                    </p>
                                </div>
                                <div
                                    class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                >
                                    <p class="text-xs text-gray-400">
                                        {{
                                            t(
                                                'calorieCalculator.result.goalLabel',
                                            )
                                        }}
                                    </p>
                                    <p
                                        class="mt-1 text-lg font-semibold text-primary-400"
                                    >
                                        {{
                                            goalCalories.toLocaleString()
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        kcal
                                    </p>
                                </div>
                            </div>

                            <!-- Macros -->
                            <div>
                                <h3
                                    class="mb-3 text-sm font-medium text-gray-300"
                                >
                                    {{
                                        t(
                                            'calorieCalculator.result.macros',
                                        )
                                    }}
                                </h3>
                                <div class="grid grid-cols-3 gap-4">
                                    <div
                                        class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                    >
                                        <p class="text-xs text-gray-400">
                                            {{
                                                t(
                                                    'calorieCalculator.result.protein',
                                                )
                                            }}
                                        </p>
                                        <p
                                            class="mt-1 text-lg font-semibold text-white"
                                        >
                                            {{ macros.protein }}g
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                    >
                                        <p class="text-xs text-gray-400">
                                            {{
                                                t(
                                                    'calorieCalculator.result.carbs',
                                                )
                                            }}
                                        </p>
                                        <p
                                            class="mt-1 text-lg font-semibold text-white"
                                        >
                                            {{ macros.carbs }}g
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                    >
                                        <p class="text-xs text-gray-400">
                                            {{
                                                t(
                                                    'calorieCalculator.result.fat',
                                                )
                                            }}
                                        </p>
                                        <p
                                            class="mt-1 text-lg font-semibold text-white"
                                        >
                                            {{ macros.fat }}g
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA with dynamic calories -->
                            <div
                                class="rounded-xl border border-primary-500/20 bg-primary-500/5 p-6 text-center"
                            >
                                <p
                                    class="text-base font-medium text-white"
                                >
                                    {{
                                        t(
                                            'calorieCalculator.result.ctaHeadline',
                                        )
                                    }}
                                </p>
                                <p class="mt-2 text-sm text-gray-300">
                                    {{
                                        t(
                                            'calorieCalculator.result.ctaText',
                                            {
                                                calories:
                                                    goalCalories.toLocaleString(),
                                            },
                                        )
                                    }}
                                </p>
                                <GenerateFitnessPlanModal
                                    utm-content="calorie_calculator_result"
                                    utm-campaign="calorie_calculator"
                                    #default="{ open }"
                                >
                                    <button
                                        @click="open"
                                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-base font-semibold text-dark-surfaces-900 transition hover:bg-primary-400"
                                    >
                                        {{
                                            t(
                                                'calorieCalculator.result.ctaButton',
                                            )
                                        }}
                                    </button>
                                </GenerateFitnessPlanModal>
                                <p class="mt-2 text-xs text-gray-400">
                                    {{
                                        t(
                                            'calorieCalculator.result.ctaSubline',
                                        )
                                    }}
                                </p>
                            </div>

                            <!-- Comparison Table -->
                            <div class="overflow-hidden rounded-xl border border-dark-surfaces-500">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-dark-surfaces-700">
                                            <th class="px-4 py-3 text-left font-medium text-gray-300">
                                                {{ t('calorieCalculator.comparison.feature') }}
                                            </th>
                                            <th class="px-4 py-3 text-center font-medium text-gray-300">
                                                {{ t('calorieCalculator.comparison.calculator') }}
                                            </th>
                                            <th class="px-4 py-3 text-center font-medium text-primary-400">
                                                Fytrr
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(feat, i) in comparisonFeatures"
                                            :key="feat.key"
                                            :class="i % 2 === 0 ? 'bg-dark-surfaces-800' : 'bg-dark-surfaces-800/50'"
                                        >
                                            <td class="px-4 py-3 text-gray-300">
                                                {{ t(`calorieCalculator.comparison.features.${feat.key}`) }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span v-if="feat.calculator" class="text-primary-400">&#10003;</span>
                                                <span v-else class="text-gray-500">&#10007;</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="text-primary-400">&#10003;</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Objection Handling -->
                            <div class="space-y-3">
                                <div
                                    v-for="objection in ['needApp', 'cost', 'cancel']"
                                    :key="objection"
                                    class="flex gap-3 text-sm"
                                >
                                    <span class="mt-0.5 shrink-0 text-primary-400">&#8594;</span>
                                    <p class="text-gray-400">
                                        <strong class="text-gray-300">{{ t(`calorieCalculator.objections.${objection}.q`) }}</strong>
                                        {{ t(`calorieCalculator.objections.${objection}.a`) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SEO Content -->
            <section class="px-4 pb-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <article class="prose prose-invert max-w-none">
                        <!-- H2: How the calculator works -->
                        <h2
                            class="font-display text-2xl font-bold text-white"
                        >
                            {{ t('calorieCalculator.content.h2_1') }}
                        </h2>
                        <p class="leading-relaxed text-gray-300">
                            {{ t('calorieCalculator.content.p1') }}
                        </p>
                        <ul class="space-y-2 text-gray-300">
                            <li>
                                {{
                                    t(
                                        'calorieCalculator.content.formula_male',
                                    )
                                }}
                            </li>
                            <li>
                                {{
                                    t(
                                        'calorieCalculator.content.formula_female',
                                    )
                                }}
                            </li>
                        </ul>
                        <p class="leading-relaxed text-gray-300">
                            {{ t('calorieCalculator.content.p1b') }}
                        </p>

                        <!-- H2: What is BMR -->
                        <h2
                            class="mt-10 font-display text-2xl font-bold text-white"
                        >
                            {{ t('calorieCalculator.content.h2_2') }}
                        </h2>
                        <p class="leading-relaxed text-gray-300">
                            {{ t('calorieCalculator.content.p2') }}
                        </p>

                        <!-- H2: Calories for weight loss -->
                        <h2
                            class="mt-10 font-display text-2xl font-bold text-white"
                        >
                            {{ t('calorieCalculator.content.h2_3') }}
                        </h2>
                        <p class="leading-relaxed text-gray-300">
                            {{ t('calorieCalculator.content.p3') }}
                        </p>

                        <!-- H2: Calories for muscle gain -->
                        <h2
                            class="mt-10 font-display text-2xl font-bold text-white"
                        >
                            {{ t('calorieCalculator.content.h2_4') }}
                        </h2>
                        <p class="leading-relaxed text-gray-300">
                            {{ t('calorieCalculator.content.p4') }}
                        </p>

                        <!-- H2: Women vs Men -->
                        <h2
                            class="mt-10 font-display text-2xl font-bold text-white"
                        >
                            {{ t('calorieCalculator.content.h2_5') }}
                        </h2>
                        <p class="leading-relaxed text-gray-300">
                            {{ t('calorieCalculator.content.p5') }}
                        </p>

                        <!-- H2: Activity levels -->
                        <h2
                            class="mt-10 font-display text-2xl font-bold text-white"
                        >
                            {{ t('calorieCalculator.content.h2_6') }}
                        </h2>
                        <p class="leading-relaxed text-gray-300">
                            {{ t('calorieCalculator.content.p6') }}
                        </p>
                        <ul class="space-y-2 text-gray-300">
                            <li
                                v-for="level in [
                                    'sedentary',
                                    'light',
                                    'moderate',
                                    'active',
                                    'veryActive',
                                ]"
                                :key="level"
                            >
                                <strong class="text-white">
                                    {{
                                        t(
                                            `calorieCalculator.content.levels.${level}.label`,
                                        )
                                    }}:
                                </strong>
                                {{
                                    t(
                                        `calorieCalculator.content.levels.${level}.desc`,
                                    )
                                }}
                            </li>
                        </ul>

                        <!-- H2: Next step -->
                        <h2
                            class="mt-10 font-display text-2xl font-bold text-white"
                        >
                            {{ t('calorieCalculator.content.h2_7') }}
                        </h2>
                        <p class="leading-relaxed text-gray-300">
                            {{ t('calorieCalculator.content.p7') }}
                        </p>
                    </article>
                </div>
            </section>

            <!-- Further Reading -->
            <section
                v-if="relatedArticles.length"
                class="px-4 pb-16 sm:px-6 lg:px-8"
            >
                <div class="mx-auto max-w-3xl">
                    <h2
                        class="font-display text-2xl font-bold text-white"
                    >
                        {{ t('calorieCalculator.furtherReading.heading') }}
                    </h2>
                    <div class="mt-4 space-y-3">
                        <a
                            v-for="article in relatedArticles"
                            :key="article.url"
                            :href="article.url"
                            class="flex items-center justify-between rounded-xl border border-dark-surfaces-500 bg-dark-surfaces-800 p-5 transition hover:border-primary-500"
                        >
                            <div>
                                <p class="font-medium text-white">
                                    {{ article.title }}
                                </p>
                                <p
                                    class="mt-1 text-sm text-gray-400"
                                >
                                    {{ article.description }}
                                </p>
                            </div>
                            <span
                                class="ml-4 shrink-0 text-primary-400"
                                >&rarr;</span
                            >
                        </a>
                    </div>

                    <!-- App Store -->
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-400">
                            {{
                                t(
                                    'calorieCalculator.furtherReading.appStoreText',
                                )
                            }}
                        </p>
                        <a
                            :href="
                                page.props.footerLinks.appStoreUrl
                            "
                            target="_blank"
                            rel="noopener"
                            class="mt-3 inline-block"
                        >
                            <img
                                :src="`/assets/badges/App_Store_Badge_${locale.toUpperCase()}.svg`"
                                :alt="
                                    t(
                                        'calorieCalculator.furtherReading.appStoreBadge',
                                    )
                                "
                                class="h-10"
                                loading="lazy"
                            />
                        </a>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <FAQSection
                :faqs="faqs"
                :heading="t('calorieCalculator.faq.heading')"
                class="rounded-2xl"
            />
        </div>
    </GuestLayout>
</template>
