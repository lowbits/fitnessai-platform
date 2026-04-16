<script setup lang="ts">
import { useSelectedLanguage } from '@/composables/useSelectedLanguage';
import { Link } from '@inertiajs/vue3';

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
                    alt="fytrr — AI workout and meal plan generator"
                />
            </Link>
        </nav>
    </header>
</template>
