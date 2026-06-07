import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

export interface EnumOption {
    value: string;
    label: string;
    description?: string;
    icon?: string;
}

export function useTranslatedEnums() {
    const { t } = useI18n();

    const GENDERS = computed<EnumOption[]>(() => [
        { value: 'male', label: t('enums.gender.male'), icon: 'mars' },
        { value: 'female', label: t('enums.gender.female'), icon: 'venus' },
        { value: 'other', label: t('enums.gender.other'), icon: 'nonBinary' },
    ]);

    const BODY_GOALS = computed<EnumOption[]>(() => [
        { value: 'lose_weight', label: t('enums.bodyGoal.lose_weight') },
        { value: 'build_muscle', label: t('enums.bodyGoal.build_muscle') },
        { value: 'get_fit', label: t('enums.bodyGoal.get_fit') },
    ]);

    const SKILL_LEVELS = computed<EnumOption[]>(() => [
        { value: 'beginner', label: t('enums.skillLevel.beginner') },
        { value: 'intermediate', label: t('enums.skillLevel.intermediate') },
        { value: 'advanced', label: t('enums.skillLevel.advanced') },
        /*{ value: 'elite', label: t('enums.skillLevel.elite') },*/
    ]);

    const ACTIVITY_LEVELS = computed<EnumOption[]>(() => [
        {
            value: 'mainly_sitting',
            label: t('enums.activityLevel.mainly_sitting'),
        },
        {
            value: 'mainly_standing',
            label: t('enums.activityLevel.mainly_standing'),
        },
        {
            value: 'mainly_walking',
            label: t('enums.activityLevel.mainly_walking'),
        },
        { value: 'hard_working', label: t('enums.activityLevel.hard_working') },
    ]);

    const TRAINING_PLACES = computed<EnumOption[]>(() => [
        { value: 'gym', label: t('enums.trainingPlace.gym'), icon: 'dumbbell' },
        {
            value: 'home',
            label: t('enums.trainingPlace.home'),
            icon: 'houseHeart',
        },
        {
            value: 'outdoor',
            label: t('enums.trainingPlace.outdoor'),
            icon: 'leaf',
        },
        /*{ value: 'no_preference', label: t('enums.trainingPlace.no_preference') },*/
    ]);

    const DIET_TYPES = computed<EnumOption[]>(() => [
        { value: 'vegan', label: t('enums.dietType.vegan'), icon: 'sprout' },
        { value: 'high_protein', label: t('enums.dietType.high_protein') },
        { value: 'low_carb', label: t('enums.dietType.low_carb') },
        { value: 'ketogenic', label: t('enums.dietType.ketogenic') },
        { value: 'paleo', label: t('enums.dietType.paleo') },
        { value: 'mediterranean', label: t('enums.dietType.mediterranean') },
        /*{ value: 'intermittent_fasting', label: t('enums.dietType.intermittent_fasting') },*/
    ]);

    const DIETARY_PREFERENCES = computed<EnumOption[]>(() => [
        {
            value: 'omnivore',
            label: t('enums.dietaryPreference.omnivore'),
            description: t('enums.dietaryPreference.omnivoreDescription'),
            icon: 'utensilsCrossed',
        },
        {
            value: 'vegetarian',
            label: t('enums.dietaryPreference.vegetarian'),
            description: t('enums.dietaryPreference.vegetarianDescription'),
            icon: 'salad',
        },
        {
            value: 'pescatarian',
            label: t('enums.dietaryPreference.pescatarian'),
            description: t('enums.dietaryPreference.pescatarianDescription'),
            icon: 'fish',
        },
        {
            value: 'vegan',
            label: t('enums.dietaryPreference.vegan'),
            description: t('enums.dietaryPreference.veganDescription'),
            icon: 'sprout',
        },
    ]);

    const DIET_STYLES = computed<EnumOption[]>(() => [
        { value: 'high_protein', label: t('enums.dietStyle.high_protein') },
        { value: 'low_carb', label: t('enums.dietStyle.low_carb') },
        { value: 'ketogenic', label: t('enums.dietStyle.ketogenic') },
        { value: 'paleo', label: t('enums.dietStyle.paleo') },
        { value: 'mediterranean', label: t('enums.dietStyle.mediterranean') },
    ]);

    const DAYS_OF_WEEK = computed<EnumOption[]>(() => [
        { value: 'monday', label: t('enums.dayOfWeek.monday') },
        { value: 'tuesday', label: t('enums.dayOfWeek.tuesday') },
        { value: 'wednesday', label: t('enums.dayOfWeek.wednesday') },
        { value: 'thursday', label: t('enums.dayOfWeek.thursday') },
        { value: 'friday', label: t('enums.dayOfWeek.friday') },
        { value: 'saturday', label: t('enums.dayOfWeek.saturday') },
        { value: 'sunday', label: t('enums.dayOfWeek.sunday') },
    ]);

    return {
        GENDERS,
        BODY_GOALS,
        SKILL_LEVELS,
        ACTIVITY_LEVELS,
        TRAINING_PLACES,
        DIET_TYPES,
        DIETARY_PREFERENCES,
        DIET_STYLES,
        DAYS_OF_WEEK,
    };
}
