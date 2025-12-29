<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <header class="w-full border-b border-dark-surfaces-25">
        <nav class="container mx-auto px-6 py-4 md:px-0">
            <Link href="/">
                <img
                    class="h-auto w-40"
                    src="/fytrr-logo.svg"
                    alt="fytrr.com logo"
                />
            </Link>
        </nav>
    </header>
</template>
