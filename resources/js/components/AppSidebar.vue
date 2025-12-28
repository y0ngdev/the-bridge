<script setup lang="ts">
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { index as tenuresIndex } from '@/routes/tenures';
import { index as departmentsIndex } from '@/routes/departments';
import { index as alumnusIndex } from '@/routes/alumni';
import { birthdays, distribution, executives } from '@/routes/alumni';
import { index as rwIndex } from '@/routes/redemption-week';
import { Link, usePage } from '@inertiajs/vue3';
import AppLogo from '@/components/AppLogo.vue';
import { home } from '@/routes';
import { NavItem } from '@/types';
import { Building2, Cake, Calendar, CalendarDays, HardDrive, LayoutGrid, MapPin, UserCheck, Users } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.is_admin ?? false);

const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: home(),
        icon: LayoutGrid,
    },
    {
        title: 'Tenures',
        href: tenuresIndex().url,
        icon: Calendar,
        adminOnly: true,
    },
    {
        title: 'Departments',
        href: departmentsIndex().url,
        icon: Building2,
        adminOnly: true,
    },
    {
        title: 'Alumni',
        href: alumnusIndex().url,
        icon: Users,
    },
    {
        title: 'Redemption Week',
        href: rwIndex().url,
        icon: CalendarDays,
    },
    {
        title: 'Birthdays',
        href: birthdays().url,
        icon: Cake,
    },
    {
        title: 'Location Distribution',
        href: distribution().url,
        icon: MapPin,
        adminOnly: true,
    },
    {
        title: 'Executives',
        href: executives().url,
        icon: UserCheck,
        adminOnly: true,
    },
    {
        title: 'Backup',
        href: '/settings/backup',
        icon: HardDrive,
        adminOnly: true,
    },
];

const mainNavItems = computed(() => 
    allNavItems.filter(item => !item.adminOnly || isAdmin.value)
);
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
