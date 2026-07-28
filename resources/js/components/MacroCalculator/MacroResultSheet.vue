<script setup lang="ts">
import BaseCard from '@/components/Base/BaseCard.vue';
import type { MacroResult } from '@/composables/useMacroCalculator';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import MacroRow from './MacroRow.vue';
import MealSplitStrip from './MealSplitStrip.vue';

interface Props {
    result: MacroResult | null;
}

const props = defineProps<Props>();
const { t, n } = useI18n();

const ZERO: MacroResult = {
    bmr: 0,
    tdee: 0,
    calories: 0,
    protein: { grams: 0, kcal: 0, share: 0 },
    carbs: { grams: 0, kcal: 0, share: 0 },
    fat: { grams: 0, kcal: 0, share: 0 },
};

// Before the form is complete we show the full card zeroed out, not a prompt.
const view = computed(() => props.result ?? ZERO);

const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Soft count-up of the headline calories on change.
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

const meals = computed(() => [
    {
        label: t('macroCalculator.result.breakfast'),
        kcal: Math.round(view.value.calories * 0.3),
    },
    {
        label: t('macroCalculator.result.lunch'),
        kcal: Math.round(view.value.calories * 0.4),
    },
    {
        label: t('macroCalculator.result.dinner'),
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
                {{ t('macroCalculator.result.dailyTarget') }}
            </p>
            <p class="mt-1 flex items-baseline gap-2">
                <span class="text-6xl font-bold text-paper-ink tabular-nums">{{
                    n(displayedCalories)
                }}</span>
                <span class="text-lg font-medium text-paper-soft">{{
                    t('macroCalculator.result.kcalPerDay')
                }}</span>
            </p>
        </div>

        <!-- BMR / TDEE -->
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-[8px] border border-paper-line px-4 py-3">
                <p class="text-xs text-paper-soft">
                    {{ t('macroCalculator.result.bmr') }}
                </p>
                <p class="mt-0.5 text-paper-ink tabular-nums">
                    <span class="font-bold">{{ n(view.bmr) }}</span> kcal
                </p>
            </div>
            <div class="rounded-[8px] border border-paper-line px-4 py-3">
                <p class="text-xs text-paper-soft">
                    {{ t('macroCalculator.result.tdee') }}
                </p>
                <p class="mt-0.5 text-paper-ink tabular-nums">
                    <span class="font-bold">{{ n(view.tdee) }}</span> kcal
                </p>
            </div>
        </div>

        <!-- Macro rows -->
        <div class="flex flex-col gap-5">
            <MacroRow
                :label="t('macroCalculator.result.protein')"
                :portion="view.protein"
                color="var(--v2-macro-protein)"
            />
            <MacroRow
                :label="t('macroCalculator.result.carbs')"
                :portion="view.carbs"
                color="var(--v2-macro-carbs)"
            />
            <MacroRow
                :label="t('macroCalculator.result.fat')"
                :portion="view.fat"
                color="var(--v2-macro-fat)"
            />
        </div>

        <MealSplitStrip
            :heading="t('macroCalculator.result.mealSplit')"
            :meals="meals"
        />
    </BaseCard>
</template>
