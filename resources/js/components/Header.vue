<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useSelectedLanguage } from '@/composables/useSelectedLanguage';

const { language: selectedLanguage } = useSelectedLanguage();
</script>

<template>
    <header class="w-full border-b border-dark-surfaces-25">
        <nav class="container mx-auto px-6 py-4 md:px-0">
            <Link :href="`/${selectedLanguage}`">
                <img
                    width="883"
                    height="303"
                    class="h-auto w-28"
                    src="/fytrr-logo.svg"
                    alt="fytrr.com logo"
                />
            </Link>
        </nav>
    </header>
</template>
