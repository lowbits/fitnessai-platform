<script setup lang="ts">
defineProps({
    wrap: Boolean,
    wrapOnMobile: Boolean
})
</script>

<template>
    <div class="flex gap-6" :class="{'flex-col md:flex-row': wrapOnMobile, 'flex-col': wrap}">
        <slot/>
    </div>
</template>
