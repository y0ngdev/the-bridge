<script setup lang="ts">
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { index as tenuresIndex } from '@/routes/tenures';
import { index as alumnusIndex } from '@/routes/alumni';
import { birthdays, distribution, executives } from '@/routes/alumni';
import { Link } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import { home } from '@/routes';
import { NavItem } from '@/types';
import { Cake, Calendar, HardDrive, LayoutGrid, MapPin, UserCheck, Users } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: home(),
        icon: LayoutGrid,
    },
    {
        title: 'Tenures',
        href: tenuresIndex().url,

        icon: Calendar,
    },
    {
        title: 'Alumni',
        href: alumnusIndex().url,
        icon: Users,
    },
    {
        title: 'Birthdays',
        href: birthdays().url,

        icon: Cake,
    },
    {
        title: 'Distribution',
        href: distribution().url,
        icon: MapPin,
    },
    {
        title: 'Executives',
        href: executives().url,
        icon: UserCheck,
    },
    {
        title: 'Backup',
        href: '/settings/backup',
        icon: HardDrive,
    },
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
