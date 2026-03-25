<script setup lang="ts">
import GenerateFitnessPlanModal from '@/components/modals/GenerateFitnessPlanModal.vue';
import FAQSection from '@/components/workoutPlan/FAQSection.vue';
import { Button } from '@/components/ui/button';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    meta: {
        title: string;
        description: string;
        canonical: string;
    };
    alternateUrls: Record<string, string>;
    schema: object[];
}

const props = defineProps<Props>();

const { t, locale } = useI18n();

const schemaJson = computed(() =>
    props.schema.map((s) => JSON.stringify(s)),
);

// Calculator state
const form = reactive({
    gender: 'male' as 'male' | 'female',
    age: null as number | null,
    weight: null as number | null,
    height: null as number | null,
    activity: '1.55' as string,
    goal: 'maintain' as 'lose' | 'maintain' | 'gain',
});

const hasCalculated = ref(false);

// Mifflin-St Jeor formula
const bmr = computed(() => {
    if (!form.age || !form.weight || !form.height) return 0;
    if (form.gender === 'male') {
        return 10 * form.weight + 6.25 * form.height - 5 * form.age + 5;
    }
    return 10 * form.weight + 6.25 * form.height - 5 * form.age - 161;
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
        // Higher protein for muscle preservation
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
    // Maintain
    const protein = Math.round((cal * 0.3) / 4);
    const fat = Math.round((cal * 0.3) / 9);
    const carbs = Math.round((cal * 0.4) / 4);
    return { protein, fat, carbs };
});

const isValid = computed(
    () =>
        form.age &&
        form.age > 0 &&
        form.weight &&
        form.weight > 0 &&
        form.height &&
        form.height > 0,
);

const calculate = () => {
    if (isValid.value) {
        hasCalculated.value = true;
    }
};

const faqs = computed(() => {
    return (
        props.schema[1] as { mainEntity: { name: string; acceptedAnswer: { text: string } }[] }
    ).mainEntity.map((q) => ({
        question: q.name,
        answer: q.acceptedAnswer.text,
    }));
});
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
                        <label class="mb-2 block text-sm font-medium text-gray-300">
                            {{ t('calorieCalculator.form.gender') }}
                        </label>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-lg border px-4 py-3 text-sm font-medium transition"
                                :class="
                                    form.gender === 'male'
                                        ? 'border-primary-500 bg-primary-500/10 text-primary-400'
                                        : 'border-dark-surfaces-500 text-gray-400 hover:border-gray-400'
                                "
                                @click="form.gender = 'male'"
                            >
                                {{ t('calorieCalculator.form.male') }}
                            </button>
                            <button
                                type="button"
                                class="flex-1 rounded-lg border px-4 py-3 text-sm font-medium transition"
                                :class="
                                    form.gender === 'female'
                                        ? 'border-primary-500 bg-primary-500/10 text-primary-400'
                                        : 'border-dark-surfaces-500 text-gray-400 hover:border-gray-400'
                                "
                                @click="form.gender = 'female'"
                            >
                                {{ t('calorieCalculator.form.female') }}
                            </button>
                        </div>
                    </div>

                    <!-- Age, Weight, Height -->
                    <div class="mb-6 grid grid-cols-3 gap-4">
                        <div>
                            <label
                                for="age"
                                class="mb-2 block text-sm font-medium text-gray-300"
                            >
                                {{ t('calorieCalculator.form.age') }}
                            </label>
                            <input
                                id="age"
                                v-model.number="form.age"
                                type="number"
                                min="14"
                                max="100"
                                :placeholder="t('calorieCalculator.form.agePlaceholder')"
                                class="w-full rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label
                                for="weight"
                                class="mb-2 block text-sm font-medium text-gray-300"
                            >
                                {{ t('calorieCalculator.form.weight') }}
                            </label>
                            <input
                                id="weight"
                                v-model.number="form.weight"
                                type="number"
                                min="30"
                                max="300"
                                :placeholder="t('calorieCalculator.form.weightPlaceholder')"
                                class="w-full rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label
                                for="height"
                                class="mb-2 block text-sm font-medium text-gray-300"
                            >
                                {{ t('calorieCalculator.form.height') }}
                            </label>
                            <input
                                id="height"
                                v-model.number="form.height"
                                type="number"
                                min="100"
                                max="250"
                                :placeholder="t('calorieCalculator.form.heightPlaceholder')"
                                class="w-full rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 px-4 py-3 text-white placeholder-gray-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Activity Level -->
                    <div class="mb-6">
                        <label
                            for="activity"
                            class="mb-2 block text-sm font-medium text-gray-300"
                        >
                            {{ t('calorieCalculator.form.activity') }}
                        </label>
                        <select
                            id="activity"
                            v-model="form.activity"
                            class="w-full rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 px-4 py-3 text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none"
                        >
                            <option value="1.2">
                                {{ t('calorieCalculator.form.sedentary') }}
                            </option>
                            <option value="1.375">
                                {{ t('calorieCalculator.form.light') }}
                            </option>
                            <option value="1.55">
                                {{ t('calorieCalculator.form.moderate') }}
                            </option>
                            <option value="1.725">
                                {{ t('calorieCalculator.form.active') }}
                            </option>
                            <option value="1.9">
                                {{ t('calorieCalculator.form.veryActive') }}
                            </option>
                        </select>
                    </div>

                    <!-- Goal -->
                    <div class="mb-8">
                        <label class="mb-2 block text-sm font-medium text-gray-300">
                            {{ t('calorieCalculator.form.goal') }}
                        </label>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-lg border px-4 py-3 text-sm font-medium transition"
                                :class="
                                    form.goal === 'lose'
                                        ? 'border-primary-500 bg-primary-500/10 text-primary-400'
                                        : 'border-dark-surfaces-500 text-gray-400 hover:border-gray-400'
                                "
                                @click="form.goal = 'lose'"
                            >
                                {{ t('calorieCalculator.form.lose') }}
                            </button>
                            <button
                                type="button"
                                class="flex-1 rounded-lg border px-4 py-3 text-sm font-medium transition"
                                :class="
                                    form.goal === 'maintain'
                                        ? 'border-primary-500 bg-primary-500/10 text-primary-400'
                                        : 'border-dark-surfaces-500 text-gray-400 hover:border-gray-400'
                                "
                                @click="form.goal = 'maintain'"
                            >
                                {{ t('calorieCalculator.form.maintain') }}
                            </button>
                            <button
                                type="button"
                                class="flex-1 rounded-lg border px-4 py-3 text-sm font-medium transition"
                                :class="
                                    form.goal === 'gain'
                                        ? 'border-primary-500 bg-primary-500/10 text-primary-400'
                                        : 'border-dark-surfaces-500 text-gray-400 hover:border-gray-400'
                                "
                                @click="form.goal = 'gain'"
                            >
                                {{ t('calorieCalculator.form.gain') }}
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

                    <!-- Results (pre-reserved space to avoid CLS) -->
                    <div
                        class="mt-8 min-h-[280px]"
                        :class="{ 'opacity-0': !hasCalculated }"
                    >
                        <div
                            v-if="hasCalculated"
                            class="space-y-6"
                        >
                            <!-- Main Result -->
                            <div
                                class="rounded-xl border border-primary-500/30 bg-primary-500/5 p-6 text-center"
                            >
                                <p class="text-sm font-medium text-gray-400">
                                    {{ t('calorieCalculator.result.dailyNeeds') }}
                                </p>
                                <p
                                    class="mt-1 text-5xl font-bold text-primary-400"
                                >
                                    {{ goalCalories.toLocaleString() }}
                                </p>
                                <p class="mt-1 text-sm text-gray-400">
                                    {{ t('calorieCalculator.result.kcalPerDay') }}
                                </p>
                            </div>

                            <!-- Breakdown -->
                            <div class="grid grid-cols-3 gap-4">
                                <div
                                    class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                >
                                    <p class="text-xs text-gray-400">
                                        {{ t('calorieCalculator.result.bmr') }}
                                    </p>
                                    <p
                                        class="mt-1 text-lg font-semibold text-white"
                                    >
                                        {{ Math.round(bmr).toLocaleString() }}
                                    </p>
                                    <p class="text-xs text-gray-400">kcal</p>
                                </div>
                                <div
                                    class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                >
                                    <p class="text-xs text-gray-400">
                                        {{ t('calorieCalculator.result.tdee') }}
                                    </p>
                                    <p
                                        class="mt-1 text-lg font-semibold text-white"
                                    >
                                        {{ tdee.toLocaleString() }}
                                    </p>
                                    <p class="text-xs text-gray-400">kcal</p>
                                </div>
                                <div
                                    class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                >
                                    <p class="text-xs text-gray-400">
                                        {{ t('calorieCalculator.result.goalLabel') }}
                                    </p>
                                    <p
                                        class="mt-1 text-lg font-semibold text-primary-400"
                                    >
                                        {{ goalCalories.toLocaleString() }}
                                    </p>
                                    <p class="text-xs text-gray-400">kcal</p>
                                </div>
                            </div>

                            <!-- Macros -->
                            <div>
                                <h3
                                    class="mb-3 text-sm font-medium text-gray-300"
                                >
                                    {{ t('calorieCalculator.result.macros') }}
                                </h3>
                                <div class="grid grid-cols-3 gap-4">
                                    <div
                                        class="rounded-lg border border-dark-surfaces-500 bg-dark-surfaces-700 p-4 text-center"
                                    >
                                        <p class="text-xs text-gray-400">
                                            {{ t('calorieCalculator.result.protein') }}
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
                                            {{ t('calorieCalculator.result.carbs') }}
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
                                            {{ t('calorieCalculator.result.fat') }}
                                        </p>
                                        <p
                                            class="mt-1 text-lg font-semibold text-white"
                                        >
                                            {{ macros.fat }}g
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA -->
                            <div
                                class="rounded-xl border border-primary-500/20 bg-primary-500/5 p-6 text-center"
                            >
                                <p class="text-sm font-medium text-white">
                                    {{ t('calorieCalculator.result.ctaText') }}
                                </p>
                                <GenerateFitnessPlanModal
                                    utm-content="calorie_calculator_result"
                                    utm-campaign="calorie_calculator"
                                    #default="{ open }"
                                >
                                    <button
                                        @click="open"
                                        class="mt-3 inline-flex items-center gap-2 rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-dark-surfaces-900 transition hover:bg-primary-400"
                                    >
                                        {{ t('calorieCalculator.result.ctaButton') }}
                                    </button>
                                </GenerateFitnessPlanModal>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SEO Content -->
            <section class="px-4 pb-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl">
                    <article class="prose prose-invert max-w-none">
                        <h2 class="font-display text-2xl font-bold text-white">
                            {{ t('calorieCalculator.content.h2_1') }}
                        </h2>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('calorieCalculator.content.p1') }}
                        </p>

                        <h3 class="mt-8 font-display text-xl font-bold text-white">
                            {{ t('calorieCalculator.content.h3_1') }}
                        </h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('calorieCalculator.content.p2') }}
                        </p>
                        <ul class="space-y-2 text-gray-300">
                            <li>{{ t('calorieCalculator.content.formula_male') }}</li>
                            <li>{{ t('calorieCalculator.content.formula_female') }}</li>
                        </ul>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('calorieCalculator.content.p3') }}
                        </p>

                        <h3 class="mt-8 font-display text-xl font-bold text-white">
                            {{ t('calorieCalculator.content.h3_2') }}
                        </h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('calorieCalculator.content.p4') }}
                        </p>
                        <ul class="space-y-2 text-gray-300">
                            <li v-for="(level, i) in ['sedentary', 'light', 'moderate', 'active', 'veryActive']" :key="i">
                                <strong class="text-white">{{ t(`calorieCalculator.content.levels.${level}.label`) }}:</strong>
                                {{ t(`calorieCalculator.content.levels.${level}.desc`) }}
                            </li>
                        </ul>

                        <h3 class="mt-8 font-display text-xl font-bold text-white">
                            {{ t('calorieCalculator.content.h3_3') }}
                        </h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('calorieCalculator.content.p5') }}
                        </p>

                        <h3 class="mt-8 font-display text-xl font-bold text-white">
                            {{ t('calorieCalculator.content.h3_4') }}
                        </h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ t('calorieCalculator.content.p6') }}
                        </p>
                    </article>
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
