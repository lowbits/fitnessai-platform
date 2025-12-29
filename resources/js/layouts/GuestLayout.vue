<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Footer from '@/components/Footer.vue';

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
                    src="/fytrr-logo.svg"
                    alt="fytrr.com logo"
                />
            </Link>
        </nav>
    </header>
    <main class="flex min-h-[85vh] flex-col">
        <slot />
    </main>
    <Footer />
</template>
