<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { useVModel } from "@vueuse/core"
import { cn } from "@/lib/utils"

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes["class"]
}>()

const emits = defineEmits<{
  (e: "update:modelValue", payload: string | number): void
}>()

const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
})
</script>

<template>
  <input
    v-model="modelValue"
    data-slot="input"
    :class="cn(
      'w-full bg-transparent px-3 py-2  border border-dark-surfaces-25 outline-none focus:outline-none focus:ring-green-500 transition-colors focus:border-green-500 rounded-xl placeholder:text-secondary-200/20 text-green-600',

      props.class,
    )"
  >
</template>
