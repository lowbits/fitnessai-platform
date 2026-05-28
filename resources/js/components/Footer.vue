<script setup lang="ts">
import SelectInput from '@/components/form/SelectInput.vue';
import { useSelectedLanguage } from '@/composables/useSelectedLanguage';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const page = usePage();

interface FooterLinks {
    workoutPlans: Record<string, { url: string; label: string }>;
    indexUrl: string;
    legalLinks: Record<string, { url: string; label: string }>;
    languages: Record<
        string,
        { name: string; code: string; url: string; active: boolean }
    >;
    labels: {
        heading: string;
        all: string;
        product: string;
        home: string;
        app: string;
        calorieCalculator: string;
        blog: string;
        about: string;
        legal: string;
        language: string;
        description: string;
        copyright: string;
    };
    calorieCalculatorUrl: string;
    blogUrl: string;
    aboutUrl: string;
    appStoreUrl: string;
    appUrl: string;
    landingPages: { url: string; label: string }[];
}

const footerLinks = computed(() => page.props.footerLinks as FooterLinks);
const { language: selectedLanguage } = useSelectedLanguage();

const getPathFromUrl = (url: string) => {
    return url.replace(/^https?:\/\/[^\/]+/, '');
};

// Watch for language changes and redirect
watch(selectedLanguage, (newLocale) => {
    if (newLocale && newLocale !== page.props.currentLocale) {
        const alternateUrls = page.props.alternateUrls as
            | Record<string, string>
            | undefined;

        let languageUrl: string | undefined;

        if (alternateUrls && alternateUrls[newLocale]) {
            languageUrl = alternateUrls[newLocale];
        } else if (footerLinks.value.languages[newLocale]) {
            languageUrl = footerLinks.value.languages[newLocale].url;
        }

        if (languageUrl) {
            // Full page reload for language change to ensure everything is refreshed
            window.location.href = languageUrl;
        }
    }
});
</script>

<template>
    <footer
        class="border-t border-dark-surfaces-500 bg-dark-surfaces-800 px-4 py-12 text-gray-300 sm:px-6 lg:px-8"
    >
        <div class="container mx-auto max-w-7xl">
            <div
                class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
            >
                <!-- Brand -->
                <div>
                    <Link :href="`/${selectedLanguage}`">
                        <img
                            class="h-auto w-[180px]"
                            width="892"
                            height="323"
                            src="/fytrr-logo.png?v2"
                            alt="fytrr logo"
                        />
                    </Link>
                    <p class="mt-3 text-sm text-gray-400">
                        {{ footerLinks.labels.description }}
                    </p>
                </div>

                <!-- Kostenlose Trainingspläne -->
                <div>
                    <h3
                        class="mb-4 text-sm font-semibold tracking-wider text-white uppercase"
                    >
                        {{ footerLinks.labels.heading }}
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li
                            v-for="(link, key) in footerLinks.workoutPlans"
                            :key="key"
                        >
                            <Link
                                :href="link.url"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url.startsWith(
                                            getPathFromUrl(link.url),
                                        ),
                                    'text-gray-300 transition hover:text-secondary-100':
                                        !$page.url.startsWith(
                                            getPathFromUrl(link.url),
                                        ),
                                }"
                            >
                                {{ link.label }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="footerLinks.indexUrl"
                                class="font-semibold text-primary-300 transition hover:text-primary-200"
                            >
                                {{ footerLinks.labels.all }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Produkt -->
                <div>
                    <h3
                        class="mb-4 text-sm font-semibold tracking-wider text-white uppercase"
                    >
                        {{ footerLinks.labels.product }}
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <Link
                                :href="`/${selectedLanguage}`"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url === `/${selectedLanguage}`,
                                    'hover:text-se-300 text-gray-300 transition':
                                        $page.url !== `/${selectedLanguage}`,
                                }"
                            >
                                {{ footerLinks.labels.home }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="footerLinks.appUrl"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url.startsWith(
                                            footerLinks.appUrl,
                                        ),
                                    'text-gray-300 transition hover:text-secondary-100':
                                        !$page.url.startsWith(
                                            footerLinks.appUrl,
                                        ),
                                }"
                            >
                                {{ footerLinks.labels.app }}
                            </Link>
                        </li>
                        <li
                            v-for="(landing, i) in footerLinks.landingPages"
                            :key="`landing-${i}`"
                        >
                            <Link
                                :href="landing.url"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url.startsWith(
                                            getPathFromUrl(landing.url),
                                        ),
                                    'text-gray-300 transition hover:text-secondary-100':
                                        !$page.url.startsWith(
                                            getPathFromUrl(landing.url),
                                        ),
                                }"
                            >
                                {{ landing.label }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="footerLinks.indexUrl"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url.startsWith(
                                            getPathFromUrl(
                                                footerLinks.indexUrl,
                                            ),
                                        ),
                                    'text-gray-300 transition hover:text-secondary-100':
                                        !$page.url.startsWith(
                                            getPathFromUrl(
                                                footerLinks.indexUrl,
                                            ),
                                        ),
                                }"
                            >
                                {{ footerLinks.labels.heading }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="footerLinks.calorieCalculatorUrl"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url.startsWith(
                                            getPathFromUrl(
                                                footerLinks.calorieCalculatorUrl,
                                            ),
                                        ),
                                    'text-gray-300 transition hover:text-secondary-100':
                                        !$page.url.startsWith(
                                            getPathFromUrl(
                                                footerLinks.calorieCalculatorUrl,
                                            ),
                                        ),
                                }"
                            >
                                {{ footerLinks.labels.calorieCalculator }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="footerLinks.blogUrl"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url.startsWith(
                                            getPathFromUrl(footerLinks.blogUrl),
                                        ),
                                    'text-gray-300 transition hover:text-secondary-100':
                                        !$page.url.startsWith(
                                            getPathFromUrl(footerLinks.blogUrl),
                                        ),
                                }"
                            >
                                {{ footerLinks.labels.blog }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="footerLinks.aboutUrl"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url.startsWith(
                                            getPathFromUrl(
                                                footerLinks.aboutUrl,
                                            ),
                                        ),
                                    'text-gray-300 transition hover:text-secondary-100':
                                        !$page.url.startsWith(
                                            getPathFromUrl(
                                                footerLinks.aboutUrl,
                                            ),
                                        ),
                                }"
                            >
                                {{ footerLinks.labels.about }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3
                        class="mb-4 text-sm font-semibold tracking-wider text-white uppercase"
                    >
                        {{ footerLinks.labels.legal }}
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li
                            v-for="(link, key) in footerLinks.legalLinks"
                            :key="key"
                        >
                            <Link
                                :href="link.url"
                                :class="{
                                    'font-semibold text-secondary-200':
                                        $page.url.startsWith(
                                            getPathFromUrl(link.url),
                                        ),
                                    'text-gray-300 transition hover:text-secondary-100':
                                        !$page.url.startsWith(
                                            getPathFromUrl(link.url),
                                        ),
                                }"
                            >
                                {{ link.label }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Language Switcher -->
                <div>
                    <div class="mb-4">
                        <label
                            for="language_select"
                            class="mb-4 text-sm font-semibold tracking-wider text-white uppercase"
                        >
                            {{ footerLinks.labels.language }}
                        </label>
                        <SelectInput
                            id="language_select"
                            v-model="selectedLanguage"
                        >
                            <option
                                v-for="(lang, code) in footerLinks.languages"
                                :key="code"
                                :value="code"
                            >
                                {{ lang.name }}
                            </option>
                        </SelectInput>
                    </div>
                    <a
                        :href="footerLinks.appStoreUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-20 transition-opacity hover:opacity-80"
                    >
                        <img
                            :src="`/assets/badges/App_Store_Badge_${selectedLanguage.toUpperCase()}.svg`"
                            alt="Download fytrr on the Apple App Store"
                            width="120"
                            height="40"
                            class="h-10 w-auto"
                        />
                    </a>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div
                class="mt-8 border-t border-dark-surfaces-500 pt-8 text-center text-sm text-gray-400"
            >
                <p>{{ footerLinks.labels.copyright }}</p>
            </div>
        </div>
    </footer>
</template>
