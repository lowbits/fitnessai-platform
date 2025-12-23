import { createI18n } from 'vue-i18n';
import { locales, type Locale } from './i18n/locales';

const i18n = createI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: locales,
});

export const setLocale = (locale: string) => {
    if (locales[locale as Locale]) {
        i18n.global.locale = locale as Locale;
    } else {
        console.error(`Locale ${locale} not found.`);
    }
};

export default i18n;
