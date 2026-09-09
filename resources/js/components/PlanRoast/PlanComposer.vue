<script setup lang="ts">
import {
    ArrowUp,
    Feather,
    FileText,
    Flame,
    Paperclip,
    X,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const MAX_FILES = 6;
const MAX_SIZE_MB = 10;

interface Attachment {
    file: File;
    url: string | null;
}

interface Props {
    busy: boolean;
    error?: string;
}

const props = withDefaults(defineProps<Props>(), { error: '' });

const emit = defineEmits<{
    submit: [payload: { text: string; files: File[] }];
    invalid: [];
}>();

const tone = defineModel<'roast' | 'neutral'>('tone', { default: 'roast' });

const { t } = useI18n();

const text = ref('');
const attachments = ref<Attachment[]>([]);
const dragOver = ref(false);
const isMac = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const canSend = computed(
    () =>
        !props.busy &&
        (text.value.trim().length > 0 || attachments.value.length > 0),
);

const containerClass = computed(() => {
    if (props.busy) return 'composer-glow';
    if (dragOver.value) return 'border-brand';
    if (props.error) return 'border-red-400/70';
    return 'border-stroke focus-within:border-brand/60';
});

function openPicker(): void {
    fileInput.value?.click();
}

const MAX_IMAGE_DIMENSION = 2000;

async function downscaleImage(file: File): Promise<File> {
    if (!file.type.startsWith('image/')) return file;

    try {
        const bitmap = await createImageBitmap(file);
        const scale = Math.min(
            1,
            MAX_IMAGE_DIMENSION / Math.max(bitmap.width, bitmap.height),
        );

        if (scale === 1 && file.size < 1_200_000) return file;

        const canvas = document.createElement('canvas');
        canvas.width = Math.round(bitmap.width * scale);
        canvas.height = Math.round(bitmap.height * scale);
        canvas
            .getContext('2d')
            ?.drawImage(bitmap, 0, 0, canvas.width, canvas.height);

        const blob = await new Promise<Blob | null>((resolve) =>
            canvas.toBlob(resolve, 'image/jpeg', 0.82),
        );

        if (!blob || blob.size >= file.size) return file;

        return new File([blob], file.name.replace(/\.\w+$/, '.jpg'), {
            type: 'image/jpeg',
        });
    } catch {
        return file;
    }
}

async function addFiles(files: File[]): Promise<void> {
    if (attachments.value.length + files.length > MAX_FILES) {
        emit('invalid');
        return;
    }

    const processed = await Promise.all(files.map(downscaleImage));

    if (processed.some((file) => file.size > MAX_SIZE_MB * 1024 * 1024)) {
        emit('invalid');
        return;
    }

    processed.forEach((file) => {
        attachments.value.push({
            file,
            url: file.type.startsWith('image/')
                ? URL.createObjectURL(file)
                : null,
        });
    });
}

function removeAttachment(index: number): void {
    const [removed] = attachments.value.splice(index, 1);
    if (removed?.url) URL.revokeObjectURL(removed.url);
}

function onPick(event: Event): void {
    const files = (event.target as HTMLInputElement).files;
    if (files?.length) addFiles(Array.from(files));
    (event.target as HTMLInputElement).value = '';
}

function onDrop(event: DragEvent): void {
    dragOver.value = false;
    const files = event.dataTransfer?.files;
    if (files?.length) addFiles(Array.from(files));
}

function onPaste(event: ClipboardEvent): void {
    const files = event.clipboardData?.files;
    if (files?.length) {
        event.preventDefault();
        addFiles(Array.from(files));
    }
}

function submit(): void {
    if (!canSend.value) return;

    emit('submit', {
        text: text.value.trim(),
        files: attachments.value.map((attachment) => attachment.file),
    });
}

onMounted(() => {
    isMac.value = /Mac|iPhone|iPad/i.test(
        navigator.platform || navigator.userAgent,
    );
});

onBeforeUnmount(() => {
    attachments.value.forEach((attachment) => {
        if (attachment.url) URL.revokeObjectURL(attachment.url);
    });
});
</script>

<template>
    <div>
        <div
            class="relative rounded-[24px] border bg-surface p-3 transition-colors"
            :class="containerClass"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="onDrop"
        >
            <input
                ref="fileInput"
                type="file"
                accept=".txt,.pdf,image/jpeg,image/png,image/webp"
                multiple
                class="hidden"
                @change="onPick"
            />

            <!-- Attachment chips -->
            <div
                v-if="attachments.length"
                class="mb-2 flex flex-wrap gap-2 px-1"
            >
                <div
                    v-for="(attachment, i) in attachments"
                    :key="i"
                    class="group relative flex items-center gap-2 rounded-xl border border-stroke bg-surface-raised py-1.5 pr-2 pl-1.5"
                >
                    <a
                        v-if="attachment.url"
                        :href="attachment.url"
                        target="_blank"
                        rel="noopener"
                        class="block h-9 w-9 shrink-0 overflow-hidden rounded-lg"
                    >
                        <img
                            :src="attachment.url"
                            :alt="attachment.file.name"
                            class="h-full w-full object-cover"
                        />
                    </a>
                    <span
                        v-else
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-ink/[0.06] text-ink-muted"
                    >
                        <FileText class="h-4 w-4" />
                    </span>
                    <span class="max-w-[140px] truncate text-xs text-ink">{{
                        attachment.file.name
                    }}</span>
                    <button
                        type="button"
                        class="flex h-5 w-5 items-center justify-center rounded-full text-ink-muted transition-colors hover:bg-ink/10 hover:text-ink"
                        :aria-label="t('planRoast.composer.remove')"
                        @click="removeAttachment(i)"
                    >
                        <X class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>

            <!-- Text -->
            <textarea
                v-model="text"
                rows="3"
                :placeholder="t('planRoast.composer.placeholder')"
                :aria-label="t('planRoast.composer.placeholder')"
                class="max-h-56 w-full resize-none bg-transparent px-2 py-1.5 text-ink placeholder:text-ink-muted/70 focus:outline-none"
                @paste="onPaste"
                @keydown.enter.exact.prevent="submit"
            />

            <!-- Toolbar -->
            <div class="mt-1 flex items-center gap-2 px-1">
                <button
                    type="button"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-ink-muted transition-colors hover:bg-ink/[0.06] hover:text-ink"
                    :aria-label="t('planRoast.composer.attach')"
                    @click="openPicker"
                >
                    <Paperclip class="h-4.5 w-4.5" />
                </button>

                <!-- Tone switch (subtle, neutral) -->
                <div
                    class="inline-flex rounded-full border border-stroke bg-surface-raised p-0.5"
                >
                    <button
                        type="button"
                        class="flex h-7 w-7 items-center justify-center rounded-full transition-colors"
                        :class="
                            tone === 'roast'
                                ? 'bg-ink/10 text-ink'
                                : 'text-ink-muted hover:text-ink'
                        "
                        :title="t('planRoast.tone.roast')"
                        :aria-label="t('planRoast.tone.roast')"
                        :aria-pressed="tone === 'roast'"
                        @click="tone = 'roast'"
                    >
                        <Flame class="h-3.5 w-3.5" />
                    </button>
                    <button
                        type="button"
                        class="flex h-7 w-7 items-center justify-center rounded-full transition-colors"
                        :class="
                            tone === 'neutral'
                                ? 'bg-ink/10 text-ink'
                                : 'text-ink-muted hover:text-ink'
                        "
                        :title="t('planRoast.tone.neutral')"
                        :aria-label="t('planRoast.tone.neutral')"
                        :aria-pressed="tone === 'neutral'"
                        @click="tone = 'neutral'"
                    >
                        <Feather class="h-3.5 w-3.5" />
                    </button>
                </div>

                <button
                    type="button"
                    class="ml-auto flex h-9 w-9 items-center justify-center rounded-full bg-brand text-on-brand transition-opacity disabled:opacity-40"
                    :disabled="!canSend"
                    :aria-label="t('planRoast.composer.send')"
                    @click="submit"
                >
                    <span
                        v-if="busy"
                        class="flex items-center gap-0.5"
                        aria-hidden="true"
                    >
                        <span
                            class="h-1 w-1 animate-bounce rounded-full bg-on-brand [animation-delay:-0.32s]"
                        />
                        <span
                            class="h-1 w-1 animate-bounce rounded-full bg-on-brand [animation-delay:-0.16s]"
                        />
                        <span
                            class="h-1 w-1 animate-bounce rounded-full bg-on-brand"
                        />
                    </span>
                    <ArrowUp v-else class="h-4.5 w-4.5" />
                </button>
            </div>
        </div>

        <p class="mt-2 px-1 text-xs text-ink-muted/70">
            <template v-if="error">
                <span class="text-red-400">{{ error }}</span>
            </template>
            <template v-else>
                {{
                    t('planRoast.composer.hint', {
                        maxFiles: MAX_FILES,
                        maxSize: MAX_SIZE_MB,
                    })
                }}
            </template>
        </p>
    </div>
</template>

<style scoped>
@property --composer-angle {
    syntax: '<angle>';
    initial-value: 0deg;
    inherits: false;
}

/* Animated "AI is working" gradient ring, shown only while busy. */
.composer-glow {
    border-color: transparent;
}

.composer-glow::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1.5px;
    background: conic-gradient(
        from var(--composer-angle),
        transparent 5%,
        var(--v2-accent) 25%,
        transparent 50%,
        var(--v2-accent) 75%,
        transparent 95%
    );
    -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: composer-rotate 2.5s linear infinite;
    pointer-events: none;
}

@keyframes composer-rotate {
    to {
        --composer-angle: 360deg;
    }
}

@media (prefers-reduced-motion: reduce) {
    .composer-glow::before {
        animation: none;
    }
}
</style>
