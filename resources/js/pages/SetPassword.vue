<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

defineProps<{
    token: string;
    email: string;
    iosAppStoreUrl: string;
}>();

const { t } = useI18n();

const deepLink = `fytrr://set-password?token=${encodeURIComponent(props.token)}&email=${encodeURIComponent(props.email)}`;

onMounted(() => {
    window.location.href = deepLink;
});
</script>

<template>
    <Head :title="t('set_password.meta.title')" />

    <GuestLayout>
        <div class="mx-auto mt-10 max-w-2xl px-6 md:px-0">
            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="mb-4">
                    <img
                        src="/logomark.png"
                        alt="Fytrr"
                        class="mx-auto h-20 w-20"
                    />
                </div>
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
                            {{ t('set_password.tip.text') }}
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="my-6 text-sm text-gray-500">
                        {{ t('set_password.no_app') }}
                    </div>

                    <!-- Download Section -->
                    <p class="mb-5 text-sm text-gray-400">
                        {{ t('set_password.download_prompt') }}
                    </p>

                    <div class="flex flex-wrap justify-center gap-3">
                        <!-- App Store Badge (only iOS for now) -->
                        <a
                            :href="iosAppStoreUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="transition-opacity hover:opacity-80"
                        >
                            <img
                                src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/en-us?size=250x83"
                                alt="Download on App Store"
                                class="h-10"
                            />
                        </a>
                    </div>
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
