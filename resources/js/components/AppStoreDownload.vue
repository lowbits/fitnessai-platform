<script setup lang="ts">
import { useI18n } from 'vue-i18n';

defineProps<{
    appStoreUrl: string;
    qrCode?: string | null;
}>();

const emit = defineEmits<{
    click: [];
}>();

const { t, locale } = useI18n();
</script>

<template>
    <!-- Desktop: QR Code + divider + App Store badge side by side -->
    <div v-if="qrCode" class="flex items-center justify-center gap-6">
        <div class="flex flex-col items-center">
            <div class="rounded-xl border border-gray-600 bg-white p-2">
                <img
                    :src="qrCode"
                    alt="QR Code - App Store"
                    width="400"
                    height="400"
                    class="h-28 w-28"
                />
            </div>
            <span class="mt-1.5 text-[10px] text-gray-500">
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
            @click="emit('click')"
        >
            <img
                :src="`/assets/badges/App_Store_Badge_${locale.toUpperCase()}.svg`"
                alt="Download on App Store"
                width="120"
                height="40"
                class="h-10 w-auto"
            />
        </a>
    </div>

    <!-- Mobile / no QR: App Store badge only -->
    <a
        v-else
        :href="appStoreUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-block transition-opacity hover:opacity-80"
        @click="emit('click')"
    >
        <img
            :src="`/assets/badges/App_Store_Badge_${locale.toUpperCase()}.svg`"
            alt="Download on App Store"
            class="h-10 w-auto"
        />
    </a>
</template>
