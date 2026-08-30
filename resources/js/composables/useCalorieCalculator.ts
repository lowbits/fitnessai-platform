import { useTracking } from '@/composables/useTracking';
import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, onMounted, reactive, ref } from 'vue';

export type Gender = 'male' | 'female';
export type Activity =
    | 'sedentary'
    | 'light'
    | 'moderate'
    | 'active'
    | 'veryActive';
export type Goal = 'lose' | 'maintain' | 'gain';

export const ACTIVITY_FACTORS: Record<Activity, number> = {
    sedentary: 1.2,
    light: 1.375,
    moderate: 1.55,
    active: 1.725,
    veryActive: 1.9,
};

export interface CalorieInput {
    gender: Gender | null;
    age: number | null;
    height: number | null; // cm
    weight: number | null; // kg
    activity: Activity;
    goal: Goal;
}

export interface MacroPortion {
    grams: number;
    kcal: number;
    share: number;
}

export interface CalorieResult {
    bmr: number;
    tdee: number;
    calories: number;
    goalDelta: number;
    protein: MacroPortion;
    carbs: MacroPortion;
    fat: MacroPortion;
}

const STORAGE_KEY = 'fytrr:calorie-calculator';

const EMPTY_INPUT: CalorieInput = {
    gender: null,
    age: null,
    height: null,
    weight: null,
    activity: 'moderate',
    goal: 'maintain',
};

export function useCalorieCalculator() {
    const { trackEvent } = useTracking();

    const input = reactive<CalorieInput>({ ...EMPTY_INPUT });
    const isLoading = ref(false);

    let inputChangeTracked = false;

    if (typeof window !== 'undefined') {
        try {
            const saved = window.localStorage.getItem(STORAGE_KEY);
            if (saved) {
                const parsed = JSON.parse(saved) as Partial<CalorieInput>;
                Object.assign(input, parsed);
            }
        } catch {
            // Ignore malformed storage.
        }
    }

    const isComplete = computed(
        () =>
            input.gender !== null &&
            input.age !== null &&
            input.age > 0 &&
            input.height !== null &&
            input.height > 0 &&
            input.weight !== null &&
            input.weight > 0,
    );

    const reloadResult = () => {
        if (!isComplete.value || typeof window === 'undefined') return;

        router.reload({
            only: ['result'],
            data: {
                gender: input.gender,
                age: input.age,
                height: input.height,
                weight: input.weight,
                activity: input.activity,
                goal: input.goal,
            },
            preserveUrl: true,
            preserveState: true,
            preserveScroll: true,
            onStart: () => (isLoading.value = true),
            onFinish: () => (isLoading.value = false),
        });
    };

    const persist = () => {
        if (typeof window === 'undefined') return;
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(input));
        } catch {
            // Storage may be unavailable (private mode); ignore.
        }
    };

    watchDebounced(
        () => ({ ...input }),
        () => {
            if (!inputChangeTracked) {
                inputChangeTracked = true;
                trackEvent('calorie_calc_input_changed');
            }
            persist();
            reloadResult();
        },
        { debounce: 300, deep: true },
    );

    onMounted(() => {
        if (isComplete.value) reloadResult();
    });

    return {
        input,
        isComplete,
        isLoading,
    };
}
