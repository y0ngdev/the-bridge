<script setup lang="ts">
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem
} from '@/components/ui/sidebar';

import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Link } from '@inertiajs/vue3';
import { index as tenuresIndex } from '@/routes/tenures';

import AppLogo from '@/components/AppLogo.vue';
import { home } from '@/routes';
import { NavItem } from '@/types';
import { Cake, Calendar, HardDrive, LayoutGrid, UserCheck, Users } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: home(),
        icon: LayoutGrid
    },
    {
        title: 'Tenures',
        href: tenuresIndex().url,

        icon: Calendar
    },
    {
        title: 'Alumni',
        // href: alumnusIndex().url,
        href: home(),
        icon: Users
    },
    {
        title: 'Birthdays',
        // href: birthdays().url,
        href: home(),
        icon: Cake
    },
    {
        title: 'Executives',
        // href: executivesIndex().url,
        href: home(),
        icon: UserCheck
    },
    {
        title: 'Backup',
        href: '/settings/backup',
        icon: HardDrive
    }
    // {
    //     title: 'Templates',
    //     href: templatesIndex().url,
    //     icon: Mail,
    // },
    // {
    //     title: 'Campaigns',
    //     href: campaignsIndex().url,
    //     icon: Send,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="home()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
