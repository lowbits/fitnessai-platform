<script setup lang="ts">
import CheckIcon from '@/components/icons/CheckIcon.vue';
import { RadioGroup, RadioGroupLabel, RadioGroupOption } from '@headlessui/vue';
import { computed } from 'vue';

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
            class="mt-2 gap-4"
            :class="{
                'space-y-4': !isHorizontal,
                'flex flex-col items-center justify-evenly md:flex-row':
                    isHorizontal,
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
                        checked
                            ? 'z-10 border-primary-200 bg-linear-to-tr from-transparent to-primary-300/5'
                            : 'border-dark-surfaces-25',
                        'relative flex w-full cursor-pointer justify-between rounded-2xl border p-4 whitespace-nowrap transition-colors focus:outline-none',
                    ]"
                >
                    <span class="ml-3 flex flex-col">
                        <RadioGroupLabel
                            as="span"
                            class="block font-medium text-white capitalize"
                        >
                            <slot :item="item">
                                {{ item }}
                            </slot>
                        </RadioGroupLabel>
                    </span>

                    <span
                        :class="[
                            checked
                                ? 'border-transparent bg-primary-500'
                                : 'border-dark-surfaces-25 bg-dark-surfaces-900',
                            active
                                ? 'ring-1 ring-primary-300 ring-offset-1'
                                : '',
                            'mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full border transition-all',
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
