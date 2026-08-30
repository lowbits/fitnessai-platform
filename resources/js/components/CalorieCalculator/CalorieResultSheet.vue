<script setup lang="ts">
import BaseCard from '@/components/Base/BaseCard.vue';
import MacroRow from '@/components/MacroCalculator/MacroRow.vue';
import MealSplitStrip from '@/components/MacroCalculator/MealSplitStrip.vue';
import type { CalorieResult } from '@/composables/useCalorieCalculator';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    result: CalorieResult | null;
}

const props = defineProps<Props>();
const { t, locale } = useI18n();

const ZERO: CalorieResult = {
    bmr: 0,
    tdee: 0,
    calories: 0,
    goalDelta: 0,
    protein: { grams: 0, kcal: 0, share: 0 },
    carbs: { grams: 0, kcal: 0, share: 0 },
    fat: { grams: 0, kcal: 0, share: 0 },
};

const view = computed(() => props.result ?? ZERO);

const numberFormat = computed(() => new Intl.NumberFormat(locale.value));
const fmt = (value: number) => numberFormat.value.format(value);

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const displayedCalories = ref(view.value.calories);

watch(
    () => view.value.calories,
    (target, previous) => {
        if (prefersReducedMotion || typeof window === 'undefined') {
            displayedCalories.value = target;
            return;
        }

        const from = previous ?? displayedCalories.value;
        const duration = 400;
        const start = performance.now();

        const step = (now: number) => {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - (1 - progress) ** 3;
            displayedCalories.value = Math.round(
                from + (target - from) * eased,
            );
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    },
);

const goalLabel = computed(() => {
    if (view.value.goalDelta < 0) return t('calorieCalculator.result.deficit');
    if (view.value.goalDelta > 0) return t('calorieCalculator.result.surplus');
    return t('calorieCalculator.result.maintenance');
});

const meals = computed(() => [
    {
        label: t('calorieCalculator.result.breakfast'),
        kcal: Math.round(view.value.calories * 0.3),
    },
    {
        label: t('calorieCalculator.result.lunch'),
        kcal: Math.round(view.value.calories * 0.4),
    },
    {
        label: t('calorieCalculator.result.dinner'),
        kcal: Math.round(view.value.calories * 0.3),
    },
]);
</script>

<template>
    <BaseCard
        tone="paper"
        class="flex min-h-[520px] flex-col gap-6"
        aria-live="polite"
    >
        <!-- Headline calories -->
        <div>
            <p class="text-sm font-semibold text-paper-soft">
                {{ t('calorieCalculator.result.dailyNeeds') }}
            </p>
            <p class="mt-1 flex items-baseline gap-2">
                <span class="text-6xl font-bold text-paper-ink tabular-nums">{{
                    fmt(displayedCalories)
                }}</span>
                <span class="text-lg font-medium text-paper-soft">{{
                    t('calorieCalculator.result.kcalPerDay')
                }}</span>
            </p>
            <p
                v-if="view.goalDelta !== 0"
                class="mt-1 text-sm font-medium text-paper-soft tabular-nums"
            >
                {{ goalLabel }}: {{ view.goalDelta > 0 ? '+' : '−'
                }}{{ fmt(Math.abs(view.goalDelta)) }} kcal
                {{ t('calorieCalculator.result.vsTdee') }}
            </p>
        </div>

        <!-- BMR / TDEE -->
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-[8px] border border-paper-line px-4 py-3">
                <p class="text-xs text-paper-soft">
                    {{ t('calorieCalculator.result.bmr') }}
                </p>
                <p class="mt-0.5 text-paper-ink tabular-nums">
                    <span class="font-bold">{{ fmt(view.bmr) }}</span> kcal
                </p>
            </div>
            <div class="rounded-[8px] border border-paper-line px-4 py-3">
                <p class="text-xs text-paper-soft">
                    {{ t('calorieCalculator.result.tdee') }}
                </p>
                <p class="mt-0.5 text-paper-ink tabular-nums">
                    <span class="font-bold">{{ fmt(view.tdee) }}</span> kcal
                </p>
            </div>
        </div>

        <!-- Macro rows -->
        <div class="flex flex-col gap-5">
            <MacroRow
                :label="t('calorieCalculator.result.protein')"
                :portion="view.protein"
                color="var(--v2-macro-protein)"
            />
            <MacroRow
                :label="t('calorieCalculator.result.carbs')"
                :portion="view.carbs"
                color="var(--v2-macro-carbs)"
            />
            <MacroRow
                :label="t('calorieCalculator.result.fat')"
                :portion="view.fat"
                color="var(--v2-macro-fat)"
            />
        </div>

        <MealSplitStrip
            :heading="t('calorieCalculator.result.mealSplit')"
            :meals="meals"
        />
    </BaseCard>
</template>
