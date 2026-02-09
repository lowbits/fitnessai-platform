import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

export const useSelectedLanguage = () => {
    const page = usePage();
    const selectedLanguage = ref(page.props.currentLocale as string);

    return {
        language: selectedLanguage,
    };
};
