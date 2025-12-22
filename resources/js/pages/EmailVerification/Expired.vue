<script setup lang="ts">
interface Props {
    message: string;
}

defineProps<Props>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center">
            <div class="bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700">
                <!-- Warning Icon -->
                <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-500/10 rounded-full mb-6">
                    <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold text-white mb-4">Link Expired</h1>

                <!-- Message -->
                <p class="text-gray-400 mb-8">{{ message }}</p>

                <!-- Info -->
                <div class="bg-gray-750 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm text-gray-300 mb-2">This verification link has expired.</p>
                    <p class="text-sm text-gray-400">Verification links are valid for 24 hours for security reasons.</p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button
                        class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition"
                    >
                        Request New Verification Email
                    </button>

                    <a
                        href="/"
                        class="block px-6 py-3 bg-gray-700 text-gray-300 font-medium rounded-lg hover:bg-gray-600 transition"
                    >
                        Return to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

