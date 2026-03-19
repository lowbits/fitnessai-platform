<script setup lang="ts">
import AppStoreDownload from '@/components/AppStoreDownload.vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    token: string;
    email: string;
    iosAppStoreUrl: string;
    iosAppStoreQrCode: string | null;
    utmSource?: string;
    utmMedium?: string;
    utmCampaign?: string;
}>();

const { t } = useI18n();

const isSafari = computed(() => {
    if (typeof navigator === 'undefined') return false;
    return /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
});

const linkParams = computed(() => {
    const params = new URLSearchParams({
        token: props.token,
        email: props.email,
    });

    if (props.utmSource) params.append('utm_source', props.utmSource);
    if (props.utmMedium) params.append('utm_medium', props.utmMedium);
    if (props.utmCampaign) params.append('utm_campaign', props.utmCampaign);

    return params.toString();
});

const deepLink = computed(
    () => `${window.location.origin}/set-password?${linkParams.value}`,
);
const customSchemeLink = computed(
    () => `fytrr://set-password?${linkParams.value}`,
);

onMounted(() => {
    if (!isSafari.value) {
        window.location.href = customSchemeLink.value;
    }
});
</script>

<template>
    <Head :title="t('set_password.meta.title')">
        <meta
            name="apple-itunes-app"
            :content="`app-id=6757151695, app-argument=${deepLink}`"
        />
    </Head>

    <GuestLayout>
        <div class="mx-auto mt-10 max-w-2xl px-6 md:px-0">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="mb-2 text-4xl font-bold text-white">
                    {{ t('set_password.title') }}
                </h1>
                <p class="text-xl text-gray-300">
                    {{ t('set_password.subtitle') }}
                </p>
            </div>

            <!-- Info Card -->
            <div
                class="rounded-2xl border border-gray-700 bg-gray-800 p-8 shadow-2xl"
            >
                <div class="text-center">
                    <p class="mb-6 text-base leading-relaxed text-gray-300">
                        {{ t('set_password.description') }}
                    </p>

                    <!-- Tip Box -->
                    <div
                        class="mb-8 rounded-xl border border-primary-500 bg-primary-500/10 p-4"
                    >
                        <p class="text-sm text-primary-200">
                            <strong>{{ t('set_password.tip.label') }}</strong>
                            {{
                                isSafari
                                    ? t('set_password.tip.safari')
                                    : t('set_password.tip.other')
                            }}
                        </p>
                    </div>

                    <!-- Activate Account Button -->

                    <a
                        :href="customSchemeLink"
                        class="hover:bg-primary-600 inline-flex w-full items-center justify-center rounded-xl bg-primary-500 px-6 py-4 text-lg font-semibold text-white transition"
                    >
                        {{ t('set_password.activate_account') }}
                    </a>

                    <!-- Divider -->
                    <div class="my-6 text-sm text-gray-500">
                        {{ t('set_password.no_app') }}
                    </div>

                    <!-- Download Section -->
                    <p class="mb-5 text-sm text-gray-400">
                        {{ t('set_password.download_prompt') }}
                    </p>

                    <AppStoreDownload
                        :app-store-url="iosAppStoreUrl"
                        :qr-code="iosAppStoreQrCode"
                    />
                </div>

                <!-- Debug Info (only in development) -->
                <div
                    v-if="$page.props.server.isLocal"
                    class="mt-6 rounded-lg bg-gray-900 p-4 text-left"
                >
                    <p class="mb-2 font-mono text-xs text-gray-500">
                        Debug Info:
                    </p>
                    <p class="text-xs break-all text-gray-400">
                        <strong>Token:</strong> {{ token.substring(0, 20) }}...
                    </p>
                    <p class="text-xs break-all text-gray-400">
                        <strong>Email:</strong> {{ email }}
                    </p>
                    <p class="text-xs text-gray-400">
                        <strong>Deep Link:</strong> fytrr://set-password
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
