<script setup lang="ts">
import AppStoreDownload from '@/components/AppStoreDownload.vue';
import RoundedIcon from '@/components/ui/icons/RoundedIcon.vue';
import { useSelectedLanguage } from '@/composables/useSelectedLanguage';
import {
    ArrowLeftRight,
    ChartNoAxesCombined,
    MessageCircle,
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

defineProps<{
    appStoreUrl: string;
    qrCode?: string | null;
}>();

defineEmits<{
    appStoreClick: [];
}>();

const { t } = useI18n();
const { language } = useSelectedLanguage();
</script>

<template>
    <div
        class="overflow-hidden rounded-2xl border border-dark-surfaces-25 bg-gradient-to-br from-dark-surfaces-800 via-dark-surfaces-900 to-primary-500/10"
    >
        <!-- Mobile: upper half of phone, bottom half clipped -->
        <div class="relative mx-auto h-[200px] w-[180px] overflow-hidden pt-6 md:hidden">
            <img
                :src="`/assets/images/mocks/iphone/dashboard_${language}.png`"
                :alt="t('welcome.app_section.title')"
                width="1177"
                height="2408"
                class="absolute top-6 left-0 w-[180px] drop-shadow-2xl"
                loading="lazy"
                decoding="async"
            />
        </div>
        <hr class="border-primary-500/20 md:hidden" />

        <div
            class="flex flex-col items-center gap-10 p-8 md:flex-row md:gap-16 md:p-12 lg:p-16"
        >
            <!-- Desktop: phone mock side by side -->
            <div class="hidden shrink-0 justify-center md:flex md:w-1/3">
                <img
                    :src="`/assets/images/mocks/iphone/dashboard_${language}.png`"
                    :alt="t('welcome.app_section.title')"
                    width="1177"
                    height="2408"
                    class="h-auto w-[300px] drop-shadow-2xl"
                    loading="lazy"
                    decoding="async"
                />
            </div>

            <!-- Content -->
            <div class="flex-1 space-y-8">
                <div class="space-y-3">
                    <span
                        class="inline-block rounded-full bg-primary-500/15 px-4 py-1.5 text-sm font-medium text-primary-400"
                    >
                        {{ t('welcome.app_section.badge') }}
                    </span>
                    <h2
                        class="font-display text-3xl font-bold tracking-tight text-white md:text-4xl"
                    >
                        {{ t('welcome.app_section.title') }}
                    </h2>
                    <p class="text-lg text-secondary-300">
                        {{ t('welcome.app_section.subtitle') }}
                    </p>
                </div>

                <div class="space-y-5">
                    <div class="flex items-start gap-4">
                        <RoundedIcon size="sm">
                            <ArrowLeftRight
                                class="size-5 text-primary-100"
                            />
                        </RoundedIcon>
                        <div>
                            <h3 class="font-semibold text-white">
                                {{ t('welcome.app_section.feature_swap') }}
                            </h3>
                            <p class="mt-0.5 text-sm text-secondary-300">
                                {{
                                    t(
                                        'welcome.app_section.feature_swap_body',
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <RoundedIcon size="sm">
                            <ChartNoAxesCombined
                                class="size-5 text-primary-100"
                            />
                        </RoundedIcon>
                        <div>
                            <h3 class="font-semibold text-white">
                                {{ t('welcome.app_section.feature_track') }}
                            </h3>
                            <p class="mt-0.5 text-sm text-secondary-300">
                                {{
                                    t(
                                        'welcome.app_section.feature_track_body',
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <RoundedIcon size="sm">
                            <MessageCircle
                                class="size-5 text-primary-100"
                            />
                        </RoundedIcon>
                        <div>
                            <h3 class="font-semibold text-white">
                                {{ t('welcome.app_section.feature_coach') }}
                            </h3>
                            <p class="mt-0.5 text-sm text-secondary-300">
                                {{
                                    t(
                                        'welcome.app_section.feature_coach_body',
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <slot name="before-download" />

                <div class="flex flex-col items-start gap-3 pt-2">
                    <AppStoreDownload
                        :app-store-url="appStoreUrl"
                        :qr-code="qrCode"
                        @click="$emit('appStoreClick')"
                    />
                    <p class="text-sm text-secondary-300">
                        {{ t('welcome.app_section.cta_sub') }}
                    </p>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
