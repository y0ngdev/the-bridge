<script setup lang="ts">
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';
import { ChevronRight } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const page = usePage();

const props = defineProps<{
    label?: string;
    items: {
        title: string;
        url: string;
        icon?: LucideIcon;
        isActive?: boolean;
        items?: {
            title: string;
            url: string;
        }[];
    }[];
}>();

const openStates = ref<Record<string, boolean>>({});

const isGroupActive = (item: (typeof props.items)[number]) => {
    return item.isActive || item.items?.some((subItem) => urlIsActive(subItem.url, page.url, false));
};

watch(
    () => page.url,
    () => {
        props.items.forEach((item) => {
            if (item.items?.length && isGroupActive(item)) {
                openStates.value[item.title] = true;
            }
        });
    },
    { immediate: true },
);
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel v-if="label">{{ label }}</SidebarGroupLabel>
        <SidebarMenu>
            <!-- Items without sub-items: direct link -->
            <SidebarMenuItem v-for="item in items.filter((i) => !i.items || i.items.length === 0)" :key="item.title">
                <SidebarMenuButton as-child size="sm" :tooltip="item.title" :is-active="item.isActive ?? urlIsActive(item.url, page.url)">
                    <Link :href="item.url">
                        <component :is="item.icon" v-if="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
            <!-- Items with sub-items: collapsible -->
            <Collapsible
                v-for="item in items.filter((i) => i.items && i.items.length > 0)"
                :key="item.title"
                as-child
                v-model:open="openStates[item.title]"
                class="group/collapsible"
            >
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton size="sm" :tooltip="item.title" :is-active="isGroupActive(item)">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                            <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                <SidebarMenuSubButton as-child :is-active="urlIsActive(subItem.url, page.url)">
                                    <Link :href="subItem.url">
                                        <span>{{ subItem.title }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        </SidebarMenuSub>
                    </CollapsibleContent>
                </SidebarMenuItem>
            </Collapsible>
        </SidebarMenu>
    </SidebarGroup>
</template>
