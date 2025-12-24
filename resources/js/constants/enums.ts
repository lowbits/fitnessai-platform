// Enum constants matching Laravel backend enums
export const GENDERS = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Diverse' },
] as const;

export const BODY_GOALS = [
    { value: 'muscle_gain', label: 'Muscle Gain' },
    { value: 'weight_loss', label: 'Weight Loss' },
    { value: 'maintenance', label: 'Maintenance' },
    { value: 'endurance', label: 'Endurance' },
    { value: 'strength', label: 'Strength' },
] as const;

export const SKILL_LEVELS = [
    { value: 'beginner', label: 'Beginner' },
    { value: 'intermediate', label: 'Intermediate' },
    { value: 'advanced', label: 'Advanced' },
] as const;

export const ACTIVITY_LEVELS = [
    { value: 'mainly_sitting', label: 'Mainly Sitting' },
    { value: 'mainly_standing', label: 'Mainly Standing' },
    { value: 'mainly_walking', label: 'Mainly Walking' },
    { value: 'hard_working', label: 'Hard Working' },
] as const;

export const TRAINING_PLACES = [
    { value: 'gym', label: 'Gym' },
    { value: 'home', label: 'Home' },
    { value: 'outdoor', label: 'Outdoor' },
] as const;

export const DIET_TYPES = [
    { value: 'omnivore', label: 'Omnivore' },
    { value: 'vegetarian', label: 'Vegetarian' },
    { value: 'pescatarian', label: 'Pescatarian' },
    { value: 'vegan', label: 'Vegan' },
    { value: 'high_protein', label: 'High Protein' },
    { value: 'low_carb', label: 'Low Carb' },
] as const;

// Type exports for TypeScript safety
export type Gender = (typeof GENDERS)[number]['value'];
export type BodyGoal = (typeof BODY_GOALS)[number]['value'];
export type SkillLevel = (typeof SKILL_LEVELS)[number]['value'];
export type ActivityLevel = (typeof ACTIVITY_LEVELS)[number]['value'];
export type TrainingPlace = (typeof TRAINING_PLACES)[number]['value'];
export type DietType = (typeof DIET_TYPES)[number]['value'];
