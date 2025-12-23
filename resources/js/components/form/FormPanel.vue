<script setup lang="ts">
import {TabPanel} from "@headlessui/vue";
import PrimaryButton from '@/components/PrimaryButton.vue';

</script>
<template>
    <TabPanel>
        <slot />
        <PrimaryButton class="mt-10 w-full" @click="$emit('click:next')" type="button">{{$t('form_panel.submit')}}</PrimaryButton>
    </TabPanel>
</template>

