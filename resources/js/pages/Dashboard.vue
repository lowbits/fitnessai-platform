<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { onMounted } from 'vue';

onMounted(() => {
    window.location.href = 'fytrr://';
});
</script>

<template>
    <GuestLayout>
        <div class="mx-auto mt-10 max-w-2xl">

        </div>
    </GuestLayout>
</template>
