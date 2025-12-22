<script setup lang="ts">
import { computed } from 'vue';
import { RadioGroup, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue';
import CheckIcon from '@/components/icons/CheckIcon.vue';

interface Props {
    label?: string;
    name: string;
    items: string[];
    alignment?: 'horizontal' | 'vertical';
}

const props = withDefaults(defineProps<Props>(), {
    alignment: 'vertical',
});

// Use defineModel for v-model binding
const model = defineModel<string>();

const isHorizontal = computed(() => props.alignment === 'horizontal');
</script>

<template>
    <RadioGroup v-model="model" :name="name">
        <RadioGroupLabel v-if="label" class="text-primary-25">
            {{ label }}
        </RadioGroupLabel>

        <div
            class="gap-4 mt-2"
            :class="{
                'space-y-4': !isHorizontal,
                'flex flex-col md:flex-row justify-evenly items-center': isHorizontal
            }"
        >
            <RadioGroupOption
                v-for="item in items"
                :key="item"
                v-slot="{ checked, active }"
                as="template"
                :value="item"
            >
                <div
                    :class="[
                        checked ? 'z-10 border-primary-200' : 'border-dark-surfaces-25',
                        'rounded-lg relative justify-between w-full whitespace-nowrap flex cursor-pointer border p-4 focus:outline-none transition-colors'
                    ]"
                >
                    <span class="ml-3 flex flex-col">
                        <RadioGroupLabel
                            as="span"
                            class="text-white capitalize block font-medium"
                        >
                            <slot :item="item">
                                {{ item }}
                            </slot>
                        </RadioGroupLabel>
                    </span>

                    <span
                        :class="[
                            checked ? 'bg-primary-500 border-transparent' : 'bg-dark-surfaces-900 border-dark-surfaces-25',
                            active ? 'ring-1 ring-offset-1 ring-primary-300' : '',
                            'mt-0.5 h-4 w-4 shrink-0 rounded-full border flex items-center justify-center transition-all'
                        ]"
                        aria-hidden="true"
                    >
                        <CheckIcon v-if="checked" />
                    </span>
                </div>
            </RadioGroupOption>
        </div>
    </RadioGroup>
</template>

