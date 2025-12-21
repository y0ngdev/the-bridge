<script setup lang="ts">
import { Breadcrumb, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';
withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header class="flex h-16 shrink-0 items-center gap-2">
        <div class="flex items-center gap-2 px-4">
            <SidebarTrigger class="-ml-1 h-8 w-8" />
            <Separator orientation="vertical" class="mr-2 data-[orientation=vertical]:h-4" />
            <Breadcrumb>
                <BreadcrumbList>
                    <template v-for="(item, index) in breadcrumbs" :key="index">
                        <template v-if="index === breadcrumbs.length - 1">
                            <BreadcrumbPage>{{ item.title }}</BreadcrumbPage>
                        </template>
                        <template v-else>
                            <BreadcrumbLink as-child>
                                <Link :href="item.href ?? '#'">{{ item.title }}</Link>
                            </BreadcrumbLink>
                        </template>

                        <BreadcrumbSeparator class="hidden md:block" v-if="index !== breadcrumbs.length - 1" />
                    </template>
                </BreadcrumbList>
            </Breadcrumb>
        </div>
    </header>
</template>
