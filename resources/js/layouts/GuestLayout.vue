<script setup lang="ts">
import Footer from '@/components/Footer.vue';
import Header from '@/components/Header.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <Header />
    <main class="flex min-h-[85vh] flex-col">
        <slot />
    </main>
    <Footer />
</template>
