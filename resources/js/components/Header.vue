<script setup lang="ts">
import { useSelectedLanguage } from '@/composables/useSelectedLanguage';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { language: selectedLanguage } = useSelectedLanguage();
const page = usePage();

const links = computed(
    () =>
        page.props.footerLinks as {
            indexUrl: string;
            blogUrl: string;
            appUrl: string;
            appStoreUrl: string;
            mealPlanUrl: string;
        },
);

const getPath = (url: string) => url.replace(/^https?:\/\/[^/]+/, '');

const navClass = (url: string) =>
    page.url.startsWith(getPath(url))
        ? 'text-sm font-medium text-white'
        : 'text-sm font-medium text-gray-300 transition hover:text-white';
</script>

<template>
    <header class="w-full border-b border-dark-surfaces-25">
        <nav
            class="container mx-auto flex items-center justify-between px-6 py-4 md:px-0"
        >
            <Link :href="`/${selectedLanguage}`" class="shrink-0">
                <img
                    width="883"
                    height="303"
                    class="h-auto w-28"
                    src="/fytrr-logo.svg"
                    alt="fytrr — AI workout and meal plan generator"
                />
            </Link>

            <div class="flex items-center gap-8">
                <div class="hidden items-center gap-8 md:flex">
                    <Link :href="links.appUrl" :class="navClass(links.appUrl)">{{
                        $t('nav.app')
                    }}</Link>
                    <Link
                        :href="links.mealPlanUrl"
                        :class="navClass(links.mealPlanUrl)"
                        >{{ $t('nav.mealPlan') }}</Link
                    >
                    <Link
                        :href="links.indexUrl"
                        :class="navClass(links.indexUrl)"
                        >{{ $t('nav.workoutPlans') }}</Link
                    >
                    <Link :href="links.blogUrl" :class="navClass(links.blogUrl)"
                        >{{ $t('nav.blog') }}</Link
                    >
                </div>
                <a
                    :href="links.appStoreUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="rounded-full bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-400"
                    >{{ $t('nav.download') }}</a
                >
            </div>
        </nav>
    </header>
</template>
