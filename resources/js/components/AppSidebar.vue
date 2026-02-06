<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { home } from '@/routes';
import { index as alumnusIndex, birthdays, distribution, executives } from '@/routes/alumni';
import { index as departmentsIndex } from '@/routes/departments';
import { index as rwIndex } from '@/routes/redemption-week';
import { index as tenuresIndex } from '@/routes/tenures';
import { Link, usePage } from '@inertiajs/vue3';
import { CalendarDays, LayoutGrid, UserCog, Users } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.is_admin ?? false);

// Navigation items with collapsible groups
const navItems = computed(() => {
    const items = [
        {
            title: 'Dashboard',
            url: home(),
            icon: LayoutGrid,
        },
        {
            title: 'Alumni',
            url: '#',
            icon: Users,
            isActive: urlIsActive('/alumni', page.url),
            items: [
                { title: 'All Alumni', url: alumnusIndex().url },
                { title: 'Birthdays', url: birthdays().url },
                ...(isAdmin.value
                    ? [
                          { title: 'Duplicates', url: '/alumni/duplicates/detect' },
                          { title: 'Pending Updates', url: '/admin/pending-updates' },
                          { title: 'Location Distribution', url: distribution().url },
                          { title: 'Executives', url: executives().url },
                      ]
                    : []),
            ],
        },
        {
            title: 'Events',
            url: '#',
            icon: CalendarDays,
            isActive: urlIsActive('/redemption-week', page.url),
            items: [{ title: 'Redemption Week', url: rwIndex().url }],
        },
        ...(isAdmin.value
            ? [
                  {
                      title: 'Administration',
                      url: '#',
                      icon: UserCog,
                      items: [
                          { title: 'Tenures', url: tenuresIndex().url },
                          { title: 'Departments', url: departmentsIndex().url },
                          { title: 'User Management', url: '/admin/users' },
                          { title: 'Backup', url: '/settings/backup' },
                      ],
                  },
              ]
            : []),
    ];

    return items;
});
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
            <NavMain :items="navItems" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
