<script setup lang="ts">
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="text-md hover:bg-primary-600 inline-flex items-center justify-center rounded-md border border-transparent bg-primary-500 px-4 py-2.5 text-center font-semibold tracking-widest text-dark-surfaces-900 transition duration-150 ease-in-out focus:ring-1 focus:ring-primary-25 focus:ring-offset-1 focus:outline-none active:bg-gray-900"
    >
        <slot />
    </button>
</template>
