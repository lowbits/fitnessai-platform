import { useNavigatorLanguage } from '@vueuse/core';
import { computed } from 'vue';

export const useGuestCountry = () => {
    const { language } = useNavigatorLanguage();

    const country = computed(() => {
        const lang = language.value?.toLowerCase() || 'de-de';

        if (lang.includes('-')) {
            return lang.split('-')[1].toUpperCase();
        }

        // Fallback based on language only
        if (lang.startsWith('de')) return 'DE';
        if (lang.startsWith('en')) return 'US'; // Default English to US
        if (lang.startsWith('fr')) return 'FR';
        if (lang.startsWith('es')) return 'ES';
        if (lang.startsWith('it')) return 'IT';
        if (lang.startsWith('nl')) return 'NL';
        if (lang.startsWith('pl')) return 'PL';

        return 'DE'; // Ultimate fallback
    });

    // Computed helpers
    const isEU = computed(() => {
        const euCountries = [
            'AT',
            'BE',
            'BG',
            'HR',
            'CY',
            'CZ',
            'DK',
            'EE',
            'FI',
            'FR',
            'DE',
            'GR',
            'HU',
            'IE',
            'IT',
            'LV',
            'LT',
            'LU',
            'MT',
            'NL',
            'PL',
            'PT',
            'RO',
            'SK',
            'SI',
            'ES',
            'SE',
        ];
        return euCountries.includes(country.value);
    });

    const isUS = computed(() => country.value === 'US');
    const isUK = computed(() => country.value === 'GB');
    const isCanada = computed(() => country.value === 'CA');
    const isGermany = computed(() => country.value === 'DE');

    // DACH Region (Deutschland, Österreich, Schweiz)
    const isDACH = computed(() => ['DE', 'AT', 'CH'].includes(country.value));

    return {
        country,
        language,
        isEU,
        isUS,
        isUK,
        isCanada,
        isGermany,
        isDACH,
    };
};
