import { useTracking } from '@/composables/useTracking';
import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, onMounted, reactive, ref } from 'vue';

export type Gender = 'male' | 'female';
export type Activity =
    | 'mainly_sitting'
    | 'mainly_standing'
    | 'mainly_walking'
    | 'hard_working';
export type Goal = 'lose' | 'maintain' | 'gain';
export type Diet = 'omnivore' | 'vegetarian' | 'pescatarian' | 'vegan';
export type UnitSystem = 'metric' | 'imperial';

/** Canonical form state — always metric. Imperial is a display layer on top. */
export interface MacroInput {
    gender: Gender | null;
    age: number | null;
    height: number | null; // cm
    weight: number | null; // kg
    activity: Activity | null;
    sessions: number | null;
    goal: Goal | null;
    diet: Diet | null;
}

const EMPTY_INPUT: MacroInput = {
    gender: null,
    age: null,
    height: null,
    weight: null,
    activity: null,
    sessions: null,
    goal: null,
    diet: null,
};

export interface MacroPortion {
    grams: number;
    kcal: number;
    share: number;
}

export interface MacroResult {
    bmr: number;
    tdee: number;
    calories: number;
    protein: MacroPortion;
    carbs: MacroPortion;
    fat: MacroPortion;
}

const STORAGE_KEY = 'fytrr:macro-calculator';
const LBS_PER_KG = 2.2046226218;
const CM_PER_INCH = 2.54;

const round = (value: number, decimals = 0) => {
    const factor = 10 ** decimals;
    return Math.round(value * factor) / factor;
};

interface Options {
    defaultUnitSystem?: UnitSystem;
}

export function useMacroCalculator(options: Options = {}) {
    const { trackEvent } = useTracking();

    // Starts empty — no pre-filled defaults. A returning visitor's own last
    // input is restored from localStorage below.
    const input = reactive<MacroInput>({ ...EMPTY_INPUT });
    const unitSystem = ref<UnitSystem>(options.defaultUnitSystem ?? 'metric');
    const isLoading = ref(false);

    let inputChangeTracked = false;

    // Restore the last session's input, if any.
    if (typeof window !== 'undefined') {
        try {
            const saved = window.localStorage.getItem(STORAGE_KEY);
            if (saved) {
                const parsed = JSON.parse(saved) as {
                    input?: Partial<MacroInput>;
                    unitSystem?: UnitSystem;
                };
                if (parsed.input) Object.assign(input, parsed.input);
                if (parsed.unitSystem) unitSystem.value = parsed.unitSystem;
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
            input.weight > 0 &&
            input.activity !== null &&
            input.sessions !== null &&
            input.goal !== null &&
            input.diet !== null,
    );

    // --- Imperial display bindings (write back to canonical metric) ---

    const weightImperial = computed<number | null>({
        get: () =>
            input.weight === null ? null : round(input.weight * LBS_PER_KG),
        set: (lbs) => {
            input.weight = lbs === null ? null : round(lbs / LBS_PER_KG, 1);
        },
    });

    const totalInches = computed(() =>
        input.height === null ? null : input.height / CM_PER_INCH,
    );

    const heightFeet = computed<number | null>({
        get: () =>
            totalInches.value === null
                ? null
                : Math.floor(totalInches.value / 12),
        set: (feet) => {
            const inches =
                totalInches.value === null ? 0 : totalInches.value % 12;
            input.height =
                feet === null
                    ? null
                    : round((feet * 12 + inches) * CM_PER_INCH, 1);
        },
    });

    const heightInches = computed<number | null>({
        get: () =>
            totalInches.value === null ? null : round(totalInches.value % 12),
        set: (inches) => {
            const feet =
                totalInches.value === null
                    ? 0
                    : Math.floor(totalInches.value / 12);
            input.height =
                inches === null
                    ? null
                    : round((feet * 12 + inches) * CM_PER_INCH, 1);
        },
    });

    const setUnitSystem = (system: UnitSystem) => {
        if (system === unitSystem.value) return;
        unitSystem.value = system;
        persist();
        trackEvent('macro_calc_unit_toggled', { unit: system });
    };

    // --- Live compute via Inertia partial reload (single source of truth: the
    //     page controller → Metabolism). Only the `result` prop is refreshed. ---

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
                sessions: input.sessions,
                goal: input.goal,
                diet: input.diet,
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
            window.localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify({
                    input: { ...input },
                    unitSystem: unitSystem.value,
                }),
            );
        } catch {
            // Storage may be unavailable (private mode); ignore.
        }
    };

    watchDebounced(
        () => ({ ...input }),
        () => {
            if (!inputChangeTracked) {
                inputChangeTracked = true;
                trackEvent('macro_calc_input_changed');
            }
            persist();
            reloadResult();
        },
        { debounce: 300, deep: true },
    );

    // On a reload, input may already be complete (restored from localStorage)
    // while the server-rendered result is still null. Compute it once so the
    // result card isn't stuck at zero.
    onMounted(() => {
        if (isComplete.value) reloadResult();
    });

    return {
        input,
        unitSystem,
        setUnitSystem,
        isLoading,
        isComplete,
        weightImperial,
        heightFeet,
        heightInches,
    };
}
