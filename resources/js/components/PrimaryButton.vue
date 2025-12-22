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
        class="inline-flex text-center justify-center items-center px-4 py-2.5 bg-primary-500 border border-transparent rounded-md font-semibold text-md text-dark-surfaces-900 tracking-widest hover:bg-primary-600  active:bg-gray-900 focus:outline-none focus:ring-1 focus:ring-primary-25 focus:ring-offset-1 transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
