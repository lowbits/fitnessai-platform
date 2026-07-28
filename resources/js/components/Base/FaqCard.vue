<script setup lang="ts">
interface Props {
    question: string;
    answer: string;
}

defineProps<Props>();
</script>

<template>
    <!-- No accordion: the answer is in the initial HTML, which matters for GEO. -->
    <div class="rounded-[16px] border border-stroke bg-surface p-6">
        <h3 class="text-lg font-semibold text-ink">{{ question }}</h3>
        <p class="mt-3 leading-relaxed text-ink-muted">{{ answer }}</p>
    </div>
</template>
