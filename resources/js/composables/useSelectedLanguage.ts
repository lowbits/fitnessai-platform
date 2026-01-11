import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

export const useSelectedLanguage = ()=> {
    const page = usePage()
    const selectedLanguage = ref(page.props.currentLocale as string);

    return  {
        language: selectedLanguage
    }
}
