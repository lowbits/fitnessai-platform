<script setup lang="ts">
import SelectInput from '@/components/form/SelectInput.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();

interface FooterLinks {
    workoutPlans: Record<string, { url: string; label: string }>;
    indexUrl: string;
    imprintUrl: string;
    languages: Record<
        string,
        { name: string; code: string; url: string; active: boolean }
    >;
    labels: {
        heading: string;
        all: string;
        product: string;
        home: string;
        imprint: string;
        language: string;
        description: string;
        copyright: string;
    };
}

const footerLinks = computed(() => page.props.footerLinks as FooterLinks);
const selectedLanguage = ref(page.props.currentLocale as string);

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
                    <Link href="/">
                        <img
                            class="h-auto w-[180px]"
                            src="/fitness-ai-me-logo.png"
                            alt="Logo fitnessAI.me footer"
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
                                class="transition hover:text-primary-300"
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
                                href="/"
                                class="transition hover:text-primary-300"
                            >
                                {{ footerLinks.labels.home }}
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="footerLinks.indexUrl"
                                class="transition hover:text-primary-300"
                            >
                                {{ footerLinks.labels.heading }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3
                        class="mb-4 text-sm font-semibold tracking-wider text-white uppercase"
                    >
                        Legal
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <Link
                                :href="footerLinks.imprintUrl"
                                class="transition hover:text-primary-300"
                            >
                                {{ footerLinks.labels.imprint }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Language Switcher -->
                <div>
                    <h3
                        class="mb-4 text-sm font-semibold tracking-wider text-white uppercase"
                    >
                        {{ footerLinks.labels.language }}
                    </h3>
                    <SelectInput v-model="selectedLanguage">
                        <option
                            v-for="(lang, code) in footerLinks.languages"
                            :key="code"
                            :value="code"
                        >
                            {{ lang.name }}
                        </option>
                    </SelectInput>
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
