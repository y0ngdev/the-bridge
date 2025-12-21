<script setup lang="ts">
import AppSidebar from '@/components/AppSidebar.vue';
import SiteHeader from '@/components/SiteHeader.vue';
import { SidebarInset, SidebarProvider } from '@/components/ui/sidebar';
import 'vue-sonner/style.css';

import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';

import { Toaster } from '@/components/ui/sonner';
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const isOpen = usePage().props.sidebarOpen;
</script>

<template>
    <SidebarProvider :default-open="isOpen">
        <AppSidebar />
        <SidebarInset>
            <SiteHeader :breadcrumbs="breadcrumbs" />
            <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
                <slot />
            </div>
        </SidebarInset>
    </SidebarProvider>
    <Toaster position="top-right" rich-colors />
</template>
