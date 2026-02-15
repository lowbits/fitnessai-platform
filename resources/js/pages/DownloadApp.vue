<script setup lang="ts">
import AppStoreDownload from '@/components/AppStoreDownload.vue';
import { Button } from '@/components/ui/button';
import { useTracking } from '@/composables/useTracking';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

interface Props {
    userName: string;
    bodyGoal: string | null;
    setPasswordUrl: string | null;
    appStoreUrl: string;
    isMobile: boolean;
    appStoreQrCode: string | null;
    setPasswordQrCode: string | null;
    utmSource?: string;
    utmMedium?: string;
    utmCampaign?: string;
}

const props = defineProps<Props>();

const { t, locale } = useI18n();
const { trackEvent } = useTracking();

const { copy, copied } = useClipboard({
    legacy: true,
    source: computed(() => props.setPasswordUrl ?? ''),
});

const bodyGoalLabel = computed(() => {
    if (!props.bodyGoal) {
        return '';
    }
    return t('enums.bodyGoal.' + props.bodyGoal);
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

const handleActivateClick = () => {
    trackEvent('Download App - Activate Click', utmProps.value);
};

const handleCopyLink = () => {
    if (!props.setPasswordUrl) {
        return;
    }
    copy(props.setPasswordUrl);
    trackEvent('Download App - Copy Link', utmProps.value);
};

onMounted(() => {
    trackEvent('Download App - Page View', utmProps.value);
});
</script>

<template>
    <Head :title="t('downloadApp.meta.title')" />

    <GuestLayout>
        <div class="container mx-auto max-w-lg px-5 pt-7 pb-12">
            <!-- Header -->
            <div class="animate-fade-in mb-6 text-center">
                <div class="mb-4">
                    <img
                        src="/favicon.svg"
                        alt="Fytrr"
                        class="mx-auto h-20 w-20"
                    />
                </div>
                <h1
                    class="mb-2 text-2xl leading-tight font-extrabold tracking-tight text-white"
                >
                    {{ t('downloadApp.greeting', { name: userName }) }}
                </h1>
                <p class="text-base text-gray-300">
                    <template v-if="isMobile">
                        {{
                            t('downloadApp.mobile.body', {
                                goal: bodyGoalLabel,
                            })
                        }}
                    </template>
                    <template v-else>
                        {{ t('downloadApp.desktop.body') }}
                    </template>
                </p>
            </div>

            <!-- Steps Card -->
            <div
                class="animate-fade-in-delay-1 rounded-2xl border border-gray-700 bg-gray-800 p-6 shadow-2xl"
            >
                <!-- ===== MOBILE VIEW ===== -->
                <template v-if="isMobile">
                    <!-- Step 1 -->
                    <div class="mb-6">
                        <div
                            class="mb-3 flex items-center gap-2 text-sm font-semibold text-primary-400"
                        >
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-500/20 text-xs font-bold"
                            >
                                1
                            </span>
                            {{ t('downloadApp.step1.text') }}
                        </div>
                        <AppStoreDownload
                            :app-store-url="appStoreUrl"
                            @click="handleAppStoreClick"
                        />
                    </div>
                    <!-- Step 2 -->
                    <div v-if="setPasswordUrl">
                        <div
                            class="mb-1 border-t border-gray-700 pt-6"
                        ></div>
                        <div
                            class="mb-3 flex items-center gap-2 text-sm font-semibold text-primary-400"
                        >
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-500/20 text-xs font-bold"
                            >
                                2
                            </span>
                            {{ t('downloadApp.step2.text') }}
                        </div>
                        <a
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
                    </div>
                </template>

                <!-- ===== DESKTOP VIEW: aligned grid ===== -->
                <template v-else>
                    <div
                        class="grid items-center justify-items-center gap-x-6 gap-y-5"
                        :style="{
                            gridTemplateColumns: '1fr auto 1fr',
                        }"
                    >
                        <!-- Row 1: Step 1 label (spans full row) -->
                        <div
                            class="col-span-3 flex w-full items-center gap-2 text-sm font-semibold text-primary-400"
                        >
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-500/20 text-xs font-bold"
                            >
                                1
                            </span>
                            {{ t('downloadApp.step1.text') }}
                        </div>

                        <!-- Row 2: QR | divider | badge -->
                        <div class="flex flex-col items-center">
                            <div
                                v-if="appStoreQrCode"
                                class="rounded-xl border border-gray-600 bg-white p-2"
                            >
                                <img
                                    :src="appStoreQrCode"
                                    alt="QR Code - App Store"
                                    class="h-28 w-28"
                                />
                            </div>
                            <span
                                class="mt-1.5 text-[10px] text-gray-500"
                            >
                                {{ t('downloadApp.desktop.scanQr') }}
                            </span>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="h-6 w-px bg-gray-700"></div>
                            <span class="text-[10px] text-gray-500">
                                {{ t('downloadApp.desktop.or') }}
                            </span>
                            <div class="h-6 w-px bg-gray-700"></div>
                        </div>
                        <a
                            :href="appStoreUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="transition-opacity hover:opacity-80"
                            @click="handleAppStoreClick"
                        >
                            <img
                                :src="`/assets/badges/App_Store_Badge_${locale.toUpperCase()}.svg`"
                                alt="Download on App Store"
                                class="h-10 w-auto"
                            />
                        </a>

                        <!-- Step 2 (if applicable) -->
                        <template v-if="setPasswordUrl">
                            <!-- Divider row -->
                            <div
                                class="col-span-3 w-full border-t border-gray-700"
                            ></div>

                            <!-- Row 3: Step 2 label -->
                            <div
                                class="col-span-3 flex w-full items-center gap-2 text-sm font-semibold text-primary-400"
                            >
                                <span
                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-500/20 text-xs font-bold"
                                >
                                    2
                                </span>
                                {{ t('downloadApp.step2.text') }}
                            </div>

                            <!-- Row 4: QR | divider | copy button -->
                            <div
                                v-if="setPasswordQrCode"
                                class="flex flex-col items-center"
                            >
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
                            <div class="flex flex-col items-center gap-1">
                                <div class="h-6 w-px bg-gray-700"></div>
                                <span class="text-[10px] text-gray-500">
                                    {{ t('downloadApp.desktop.or') }}
                                </span>
                                <div class="h-6 w-px bg-gray-700"></div>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <Button
                                    class="relative"
                                    :class="{
                                        'text-transparent!': copied,
                                    }"
                                    variant="secondary"
                                    @click="handleCopyLink"
                                >
                                    <svg
                                        :class="{
                                            'text-transparent!': copied,
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
                                        v-if="copied"
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
                        </template>
                    </div>
                </template>
            </div>

            <!-- Trust line -->
            <p
                class="animate-fade-in-delay-2 mt-5 text-center text-xs text-gray-500"
            >
                {{ t('downloadApp.trustLine') }}
            </p>
        </div>
    </GuestLayout>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease both;
}

.animate-fade-in-delay-1 {
    animation: fade-in 0.5s ease both 0.08s;
}

.animate-fade-in-delay-2 {
    animation: fade-in 0.5s ease both 0.18s;
}
</style>
