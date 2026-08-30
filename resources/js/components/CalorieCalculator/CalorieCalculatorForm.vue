<script setup lang="ts">
import BaseChoiceCard from '@/components/Base/BaseChoiceCard.vue';
import BaseNumberField from '@/components/Base/BaseNumberField.vue';
import BaseSegmentedControl from '@/components/Base/BaseSegmentedControl.vue';
import type {
    Activity,
    Gender,
    Goal,
    useCalorieCalculator,
} from '@/composables/useCalorieCalculator';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    calc: ReturnType<typeof useCalorieCalculator>;
}

const props = defineProps<Props>();

const { t } = useI18n();

const { input } = props.calc;

const genderOptions = computed(() =>
    (['male', 'female'] as Gender[]).map((value) => ({
        value,
        label: t(`calorieCalculator.form.gender_${value}`),
    })),
);

const goalOptions = computed(() =>
    (['lose', 'maintain', 'gain'] as Goal[]).map((value) => ({
        value,
        label: t(`calorieCalculator.form.goal_${value}`),
    })),
);

const activityValues: Activity[] = [
    'sedentary',
    'light',
    'moderate',
    'active',
    'veryActive',
];
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Goal -->
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-ink-muted">{{
                t('calorieCalculator.form.goal')
            }}</span>
            <BaseSegmentedControl
                v-model="input.goal"
                :options="goalOptions"
                :aria-label="t('calorieCalculator.form.goal')"
            />
        </div>

        <!-- Gender -->
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-ink-muted">{{
                t('calorieCalculator.form.gender')
            }}</span>
            <BaseSegmentedControl
                v-model="input.gender"
                :options="genderOptions"
                :aria-label="t('calorieCalculator.form.gender')"
            />
        </div>

        <!-- Body stats -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <BaseNumberField
                v-model="input.age"
                :label="t('calorieCalculator.form.age')"
                :min="14"
                :max="100"
            />
            <BaseNumberField
                v-model="input.weight"
                :label="t('calorieCalculator.form.weight')"
                suffix="kg"
                :min="30"
                :max="300"
                :step="0.5"
            />
            <BaseNumberField
                v-model="input.height"
                :label="t('calorieCalculator.form.height')"
                suffix="cm"
                :min="100"
                :max="250"
            />
        </div>

        <!-- Activity level -->
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-ink-muted">{{
                t('calorieCalculator.form.activity')
            }}</span>
            <div
                role="radiogroup"
                :aria-label="t('calorieCalculator.form.activity')"
                class="flex flex-col gap-2"
            >
                <BaseChoiceCard
                    v-for="value in activityValues"
                    :key="value"
                    v-model="input.activity"
                    :value="value"
                    :title="
                        t(
                            `calorieCalculator.form.activityOptions.${value}.title`,
                        )
                    "
                    :description="
                        t(
                            `calorieCalculator.form.activityOptions.${value}.hint`,
                        )
                    "
                />
            </div>
        </div>
    </div>
</template>
