import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

export interface EnumOption {
    value: string;
    label: string;
    icon?: string;
}

export function useTranslatedEnums() {
    const { t } = useI18n();

    const GENDERS = computed<EnumOption[]>(() => [
        { value: 'male', label: t('enums.gender.male') },
        { value: 'female', label: t('enums.gender.female') },
        { value: 'other', label: t('enums.gender.other') },
    ]);

    const BODY_GOALS = computed<EnumOption[]>(() => [
        { value: 'muscle_gain', label: t('enums.bodyGoal.muscle_gain') },
        { value: 'weight_loss', label: t('enums.bodyGoal.weight_loss') },
        { value: 'maintenance', label: t('enums.bodyGoal.maintenance') },
        { value: 'endurance', label: t('enums.bodyGoal.endurance') },
        { value: 'strength', label: t('enums.bodyGoal.strength') },
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
        { value: 'gym', label: t('enums.trainingPlace.gym') },
        { value: 'home', label: t('enums.trainingPlace.home') },
        { value: 'outdoor', label: t('enums.trainingPlace.outdoor') },
        /*{ value: 'no_preference', label: t('enums.trainingPlace.no_preference') },*/
    ]);

    const DIET_TYPES = computed<EnumOption[]>(() => [
        { value: 'omnivore', label: t('enums.dietType.omnivore') },
        { value: 'vegetarian', label: t('enums.dietType.vegetarian') },
        { value: 'pescatarian', label: t('enums.dietType.pescatarian') },
        { value: 'vegan', label: t('enums.dietType.vegan') },
        { value: 'high_protein', label: t('enums.dietType.high_protein') },
        { value: 'low_carb', label: t('enums.dietType.low_carb') },
        { value: 'ketogenic', label: t('enums.dietType.ketogenic') },
        { value: 'paleo', label: t('enums.dietType.paleo') },
        { value: 'mediterranean', label: t('enums.dietType.mediterranean') },
        /*{ value: 'intermittent_fasting', label: t('enums.dietType.intermittent_fasting') },*/
    ]);

    const DIETARY_PREFERENCES = computed<EnumOption[]>(() => [
        { value: 'omnivore', label: t('enums.dietaryPreference.omnivore'), icon: '🍽️' },
        { value: 'vegetarian', label: t('enums.dietaryPreference.vegetarian'), icon: '🥗' },
        { value: 'pescatarian', label: t('enums.dietaryPreference.pescatarian'), icon: '🐟' },
        { value: 'vegan', label: t('enums.dietaryPreference.vegan'), icon: '🌱' },
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
