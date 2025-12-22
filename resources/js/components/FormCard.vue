<script setup lang="ts"></script>
<template>
    <div class="flex flex-col md:flex-row">
        <div
            class="bg-dark-surfaces-900 border-dark-surfaces-25 relative z-30 flex w-full max-w-xl flex-col rounded-xl border px-4 py-8 ring-zinc-900 md:p-8"
        >
            <slot />
        </div>
    </div>
</template>
