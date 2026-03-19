<script setup lang="ts">
import AppSection from '@/components/AppSection.vue';
import { Button } from '@/components/ui/button';
import { useDeepLink } from '@/composables/useDeepLink';
import { useTracking } from '@/composables/useTracking';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    userName: string | null;
    bodyGoal: string | null;
    setPasswordUrl: string | null;
    appStoreUrl: string;
    isMobile: boolean;
    appStoreQrCode: string | null;
    setPasswordQrCode: string | null;
    openAppQrCode: string | null;
    utmSource?: string;
    utmMedium?: string;
    utmCampaign?: string;
    isAccountReady: boolean;
}

const props = defineProps<Props>();

const DEEP_LINK_URL = 'fytrr://';

const { t } = useI18n();
const { trackEvent } = useTracking();
const { openApp } = useDeepLink(props.appStoreUrl);

const { copy: copyDeepLink, copied: deepLinkCopied } = useClipboard({
    legacy: true,
    source: DEEP_LINK_URL,
});

const utmProps = computed(() => ({
    utm_source: props.utmSource,
    utm_medium: props.utmMedium,
    utm_campaign: props.utmCampaign,
    device: props.isMobile ? 'mobile' : 'desktop',
}));

const handleAppStoreClick = () => {
    trackEvent('Download App - App Store Click', utmProps.value);
};

const handleOpenAppClick = () => {
    trackEvent('Download App - Open App Click', utmProps.value);
    openApp('', {
        utm_source: props.utmSource ?? 'web',
        utm_medium: props.utmMedium ?? 'download_app',
        utm_campaign: props.utmCampaign ?? 'open_app',
    });
};

const handleCopyDeepLink = () => {
    copyDeepLink(DEEP_LINK_URL);
    trackEvent('Download App - Copy Deep Link', utmProps.value);
};

const handleActivateClick = () => {
    trackEvent('Download App - Activate Click', utmProps.value);
};

onMounted(() => {
    trackEvent('Download App - Page View', utmProps.value);

    // On mobile, try to open the app immediately
    if (props.isMobile) {
        openApp('', {
            utm_source: props.utmSource ?? 'web',
            utm_medium: props.utmMedium ?? 'download_app',
            utm_campaign: props.utmCampaign ?? 'download',
        });
    }
});
</script>

<template>
    <Head :title="t('downloadApp.meta.title')" />

    <GuestLayout>
        <div class="container mx-auto max-w-5xl px-5 pt-7 pb-12">
            <AppSection
                :app-store-url="appStoreUrl"
                :qr-code="appStoreQrCode"
                @app-store-click="handleAppStoreClick"
            >
                <!-- "Already installed?" above download when account ready -->
                <template v-if="isAccountReady" #before-download>
                    <div class="border-t border-white/10 pt-6">
                        <p
                            class="mb-3 text-sm font-semibold text-primary-400"
                        >
                            {{ t('downloadApp.alreadyInstalled') }}
                        </p>

                        <!-- Mobile: button -->
                        <Button
                            v-if="isMobile"
                            class="w-full"
                            size="lg"
                            @click="handleOpenAppClick"
                        >
                            {{ t('downloadApp.openApp') }}
                        </Button>

                        <!-- Desktop: QR | or | copy link -->
                        <div
                            v-else-if="openAppQrCode"
                            class="flex items-center justify-start gap-6"
                        >
                            <div class="flex flex-col items-center">
                                <div
                                    class="rounded-xl border border-gray-600 bg-white p-2"
                                >
                                    <img
                                        :src="openAppQrCode"
                                        alt="QR Code - Open Fytrr"
                                        class="h-28 w-28"
                                    />
                                </div>
                                <span
                                    class="mt-1.5 text-[10px] text-gray-500"
                                >
                                    {{
                                        t('downloadApp.desktop.scanQr')
                                    }}
                                </span>
                            </div>

                            <div
                                class="flex flex-col items-center gap-1"
                            >
                                <div
                                    class="h-6 w-px bg-gray-700"
                                ></div>
                                <span
                                    class="text-[10px] text-gray-500"
                                >
                                    {{ t('downloadApp.desktop.or') }}
                                </span>
                                <div
                                    class="h-6 w-px bg-gray-700"
                                ></div>
                            </div>

                            <div
                                class="flex flex-col items-center gap-2"
                            >
                                <Button
                                    class="relative"
                                    :class="{
                                        'text-transparent!':
                                            deepLinkCopied,
                                    }"
                                    variant="secondary"
                                    @click="handleCopyDeepLink"
                                >
                                    <svg
                                        :class="{
                                            'text-transparent!':
                                                deepLinkCopied,
                                        }"
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <span
                                        v-if="deepLinkCopied"
                                        class="absolute inset-0 flex items-center justify-center text-green-500"
                                    >
                                        {{
                                            t(
                                                'downloadApp.desktop.copied',
                                            )
                                        }}
                                    </span>
                                    {{
                                        t('downloadApp.desktop.copyLink')
                                    }}
                                </Button>
                                <span
                                    class="text-[10px] text-gray-500"
                                >
                                    {{
                                        t(
                                            'downloadApp.desktop.copyHint',
                                        )
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Activate account section (user needs to set password) -->
                <div
                    v-if="setPasswordUrl"
                    class="border-t border-white/10 pt-6"
                >
                    <p
                        class="mb-3 text-sm font-semibold text-primary-400"
                    >
                        {{ t('downloadApp.step2.text') }}
                    </p>

                    <!-- Mobile: button -->
                    <a
                        v-if="isMobile"
                        :href="setPasswordUrl"
                        @click="handleActivateClick"
                    >
                        <Button
                            variant="outline"
                            size="lg"
                            class="w-full"
                        >
                            {{ t('downloadApp.step2.cta') }}
                        </Button>
                    </a>

                    <!-- Desktop: QR code -->
                    <div
                        v-else-if="setPasswordQrCode"
                        class="flex items-center justify-start"
                    >
                        <div class="flex flex-col items-center">
                            <div
                                class="rounded-xl border border-gray-600 bg-white p-2"
                            >
                                <img
                                    :src="setPasswordQrCode"
                                    alt="QR Code - Activate"
                                    class="h-28 w-28"
                                />
                            </div>
                            <span
                                class="mt-1.5 text-[10px] text-gray-500"
                            >
                                {{ t('downloadApp.desktop.scanQr') }}
                            </span>
                        </div>
                    </div>
                </div>
            </AppSection>
        </div>
    </GuestLayout>
</template>
