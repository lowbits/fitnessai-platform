<script lang="ts" setup>
import { useTranslatedEnums } from '@/composables/useTranslatedEnums';
import { computed, reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';

import FormGroup from '@/components/form/FormGroup.vue';
import FormPanel from '@/components/form/FormPanel.vue';
import FormTab from '@/components/form/FormTab.vue';
import LabeledInput from '@/components/form/LabeledInput.vue';
import NumberInput from '@/components/form/NumberInput.vue';
import RadioGroup from '@/components/form/RadioGroup.vue';
import SelectInput from '@/components/form/SelectInput.vue';
import Email from '@/components/icons/email.vue';
import { Input } from '@/components/ui/input';
import { useTracking } from '@/composables/useTracking';
import { TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    totalDays: number;
}>();

// i18n
const { t, locale } = useI18n();

const { trackEvent } = useTracking();

// Translated Enums
const {
    GENDERS,
    BODY_GOALS,
    SKILL_LEVELS,
    ACTIVITY_LEVELS,
    TRAINING_PLACES,
    DIET_TYPES,
} = useTranslatedEnums();

// State
const activeStep = ref(0);
const showSuccessMessage = ref(false);
const userEmail = ref('');

const form = reactive({
    email: '',
    name: '',
    age: '',
    gender: '',
    weight: '',
    height: '',
    body_goal: '',
    skill_level: '',
    diet_type: '',
    training_place: '',
    training_sessions: '',
    activity_level: '',
    signup_newsletter: false,
    agree_terms: false,
    processing: false,
    errors: {} as Record<string, string>,
});

// Helper methods for form
const clearErrors = () => {
    form.errors = {};
};

const setError = (
    field: string | Record<string, string | string[]>,
    message?: string,
) => {
    if (typeof field === 'string' && message) {
        form.errors[field] = message;
    } else if (typeof field === 'object') {
        // Handle Laravel validation errors (arrays)
        Object.keys(field).forEach((key) => {
            const error = field[key];
            // If error is array, take first message
            form.errors[key] = Array.isArray(error) ? error[0] : error;
        });
    }
};

// Helpers
const getRecommendedTrainingSessions = (skillLevel: string): string => {
    const recommendations: Record<string, number> = {
        beginner: 3,
        intermediate: 4,
        advanced: 5,
    };
    return String(recommendations[skillLevel] || 3);
};

// Computed
const isRecommendedTrainingSessions = computed(
    () =>
        form.training_sessions ===
        getRecommendedTrainingSessions(form.skill_level),
);

// Development helper
const setDefaultValues = () => {
    const random = (max: number) => Math.floor(Math.random() * max);
    const randomItem = <T,>(arr: T[]): T => arr[random(arr.length)];

    form.email = `test${random(1000)}@example.com`;
    form.name = 'Test User';
    form.age = String(random(30 - 18) + 18);
    form.gender = randomItem(GENDERS.value).value;
    form.height = String(random(210 - 155) + 155);
    form.weight = String(parseInt(form.height) - 100 + random(41) - 10);
    form.body_goal = randomItem(BODY_GOALS.value).value;
    form.activity_level = randomItem(ACTIVITY_LEVELS.value).value;
    form.skill_level = randomItem(SKILL_LEVELS.value).value;
    form.diet_type = randomItem(DIET_TYPES.value).value;
    form.training_place = randomItem(TRAINING_PLACES.value).value;
    form.training_sessions = getRecommendedTrainingSessions(form.skill_level);
};

// Validation
const validateStep = (step: number): boolean => {
    clearErrors();

    switch (step) {
        case 0: // Gender
            if (!form.gender) {
                setError('gender', t('form.validation.genderRequired'));
                return false;
            }
            break;

        case 1: // Age, Height, Weight
            const errors: Record<string, string> = {};
            if (!form.age) errors.age = t('form.validation.ageRequired');
            if (!form.height)
                errors.height = t('form.validation.heightRequired');
            if (!form.weight)
                errors.weight = t('form.validation.weightRequired');

            if (Object.keys(errors).length > 0) {
                setError(errors);
                return false;
            }
            break;

        case 2: // Diet Type (optional)
            break;

        case 3: // Activity Level & Skill Level
            if (!form.activity_level) {
                setError(
                    'activity_level',
                    t('form.validation.activityLevelRequired'),
                );
                return false;
            }
            if (!form.skill_level) {
                setError(
                    'skill_level',
                    t('form.validation.skillLevelRequired'),
                );
                return false;
            }
            break;

        case 4: // Body Goal
            if (!form.body_goal) {
                setError('body_goal', t('form.validation.bodyGoalRequired'));
                return false;
            }
            break;

        case 5: // Training Place
            if (!form.training_place) {
                setError(
                    'training_place',
                    t('form.validation.trainingPlaceRequired'),
                );
                return false;
            }
            // Auto-set recommended sessions
            form.training_sessions = getRecommendedTrainingSessions(
                form.skill_level,
            );
            break;

        case 6: // Training Sessions
            if (
                !form.training_sessions ||
                parseInt(form.training_sessions) < 1
            ) {
                setError(
                    'training_sessions',
                    t('form.validation.trainingSessionsRequired'),
                );
                return false;
            }
            break;
    }

    return true;
};

// Navigation
const nextStep = () => {
    if (activeStep.value === 0) {
        trackEvent('Onboarding Started');
    }
    if (validateStep(activeStep.value)) {
        trackEvent('Onboarding Step Completed', { step: activeStep.value });
        activeStep.value++;
    }
};

const goToStep = (index: number) => {
    if (index < activeStep.value && index >= 0) {
        activeStep.value = index;
    }
};

console.log("WEW #######", locale.value);

// Submit
const submit = async () => {
    if (form.processing) return;

    userEmail.value = form.email;
    form.processing = true;
    clearErrors();

    try {
        trackEvent('Onboarding Completed', {
            body_goal: form.body_goal,
            diet_type: form.diet_type,
        });
        const response = await fetch('/api/v2/onboarding', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN':
                    document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                email: form.email,
                name: form.name,
                age: parseInt(form.age),
                gender: form.gender,
                weight: parseFloat(form.weight),
                height: parseFloat(form.height),
                body_goal: form.body_goal,
                skill_level: form.skill_level,
                diet_type: form.diet_type,
                training_place: form.training_place,
                training_sessions: parseInt(form.training_sessions),
                activity_level: form.activity_level,
                language: locale.value
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                // Validation errors (e.g., email already in use)
                setError(data.errors);

                // If email error exists, navigate back to email step (step 7)
                if (data.errors.email) {
                    activeStep.value = 7;
                }
            } else {
                console.error('Onboarding failed:', data);
            }
            form.processing = false;
            return;
        }

        // Success
        showSuccessMessage.value = true;
    } catch (error) {
        console.error('Onboarding failed:', error);
        form.processing = false;
    }
};
</script>

<template>
    <!-- Success Message -->
    <div v-if="showSuccessMessage" class="w-full flex-1 space-y-4">
        <div class="min-h-[400px] text-white">
            <div class="mb-6 text-center">
                <div
                    class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-500/10"
                >
                    <svg
                        class="h-8 w-8 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        ></path>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-white">
                    {{ $t('form.success.title') }}
                </h3>
            </div>

            <div class="space-y-4 text-center">
                <p class="text-lg text-gray-200">
                    {{ $t('form.success.thankYou') }}
                </p>

                <div class="rounded-xl bg-dark-surfaces-500 p-6 text-left">
                    <p class="mb-3 font-semibold text-white">
                        {{ $t('form.success.emailSent') }}
                    </p>
                    <p class="mb-4 text-lg font-bold text-primary-400">
                        {{ userEmail }}
                    </p>

                    <div class="mb-4 rounded-lg bg-dark-surfaces-800 p-4">
                        <p class="mb-2 text-sm text-secondary-200">
                            <strong class="text-white">{{
                                $t('form.success.whatNext')
                            }}</strong>
                        </p>
                        <ol
                            class="list-inside list-decimal space-y-2 text-sm text-secondary-200"
                        >
                            <li>{{ $t('form.success.steps.inbox') }}</li>
                            <li>{{ $t('form.success.steps.verify') }}</li>
                            <li>{{ $t('form.success.steps.generate') }}</li>
                        </ol>
                    </div>

                    <div
                        class="rounded-lg border border-blue-500/20 bg-blue-900/20 p-4"
                    >
                        <p class="text-sm text-blue-200">
                            <strong>{{
                                $t('form.success.generationTime')
                            }}</strong>
                            {{
                                $t('form.success.generationText', {
                                    days: totalDays,
                                    time: $t('form.success.minutes'),
                                })
                            }}
                        </p>
                    </div>
                </div>

                <div class="pt-4">
                    <p class="text-sm text-secondary-300">
                        <strong class="text-white">{{
                            $t('form.success.didntReceive')
                        }}</strong
                        ><br />
                        {{ $t('form.success.checkSpam') }}
                        <a
                            class="font-bold text-primary-400 transition hover:text-primary-300"
                            href="mailto:hello@fytrr.com"
                        >
                            hello@fytrr.com
                        </a>
                    </p>
                </div>

                <div class="pt-6">
                    <p class="text-sm text-secondary-200">
                        {{
                            $t('form.success.linkValid', {
                                hours: $t('form.success.hours'),
                            })
                        }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form v-else @submit.prevent="submit" class="w-full flex-1 space-y-4">
        <!-- Dev Helper -->
        <div v-if="$page.props.server.isLocal" class="absolute right-0">
            <button
                type="button"
                class="rounded border border-primary-500 bg-transparent px-4 py-2 text-primary-400"
                @click="setDefaultValues"
            >
                🪄 {{ $t('form.devHelper') }}
            </button>
        </div>

        <TabGroup :selectedIndex="activeStep" @change="goToStep">
            <TabPanels class="mt-2 flex min-h-[550px] flex-col">
                <!-- Step 1: Gender -->
                <FormPanel
                    :headline="$t('form.steps.gender.headline')"
                    :subline="$t('form.steps.gender.subline')"
                    @click:next="nextStep"
                >
                    <RadioGroup
                        v-model="form.gender"
                        name="gender"
                        :label="$t('form.steps.gender.label')"
                        :items="GENDERS.map((g) => g.value)"
                    >
                        <template #default="{ item }">
                            {{ GENDERS.find((g) => g.value === item)?.label }}
                        </template>
                    </RadioGroup>
                    <div
                        v-if="form.errors?.gender"
                        class="text-sm text-red-400"
                    >
                        {{ form.errors.gender }}
                    </div>
                </FormPanel>

                <!-- Step 2: Age, Height, Weight -->
                <FormPanel
                    :headline="$t('form.steps.personal.headline')"
                    :subline="$t('form.steps.personal.subline')"
                    @click:next="nextStep"
                >
                    <FormGroup wrap>
                        <LabeledInput
                            full-width
                            name="age"
                            :label="$t('form.steps.personal.age')"
                            :errors="form.errors.age"
                        >
                            <NumberInput
                                id="age"
                                name="age"
                                placeholder="23"
                                v-model="form.age"
                                type="number"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                step="1"
                                min="18"
                                max="100"
                            />
                        </LabeledInput>

                        <LabeledInput
                            full-width
                            name="height"
                            :label="$t('form.steps.personal.height')"
                            :errors="form.errors.height"
                        >
                            <NumberInput
                                id="height"
                                name="height"
                                placeholder="172"
                                v-model="form.height"
                                suffix="cm"
                                type="number"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                step="1"
                                min="100"
                                max="250"
                            />
                        </LabeledInput>

                        <LabeledInput
                            full-width
                            name="weight"
                            :label="$t('form.steps.personal.weight')"
                            :errors="form.errors.weight"
                        >
                            <NumberInput
                                id="weight"
                                name="weight"
                                placeholder="70"
                                v-model="form.weight"
                                suffix="kg"
                                type="number"
                                inputmode="decimal"
                                step="0.5"
                                min="30"
                                max="300"
                            />
                        </LabeledInput>
                    </FormGroup>
                </FormPanel>

                <!-- Step 3: Diet Type -->
                <FormPanel
                    :headline="$t('form.steps.diet.headline')"
                    :subline="$t('form.steps.diet.subline')"
                    @click:next="nextStep"
                >
                    <FormGroup class="mt-4">
                        <LabeledInput
                            full-width
                            :label="$t('form.steps.diet.label')"
                            name="diet_type"
                            :errors="form.errors.diet_type"
                        >
                            <SelectInput
                                id="diet_type"
                                name="diet_type"
                                v-model="form.diet_type"
                                :default-label="
                                    $t('form.steps.diet.placeholder')
                                "
                            >
                                <option
                                    v-for="diet in DIET_TYPES"
                                    :key="diet.value"
                                    :value="diet.value"
                                >
                                    {{ diet.label }}
                                </option>
                            </SelectInput>
                        </LabeledInput>
                    </FormGroup>
                </FormPanel>

                <!-- Step 4: Activity & Skill Level -->
                <FormPanel
                    :headline="$t('form.steps.activity.headline')"
                    :subline="$t('form.steps.activity.subline')"
                    @click:next="nextStep"
                >
                    <FormGroup wrap class="mt-8">
                        <LabeledInput
                            full-width
                            :label="$t('form.steps.activity.activityLabel')"
                            name="activity_level"
                            :hint="$t('form.steps.activity.activityHint')"
                            :errors="form.errors.activity_level"
                        >
                            <SelectInput
                                id="activity_level"
                                name="activity_level"
                                v-model="form.activity_level"
                                :default-label="
                                    $t(
                                        'form.steps.activity.activityPlaceholder',
                                    )
                                "
                            >
                                <option
                                    v-for="activity in ACTIVITY_LEVELS"
                                    :key="activity.value"
                                    :value="activity.value"
                                >
                                    {{ activity.label }}
                                </option>
                            </SelectInput>
                        </LabeledInput>

                        <LabeledInput
                            full-width
                            :label="$t('form.steps.activity.skillLabel')"
                            name="skill_level"
                            :hint="$t('form.steps.activity.skillHint')"
                            :errors="form.errors.skill_level"
                        >
                            <SelectInput
                                id="skill_level"
                                name="skill_level"
                                v-model="form.skill_level"
                                :default-label="
                                    $t('form.steps.activity.skillPlaceholder')
                                "
                            >
                                <option
                                    v-for="skill in SKILL_LEVELS"
                                    :key="skill.value"
                                    :value="skill.value"
                                >
                                    {{ skill.label }}
                                </option>
                            </SelectInput>
                        </LabeledInput>
                    </FormGroup>
                </FormPanel>

                <!-- Step 5: Body Goal -->
                <FormPanel
                    :headline="$t('form.steps.goal.headline')"
                    :subline="$t('form.steps.goal.subline')"
                    @click:next="nextStep"
                >
                    <FormGroup class="mt-8">
                        <LabeledInput
                            full-width
                            :label="$t('form.steps.goal.label')"
                            name="body_goal"
                            :errors="form.errors.body_goal"
                        >
                            <SelectInput
                                id="body_goal"
                                name="body_goal"
                                v-model="form.body_goal"
                                :default-label="
                                    $t('form.steps.goal.placeholder')
                                "
                            >
                                <option
                                    v-for="goal in BODY_GOALS"
                                    :key="goal.value"
                                    :value="goal.value"
                                >
                                    {{ goal.label }}
                                </option>
                            </SelectInput>
                        </LabeledInput>
                    </FormGroup>
                </FormPanel>

                <!-- Step 6: Training Place -->
                <FormPanel
                    :headline="$t('form.steps.training.headline')"
                    :subline="$t('form.steps.training.subline')"
                    @click:next="nextStep"
                >
                    <FormGroup class="mt-8">
                        <LabeledInput
                            full-width
                            name="training_place"
                            :label="$t('form.steps.training.placeLabel')"
                            :errors="form.errors.training_place"
                        >
                            <RadioGroup
                                v-model="form.training_place"
                                name="training_place"
                                :items="TRAINING_PLACES.map((t) => t.value)"
                                alignment="horizontal"
                            >
                                <template #default="{ item }">
                                    {{
                                        TRAINING_PLACES.find(
                                            (t) => t.value === item,
                                        )?.label
                                    }}
                                </template>
                            </RadioGroup>
                        </LabeledInput>
                    </FormGroup>
                </FormPanel>

                <!-- Step 7: Training Sessions -->
                <FormPanel
                    :headline="$t('form.steps.training.sessionsHeadline')"
                    :subline="$t('form.steps.training.sessionsSubline')"
                    @click:next="nextStep"
                >
                    <FormGroup class="relative mt-8">
                        <LabeledInput
                            full-width
                            name="training_sessions"
                            :label="$t('form.steps.training.sessionsLabel')"
                            :errors="form.errors.training_sessions"
                        >
                            <NumberInput
                                id="training_sessions"
                                name="training_sessions"
                                v-model="form.training_sessions"
                                :suffix="
                                    $t('form.steps.training.sessionsSuffix')
                                "
                                type="number"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                step="1"
                                min="1"
                                max="7"
                            />
                        </LabeledInput>
                        <p
                            v-if="isRecommendedTrainingSessions"
                            class="absolute top-11 left-8 text-xs text-secondary-300"
                        >
                            {{ $t('form.steps.training.recommended') }}
                        </p>
                    </FormGroup>
                </FormPanel>

                <!-- Step 8: Email & Submit -->
                <TabPanel class="flex min-h-[550px] flex-col">
                    <div class="shrink-0">
                        <h4
                            class="text-center font-display text-3xl font-semibold"
                        >
                            {{ $t('form.steps.final.headline') }}
                        </h4>
                        <p
                            class="mt-2 text-center font-display text-base leading-6 text-secondary-300"
                        >
                            {{ $t('form.steps.final.subline') }}
                        </p>
                    </div>

                    <div class="mt-8 grow">
                        <FormGroup wrap>
                            <LabeledInput
                                name="name"
                                :label="$t('form.steps.final.name')"
                                :errors="form.errors.name"
                            >
                                <div class="relative">
                                    <Input
                                        v-model="form.name"
                                        placeholder="Alex"
                                        required
                                    />
                                </div>
                            </LabeledInput>
                            <LabeledInput
                                name="email"
                                :label="$t('form.steps.final.email')"
                                :errors="form.errors.email"
                            >
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 flex items-center pl-3"
                                    >
                                        <Email />
                                    </span>
                                    <Input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="your@email.com"
                                        required
                                        class="pl-10"
                                    />
                                </div>
                            </LabeledInput>
                        </FormGroup>

                        <!-- Terms -->
                        <div
                            class="mt-5 flex items-center gap-4 px-3 py-1 text-sm leading-none font-semibold text-secondary-200"
                        >
                            <input
                                id="terms"
                                v-model="form.agree_terms"
                                type="checkbox"
                                @click="trackEvent('Terms Accepted')"
                                required
                                class="border-transparent bg-white text-green-600 accent-primary-500 transition-colors outline-none focus:border-green-500 focus:ring-green-500 focus:outline-none"
                            />

                            <label class="terms">
                                <i18n-t
                                    keypath="form.steps.final.terms"
                                    tag="span"
                                >
                                    <template #termsLink>
                                        <Link
                                            :href="
                                                $page.props.footerLinks
                                                    .legalLinks.terms.url
                                            "
                                            class="link underline hover:text-secondary-300"
                                        >
                                            {{
                                                $t(
                                                    'form.steps.final.termsLinkText',
                                                )
                                            }}
                                        </Link>
                                    </template>
                                    <template #disclaimerLink>
                                        <Link
                                            :href="
                                                $page.props.footerLinks
                                                    .legalLinks.disclaimer.url
                                            "
                                            class="link underline hover:text-secondary-300"
                                        >
                                            {{
                                                $t(
                                                    'form.steps.final.disclaimerLinkText',
                                                )
                                            }}
                                        </Link>
                                    </template>
                                    <template #privacyLink>
                                        <Link
                                            :href="
                                                $page.props.footerLinks
                                                    .legalLinks.data_privacy.url
                                            "
                                            class="link underline hover:text-secondary-300"
                                        >
                                            {{
                                                $t(
                                                    'form.steps.final.privacyLinkText',
                                                )
                                            }}
                                        </Link>
                                    </template>
                                </i18n-t>
                            </label>
                        </div>

                        <!-- Newsletter -->
                        <div
                            class="mt-1 flex items-center gap-4 px-3 py-1 text-sm leading-none font-semibold text-secondary-200"
                        >
                            <input
                                id="newsletter"
                                v-model="form.signup_newsletter"
                                type="checkbox"
                                @click="trackEvent('Newsletter Subscribed')"
                                class="border-transparent bg-white accent-primary-500 transition-colors outline-none focus:border-green-500 focus:ring-green-500 focus:outline-none"
                            />
                            <label for="newsletter">
                                {{ $t('form.steps.final.newsletter') }}
                            </label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-10 inline-flex w-full flex-shrink-0 justify-center gap-0.5 overflow-hidden rounded-xl border border-primary-300 bg-primary-500 px-3 py-4 text-xl font-medium text-dark-surfaces-900 transition disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{
                            form.processing
                                ? $t('form.steps.final.submitting')
                                : $t('form.steps.final.submit')
                        }}
                    </button>
                </TabPanel>

                <!-- Progress Indicator -->
                <TabList class="mt-20 flex space-x-1 p-1">
                    <FormTab v-for="i in 8" :key="i" />
                </TabList>
            </TabPanels>
        </TabGroup>
    </form>
</template>
