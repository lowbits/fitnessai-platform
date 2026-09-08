<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n();

const email = ref('');
const consent = ref(true);
const processing = ref(false);
const done = ref(false);
const error = ref('');

const submit = async () => {
    error.value = '';

    if (!email.value || !consent.value) {
        error.value = t('androidWaitlist.error');
        return;
    }

    processing.value = true;

    try {
        const response = await fetch('/api/newsletter/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                email: email.value,
                locale: locale.value,
                consent: consent.value,
                source: 'android_waitlist',
            }),
        });

        if (!response.ok) {
            error.value = t('androidWaitlist.error');
            processing.value = false;
            return;
        }

        done.value = true;
    } catch {
        error.value = t('androidWaitlist.error');
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <div class="w-full max-w-md rounded-[20px] border border-stroke bg-surface p-6">
        <h3 class="text-lg font-semibold text-ink">
            {{ t('androidWaitlist.heading') }}
        </h3>

        <p
            v-if="done"
            class="mt-3 flex items-center gap-2 text-sm font-medium text-brand"
        >
            <svg
                class="size-5 shrink-0"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M16.7 5.3a1 1 0 0 1 0 1.4l-7.5 7.5a1 1 0 0 1-1.4 0l-3.5-3.5a1 1 0 1 1 1.4-1.4l2.8 2.79 6.8-6.79a1 1 0 0 1 1.4 0z"
                    clip-rule="evenodd"
                />
            </svg>
            {{ t('androidWaitlist.success') }}
        </p>

        <form v-else class="mt-3" @submit.prevent="submit">
            <p class="text-sm leading-relaxed text-ink-muted">
                {{ t('androidWaitlist.text') }}
            </p>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <input
                    v-model="email"
                    type="email"
                    required
                    :placeholder="t('androidWaitlist.emailPlaceholder')"
                    class="w-full rounded-xl border border-stroke bg-canvas px-4 py-3 text-ink placeholder:text-ink-muted focus:border-brand focus:outline-none"
                />
                <button
                    type="submit"
                    :disabled="processing"
                    class="shrink-0 rounded-xl bg-brand px-5 py-3 font-semibold text-on-brand transition-colors hover:bg-brand/90 disabled:opacity-50"
                >
                    {{ t('androidWaitlist.submit') }}
                </button>
            </div>

            <label class="mt-3 flex items-start gap-2 text-left text-xs text-ink-muted">
                <input
                    v-model="consent"
                    type="checkbox"
                    class="mt-0.5 accent-[color:var(--color-brand)]"
                />
                <span>{{ t('form.steps.final.newsletter') }}</span>
            </label>

            <p v-if="error" class="mt-2 text-xs text-red-400">{{ error }}</p>
        </form>
    </div>
</template>
