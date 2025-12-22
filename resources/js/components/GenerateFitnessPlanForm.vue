<script lang="ts" setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

import {
    GENDERS,
    BODY_GOALS,
    SKILL_LEVELS,
    ACTIVITY_LEVELS,
    TRAINING_PLACES,
    DIET_TYPES,
} from '@/constants/enums';

import FormPanel from '@/components/form/FormPanel.vue';
import { TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/vue';
import FormGroup from '@/components/form/FormGroup.vue';
import LabeledInput from '@/components/form/LabeledInput.vue';
import Email from '@/components/icons/email.vue';
import FormTab from '@/components/form/FormTab.vue';
import SelectInput from '@/components/form/SelectInput.vue';
import NumberInput from '@/components/form/NumberInput.vue';
import RadioGroup from '@/components/form/RadioGroup.vue';
import { Input } from '@/components/ui/input';

// State
const activeStep = ref(0);
const showSuccessMessage = ref(false);
const userEmail = ref('');

const form = useForm({
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
});

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
    const randomItem = <T,>(arr: readonly T[]): T => arr[random(arr.length)];

    form.email = `test${random(1000)}@example.com`;
    form.name = 'Test User';
    form.age = String(random(30 - 18) + 18);
    form.gender = randomItem(GENDERS).value;
    form.height = String(random(190 - 130) + 130);
    form.weight = String(parseInt(form.height) - 100);
    form.body_goal = randomItem(BODY_GOALS).value;
    form.activity_level = randomItem(ACTIVITY_LEVELS).value;
    form.skill_level = randomItem(SKILL_LEVELS).value;
    form.diet_type = randomItem(DIET_TYPES).value;
    form.training_place = randomItem(TRAINING_PLACES).value;
    form.training_sessions = getRecommendedTrainingSessions(form.skill_level);
};

// Validation
const validateStep = (step: number): boolean => {
    form.clearErrors();

    switch (step) {
        case 0: // Gender
            if (!form.gender) {
                form.setError('gender', 'Please select your gender');
                return false;
            }
            break;

        case 1: // Age, Height, Weight
            const errors: Record<string, string> = {};
            if (!form.age) errors.age = 'Please add your age';
            if (!form.height) errors.height = 'Please add your height';
            if (!form.weight) errors.weight = 'Please add your weight';

            if (Object.keys(errors).length > 0) {
                form.setError(errors);
                return false;
            }
            break;

        case 2: // Diet Type (optional)
            break;

        case 3: // Activity Level & Skill Level
            if (!form.activity_level) {
                form.setError(
                    'activity_level',
                    'Please select your activity level',
                );
                return false;
            }
            if (!form.skill_level) {
                form.setError('skill_level', 'Please select your skill level');
                return false;
            }
            break;

        case 4: // Body Goal
            if (!form.body_goal) {
                form.setError('body_goal', 'Please select your goal');
                return false;
            }
            break;

        case 5: // Training Place
            if (!form.training_place) {
                form.setError(
                    'training_place',
                    'Please select your training place',
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
                form.setError(
                    'training_sessions',
                    'Please set how often you want to train',
                );
                return false;
            }
            break;
    }

    return true;
};

// Navigation
const nextStep = () => {
    if (validateStep(activeStep.value)) {
        activeStep.value++;
    }
};

const goToStep = (index: number) => {
    if (index < activeStep.value && index >= 0) {
        activeStep.value = index;
    }
};

// Submit
const submit = () => {
    userEmail.value = form.email;

    form.post('/api/v2/onboarding', {
        onSuccess: () => {
            showSuccessMessage.value = true;
        },
        onError: (errors) => {
            console.error('Onboarding failed:', errors);
        },
    });
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
                    Check Your Email! 📧
                </h3>
            </div>

            <div class="space-y-4 text-center">
                <p class="text-lg text-gray-200">
                    Thank you for completing the onboarding! 🎉
                </p>

                <div class="rounded-xl bg-dark-surfaces-500 p-6 text-left">
                    <p class="mb-3 font-semibold text-white">
                        📬 We've sent a verification email to:
                    </p>
                    <p class="mb-4 text-lg font-bold text-primary-400">
                        {{ userEmail }}
                    </p>

                    <div class="mb-4 rounded-lg bg-dark-surfaces-800 p-4">
                        <p class="mb-2 text-sm text-secondary-200">
                            <strong class="text-white"
                                >What happens next:</strong
                            >
                        </p>
                        <ol
                            class="list-inside list-decimal space-y-2 text-sm text-secondary-200"
                        >
                            <li>Check your inbox (and spam folder)</li>
                            <li>Click the verification link in the email</li>
                            <li>
                                We'll immediately start generating your
                                personalized plan
                            </li>
                        </ol>
                    </div>

                    <div
                        class="rounded-lg border border-blue-500/20 bg-blue-900/20 p-4"
                    >
                        <p class="text-sm text-blue-200">
                            <strong>⏱️ Generation Time:</strong> Your complete
                            28-day plan (meals + workouts) will be ready in
                            approximately <strong>3-5 minutes</strong> after
                            verification.
                        </p>
                    </div>
                </div>

                <div class="pt-4">
                    <p class="text-sm text-secondary-300">
                        <strong class="text-white"
                            >Didn't receive the email?</strong
                        ><br />
                        Please check your spam folder or contact us at
                        <a
                            class="font-bold text-primary-400 transition hover:text-primary-300"
                            href="mailto:hello@fitnessai.me"
                        >
                            hello@fitnessai.me
                        </a>
                    </p>
                </div>

                <div class="pt-6">
                    <p class="text-sm text-secondary-200">
                        The verification link is valid for
                        <strong class="text-white">24 hours</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form v-else @submit.prevent="submit" class="w-full flex-1 space-y-4">
        <!-- Dev Helper -->
        <div v-if="true" class="absolute right-0">
            <button
                type="button"
                class="rounded border border-primary-500 bg-transparent px-4 py-2 text-primary-400"
                @click="setDefaultValues"
            >
                🪄 Set Form
            </button>
        </div>

        <TabGroup :selectedIndex="activeStep" @change="goToStep">
            <TabPanels class="mt-2">
                <!-- Step 1: Gender -->
                <FormPanel @click:next="nextStep">
                    <RadioGroup
                        v-model="form.gender"
                        name="gender"
                        label="Gender"
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
                <FormPanel @click:next="nextStep">
                    <FormGroup wrap>
                        <LabeledInput
                            full-width
                            name="age"
                            label="Age"
                            :errors="form.errors.age"
                        >
                            <NumberInput
                                id="age"
                                name="age"
                                placeholder="23"
                                v-model="form.age"
                            />
                        </LabeledInput>

                        <LabeledInput
                            full-width
                            name="height"
                            label="Height"
                            :errors="form.errors.height"
                        >
                            <NumberInput
                                id="height"
                                name="height"
                                placeholder="172"
                                v-model="form.height"
                                suffix="cm"
                            />
                        </LabeledInput>

                        <LabeledInput
                            full-width
                            name="weight"
                            label="Weight"
                            :errors="form.errors.weight"
                        >
                            <NumberInput
                                id="weight"
                                Name="weight"
                                placeholder="70"
                                v-model="form.weight"
                                suffix="kg"
                            />
                        </LabeledInput>
                    </FormGroup>
                </FormPanel>

                <!-- Step 3: Diet Type -->
                <FormPanel @click:next="nextStep">
                    <FormGroup class="mt-4">
                        <LabeledInput
                            full-width
                            label="Dietary Preferences"
                            name="diet_type"
                            :errors="form.errors.diet_type"
                        >
                            <SelectInput
                                id="diet_type"
                                name="diet_type"
                                v-model="form.diet_type"
                                default-label="Select Diet Type"
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
                <FormPanel @click:next="nextStep">
                    <FormGroup wrap class="mt-8">
                        <LabeledInput
                            full-width
                            label="Activity Level"
                            name="activity_level"
                            hint="What's your activity on an average day?"
                            :errors="form.errors.activity_level"
                        >
                            <SelectInput
                                id="activity_level"
                                name="activity_level"
                                v-model="form.activity_level"
                                default-label="Select Activity Level"
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
                            label="Skill Level"
                            name="skill_level"
                            hint="How experienced are you in fitness?"
                            :errors="form.errors.skill_level"
                        >
                            <SelectInput
                                id="skill_level"
                                name="skill_level"
                                v-model="form.skill_level"
                                default-label="Select Skill Level"
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
                <FormPanel @click:next="nextStep">
                    <FormGroup class="mt-8">
                        <LabeledInput
                            full-width
                            label="Fitness Goal"
                            name="body_goal"
                            :errors="form.errors.body_goal"
                        >
                            <SelectInput
                                id="body_goal"
                                name="body_goal"
                                v-model="form.body_goal"
                                default-label="Select Goal"
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
                <FormPanel @click:next="nextStep">
                    <FormGroup class="mt-8">
                        <LabeledInput
                            full-width
                            name="training_place"
                            label="Where do you train?"
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
                <FormPanel @click:next="nextStep">
                    <FormGroup class="relative mt-8">
                        <LabeledInput
                            full-width
                            name="training_sessions"
                            label="How often do you train?"
                            :errors="form.errors.training_sessions"
                        >
                            <NumberInput
                                id="training_sessions"
                                name="training_sessions"
                                v-model="form.training_sessions"
                                suffix="times"
                            />
                        </LabeledInput>
                        <p
                            v-if="isRecommendedTrainingSessions"
                            class="absolute top-11 left-8 text-xs text-secondary-300"
                        >
                            (✨ recommended)
                        </p>
                    </FormGroup>
                </FormPanel>

                <!-- Step 8: Email & Submit -->
                <TabPanel>
                    <FormGroup wrap>
                        <LabeledInput name="name" label="Deine Name">
                            <div class="relative">
                                <Input
                                    v-model="form.name"
                                    placeholder="Alex"
                                    required
                                    class="w-full rounded-xl border border-dark-surfaces-25 bg-transparent px-3 py-2  text-green-600 transition-colors outline-none placeholder:text-secondary-200 focus:border-green-500 focus:ring-green-500 focus:outline-none"
                                />
                            </div>
                        </LabeledInput>
                        <LabeledInput name="email" label="E-Mail Address">
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 flex items-center pl-3"
                                >
                                    <Email />
                                </span>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="your@email.com"
                                    required
                                    class="w-full rounded-xl border border-dark-surfaces-25 bg-transparent px-3 py-2 pl-10 text-green-600 transition-colors outline-none placeholder:text-secondary-200 focus:border-green-500 focus:ring-green-500 focus:outline-none"
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
                            required
                            class="border-transparent bg-white text-green-600 transition-colors outline-none focus:border-green-500 focus:ring-green-500 focus:outline-none"
                        />
                        <label for="terms">
                            I agree to the fitnessAI.me User Agreement and
                            Privacy Policy.
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
                            class="border-transparent bg-white text-green-600 transition-colors outline-none focus:border-green-500 focus:ring-green-500 focus:outline-none"
                        />
                        <label for="newsletter">
                            By checking, you'll sign up for our newsletter and
                            receive your first
                            <strong class="text-base leading-none font-bold">
                                nutrition and workout plan for free </strong
                            >.
                        </label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-8 inline-flex w-full justify-center gap-0.5 overflow-hidden rounded-xl border border-primary-300 bg-primary-500 px-3 py-4 text-xl font-medium text-dark-surfaces-900 transition disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ form.processing ? 'Generating...' : 'Generate' }}
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
