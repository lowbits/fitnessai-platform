<script setup lang="ts">

import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <header class="w-full border-b border-dark-surfaces-25">
        <nav class="container mx-auto px-3 py-4">
            <Link href="/">
                <img
                    class="h-auto w-40"
                    src="/fitness-ai-me-logo.svg"
                    alt="fitnessAi.me logo"
                />
            </Link>
        </nav>
    </header>
    <main class="flex flex-col min-h-[85vh]">
        <slot />
    </main>
    <footer class="bg-dark-surfaces-800 p-8 text-primary-200">
        <nav class="container mx-auto flex justify-between">
            <Link href="/">fitnessai.me</Link>
            <Link href="/imprint">Imprint</Link>
        </nav>
    </footer>
</template>
