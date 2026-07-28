<script setup lang="ts">
interface Meal {
    label: string;
    kcal: number;
}

interface Props {
    heading: string;
    meals: Meal[];
}

defineProps<Props>();
</script>

<template>
    <div>
        <p class="text-sm font-semibold text-paper-soft">{{ heading }}</p>
        <div class="mt-3 grid grid-cols-3 gap-3">
            <div
                v-for="meal in meals"
                :key="meal.label"
                class="rounded-[8px] border border-paper-line bg-paper px-3 py-3 text-center"
            >
                <p class="text-xs text-paper-soft">{{ meal.label }}</p>
                <p class="mt-1 text-paper-ink tabular-nums">
                    <span class="font-bold">{{ meal.kcal }}</span>
                    <span class="text-xs"> kcal</span>
                </p>
            </div>
        </div>
    </div>
</template>
