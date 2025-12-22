<script setup lang="ts">
interface Props {
    message: string;
}

const props = defineProps<Props>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <div class="bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700">
                <!-- Error Icon -->
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-500/10 rounded-full mb-6">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold text-white mb-4">Invalid Link</h1>

                <!-- Message -->
                <p class="text-gray-400 mb-8">{{ message }}</p>

                <!-- Info -->
                <div class="bg-gray-750 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm text-gray-300">This verification link is invalid. This could happen if:</p>
                    <ul class="mt-2 text-sm text-gray-400 space-y-1">
                        <li>• The link was copied incorrectly</li>
                        <li>• The link has been tampered with</li>
                        <li>• Your email address was changed</li>
                    </ul>
                </div>

                <!-- Action Button -->
                <a
                    href="/"
                    class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition"
                >
                    Return to Home
                </a>
            </div>
        </div>
    </div>
</template>

