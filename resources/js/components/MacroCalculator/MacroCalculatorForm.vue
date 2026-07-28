<script setup lang="ts">
import BaseChoiceCard from '@/components/Base/BaseChoiceCard.vue';
import BaseNumberField from '@/components/Base/BaseNumberField.vue';
import BaseSegmentedControl from '@/components/Base/BaseSegmentedControl.vue';
import type {
    Activity,
    Diet,
    Gender,
    Goal,
    useMacroCalculator,
} from '@/composables/useMacroCalculator';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    // The calculator state is owned by the page so the upsell CTA can prefill
    // the plan form from it.
    calc: ReturnType<typeof useMacroCalculator>;
    showUnitToggle?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showUnitToggle: false,
});

const { t } = useI18n();

const {
    input,
    unitSystem,
    setUnitSystem,
    weightImperial,
    heightFeet,
    heightInches,
} = props.calc;

// Option labels reuse the app's shared `enums.*` translations so wording
// matches the rest of the frontend (e.g. omnivore → "Classic" / "Klassisch").
const genderOptions = computed(() =>
    (['male', 'female'] as Gender[]).map((value) => ({
        value,
        label: t(`enums.gender.${value}`),
    })),
);

const goalKey: Record<Goal, string> = {
    lose: 'lose_weight',
    maintain: 'get_fit',
    gain: 'build_muscle',
};
const goalOptions = computed(() =>
    (['lose', 'maintain', 'gain'] as Goal[]).map((value) => ({
        value,
        label: t(`enums.bodyGoal.${goalKey[value]}`),
    })),
);

const activityValues: Activity[] = [
    'mainly_sitting',
    'mainly_standing',
    'mainly_walking',
    'hard_working',
];
const dietValues: Diet[] = ['omnivore', 'vegetarian', 'pescatarian', 'vegan'];

const unitOptions = computed<{ value: 'metric' | 'imperial'; label: string }[]>(
    () => [
        { value: 'metric', label: t('macroCalculator.form.metric') },
        { value: 'imperial', label: t('macroCalculator.form.imperial') },
    ],
);
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Unit toggle (EN only) -->
        <div
            v-if="showUnitToggle"
            class="flex items-center justify-between gap-4"
        >
            <span class="text-sm font-medium text-ink-muted">{{
                t('macroCalculator.form.units')
            }}</span>
            <div class="w-44">
                <BaseSegmentedControl
                    :model-value="unitSystem"
                    :options="unitOptions"
                    :aria-label="t('macroCalculator.form.units')"
                    @update:model-value="setUnitSystem"
                />
            </div>
        </div>

        <!-- Goal -->
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-ink-muted">{{
                t('macroCalculator.form.goal')
            }}</span>
            <BaseSegmentedControl
                v-model="input.goal"
                :options="goalOptions"
                :aria-label="t('macroCalculator.form.goal')"
            />
        </div>

        <!-- Gender -->
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-ink-muted">{{
                t('macroCalculator.form.gender')
            }}</span>
            <BaseSegmentedControl
                v-model="input.gender"
                :options="genderOptions"
                :aria-label="t('macroCalculator.form.gender')"
            />
        </div>

        <!-- Body stats -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <BaseNumberField
                v-model="input.age"
                :label="t('macroCalculator.form.age')"
                :min="14"
                :max="100"
            />

            <template v-if="unitSystem === 'metric'">
                <BaseNumberField
                    v-model="input.weight"
                    :label="t('macroCalculator.form.weight')"
                    suffix="kg"
                    :min="30"
                    :max="300"
                    :step="0.5"
                />
                <BaseNumberField
                    v-model="input.height"
                    :label="t('macroCalculator.form.height')"
                    suffix="cm"
                    :min="100"
                    :max="250"
                />
            </template>

            <template v-else>
                <BaseNumberField
                    v-model="weightImperial"
                    :label="t('macroCalculator.form.weight')"
                    suffix="lbs"
                    :min="66"
                    :max="660"
                />
                <div class="grid grid-cols-2 gap-2">
                    <BaseNumberField
                        v-model="heightFeet"
                        :label="t('macroCalculator.form.height')"
                        suffix="ft"
                        :min="3"
                        :max="8"
                    />
                    <BaseNumberField
                        v-model="heightInches"
                        label="&nbsp;"
                        suffix="in"
                        :min="0"
                        :max="11"
                    />
                </div>
            </template>
        </div>

        <!-- Activity (job / lifestyle) -->
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-ink-muted">{{
                t('macroCalculator.form.activity')
            }}</span>
            <div
                role="radiogroup"
                :aria-label="t('macroCalculator.form.activity')"
                class="flex flex-col gap-2"
            >
                <BaseChoiceCard
                    v-for="value in activityValues"
                    :key="value"
                    v-model="input.activity"
                    :value="value"
                    :title="t(`enums.activityLevel.${value}`)"
                />
            </div>
        </div>

        <!-- Training sessions -->
        <BaseNumberField
            v-model="input.sessions"
            :label="t('macroCalculator.form.sessions')"
            :suffix="t('macroCalculator.form.sessionsSuffix')"
            :min="0"
            :max="7"
        />

        <!-- Diet -->
        <div class="flex flex-col gap-2">
            <span class="text-sm font-medium text-ink-muted">{{
                t('macroCalculator.form.diet')
            }}</span>
            <div
                role="radiogroup"
                :aria-label="t('macroCalculator.form.diet')"
                class="grid grid-cols-2 gap-2"
            >
                <BaseChoiceCard
                    v-for="value in dietValues"
                    :key="value"
                    v-model="input.diet"
                    :value="value"
                    :title="t(`enums.dietaryPreference.${value}`)"
                />
            </div>
        </div>
    </div>
</template>
