<script setup lang="ts">
import Icon from '@/components/Icon.vue';
defineProps<{name?: string, size?: 'sm' | 'md', highlight?: boolean}>()
</script>
<template>
    <div
        class="inline-flex  items-center justify-center rounded-full border-8 p-2 box-content  border-dark-surfaces-200 bg-dark-surfaces-25"
        :class="{'p-1': size === 'sm', 'p-2': size === 'md' || !size }"
    >
        <slot>
            <Icon v-if="name" :size="size === 'sm' ? 20 : 24" :class="{'size-6': (size === 'md' || !size), 'size-5': size === 'sm', 'text-primary-100': !highlight, 'text-primary-500': highlight}" :name="name" />
        </slot>
    </div>
</template>
