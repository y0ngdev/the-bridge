<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
</script>

<template>
    <header class="flex h-16 shrink-0 items-center gap-2">
        <div class="flex items-center gap-2 px-4">
            <SidebarTrigger class="-ml-1 h-8 w-8" />
            <Separator orientation="vertical" class="mr-2 data-[orientation=vertical]:h-4" />
            <Breadcrumb>
                <BreadcrumbList>
                    <BreadcrumbItem class="hidden md:block">
                        <BreadcrumbLink href="#"> Building Your Application </BreadcrumbLink>
                    </BreadcrumbItem>
                    <BreadcrumbSeparator class="hidden md:block" />
                    <BreadcrumbItem>
                        <BreadcrumbPage>Data Fetching</BreadcrumbPage>
                    </BreadcrumbItem>
                </BreadcrumbList>
            </Breadcrumb>
        </div>
    </header>
</template>
