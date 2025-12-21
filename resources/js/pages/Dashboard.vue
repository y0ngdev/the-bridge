<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { home } from '@/routes';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: home().url },
    { title: 'Dashboard', href: home().url },
    { title: 'Dashboard', href: home().url },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="aspect-video rounded-xl bg-muted/50" />
                <div class="aspect-video rounded-xl bg-muted/50" />
                <div class="aspect-video rounded-xl bg-muted/50" />
            </div>
            <div class="min-h-[100vh] flex-1 rounded-xl bg-muted/50 md:min-h-min" />
        </div>
    </AppLayout>
</template>
