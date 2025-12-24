<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Users, Calendar, Cake, TrendingUp, ArrowUpRight, MapPin, Briefcase, Search } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { BreadcrumbItem } from '@/types';
import { home } from '@/routes';
import { index as alumniIndex } from '@/actions/App/Http/Controllers/AlumnusController';
import { Link, Deferred } from '@inertiajs/vue3';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { Skeleton } from '@/components/ui/skeleton';
import { ref, computed } from 'vue';

interface Stat {
    title: string;
    value: number | string | undefined;
    icon: any;
    description: string;
}

const props = defineProps<{
    stats?: {
        total_alumni: number;
        total_tenures: number;
        birthdays_today: number;
        new_this_month: number;
    };
    gender_distribution?: {
        male: number;
        female: number;
        unspecified: number;
    };
    state_distribution?: Array<{
        state: string;
        total: number;
    }>;
    unit_distribution?: Array<{
        unit: string;
        total: number;
    }>;
    state_unit_breakdown?: Array<{
        state: string;
        unit: string;
        total: number;
    }>;
    recent_alumni?: Array<{
        id: number;
        name: string;
        email: string;
        tenure: string;
        initials: string;
    }>;
    my_stats?: {
        total: number;
        this_month: number;
        success_rate: number;
        by_type: Array<{ type: string; count: number }>;
    };
}>();

import LogActivityChart from '@/components/LogActivityChart.vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: home().url },
];

const searchQuery = ref('');

const filteredBreakdown = computed(() => {
    if (!props.state_unit_breakdown) return [];
    if (!searchQuery.value) return props.state_unit_breakdown;
    const q = searchQuery.value.toLowerCase();
    return props.state_unit_breakdown.filter((item) =>
        item.state.toLowerCase().includes(q) ||
        item.unit.toLowerCase().includes(q)
    );
});

const statConfigs = computed((): Stat[] => [
    {
        title: 'Total Alumni',
        value: props.stats?.total_alumni,
        icon: Users,
        description: 'Total registered members',
    },
    {
        title: 'Tenures',
        value: props.stats?.total_tenures,
        icon: Calendar,
        description: 'Academic years tracked',
    },
    {
        title: 'Birthdays Today',
        value: props.stats?.birthdays_today,
        icon: Cake,
        description: 'Celebrating today',
    },
    {
        title: 'New This Month',
        value: props.stats?.new_this_month,
        icon: TrendingUp,
        description: 'Recent registrations',
    },
]);

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <Deferred data="stats">
                <template #fallback>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <Card v-for="i in 4" :key="i">
                            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                                <Skeleton class="h-4 w-24" />
                                <Skeleton class="h-4 w-4 rounded-full" />
                            </CardHeader>
                            <CardContent>
                                <Skeleton class="h-8 w-16 mb-2" />
                                <Skeleton class="h-3 w-32" />
                            </CardContent>
                        </Card>
                    </div>
                </template>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="stat in statConfigs" :key="stat.title">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                {{ stat.title }}
                            </CardTitle>
                            <component :is="stat.icon" class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent v-if="stats">
                            <div class="text-2xl font-bold">{{ stat.value }}</div>
                            <p class="text-xs text-muted-foreground">
                                {{ stat.description }}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </Deferred>

            <Deferred data="my_stats">
                <template #fallback>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                        <Card class="col-span-3">
                            <CardHeader><Skeleton class="h-6 w-48" /></CardHeader>
                            <CardContent class="grid gap-4 grid-cols-3">
                                <Skeleton v-for="i in 3" :key="i" class="h-24 w-full" />
                            </CardContent>
                        </Card>
                         <Card class="col-span-4">
                            <CardHeader><Skeleton class="h-6 w-48" /></CardHeader>
                            <CardContent><Skeleton class="h-[200px]" /></CardContent>
                        </Card>
                    </div>
                </template>
                <div v-if="my_stats" class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <!-- KPI Cards -->
                    <Card class="col-span-3">
                        <CardHeader>
                            <CardTitle>My Communication Performance</CardTitle>
                            <CardDescription>Your personal outreach statistics.</CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-4 grid-cols-3">
                            <div class="flex flex-col items-center justify-center p-4 bg-muted/30 rounded-lg border text-center">
                                <span class="text-3xl font-bold">{{ my_stats.total }}</span>
                                <span class="text-xs text-muted-foreground mt-1">Total Logs</span>
                            </div>
                            <div class="flex flex-col items-center justify-center p-4 bg-muted/30 rounded-lg border text-center">
                                <span class="text-3xl font-bold">{{ my_stats.this_month }}</span>
                                <span class="text-xs text-muted-foreground mt-1">This Month</span>
                            </div>
                             <div class="flex flex-col items-center justify-center p-4 bg-muted/30 rounded-lg border text-center">
                                <span class="text-3xl font-bold text-green-600">{{ my_stats.success_rate }}%</span>
                                <span class="text-xs text-muted-foreground mt-1">Success Rate</span>
                            </div>
                        </CardContent>
                    </Card>
                    
                    <!-- Chart -->
                    <Card class="col-span-4">
                        <CardHeader>
                            <CardTitle>Activity by Type</CardTitle>
                            <CardDescription>Breakdown of your communication methods.</CardDescription>
                        </CardHeader>
                        <CardContent class="pl-2">
                            <LogActivityChart :data="my_stats.by_type" />
                        </CardContent>
                    </Card>
                </div>
            </Deferred>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Deferred data="gender_distribution">
                    <template #fallback>
                        <Card class="col-span-2">
                            <CardHeader><Skeleton class="h-6 w-32" /></CardHeader>
                            <CardContent class="space-y-4 pt-4">
                                <div v-for="i in 3" :key="i" class="space-y-2">
                                    <Skeleton class="h-4 w-full" />
                                    <Skeleton class="h-2 w-full rouned-full" />
                                </div>
                            </CardContent>
                        </Card>
                    </template>
                    <Card v-if="gender_distribution && stats" class="col-span-2">
                        <CardHeader>
                            <CardTitle>Gender Distribution</CardTitle>
                            <CardDescription>Breakdown by gender identity.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4 pt-4">
                                <div v-for="(count, gender) in gender_distribution" :key="gender" class="space-y-1.5">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="capitalize font-medium">{{ gender }}</span>
                                        <span class="text-muted-foreground">{{ count }} ({{ Math.round((count / stats.total_alumni) * 100) || 0 }}%)</span>
                                    </div>
                                    <div class="h-2 w-full bg-muted rounded-full overflow-hidden">
                                        <div 
                                            class="h-full transition-all duration-500" 
                                            :class="gender === 'male' ? 'bg-blue-500' : gender === 'female' ? 'bg-pink-500' : 'bg-gray-500'"
                                            :style="{ width: `${(count / stats.total_alumni) * 100}%` }"
                                        />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Deferred>

                <Deferred data="unit_distribution">
                    <template #fallback>
                        <Card class="col-span-2">
                            <CardHeader><Skeleton class="h-6 w-32" /></CardHeader>
                            <CardContent class="space-y-4 pt-4">
                                <div v-for="i in 4" :key="i" class="flex gap-4 items-center">
                                    <Skeleton class="h-8 w-8 rounded-full" />
                                    <div class="space-y-2">
                                        <Skeleton class="h-3 w-32" />
                                        <Skeleton class="h-1.5 w-24" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </template>
                    <Card v-if="unit_distribution && stats" class="col-span-2">
                        <CardHeader>
                            <CardTitle>Top Units</CardTitle>
                            <CardDescription>Most active alumni units.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="item in unit_distribution.slice(0, 4)" :key="item.unit" class="flex items-center gap-4">
                                    <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center text-[10px] font-bold">
                                        {{ item.unit.split(' ').map(w => w[0]).join('').substring(0, 2) }}
                                    </div>
                                    <div class="grid gap-1">
                                        <p class="text-xs font-medium">{{ item.unit }}</p>
                                        <div class="h-1.5 w-32 bg-muted rounded-full overflow-hidden">
                                            <div class="h-full bg-primary" :style="{ width: `${(item.total / stats.total_alumni) * 100}%` }" />
                                        </div>
                                    </div>
                                    <div class="ml-auto text-xs font-medium">{{ item.total }}</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Deferred>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                <Deferred data="state_distribution">
                    <template #fallback>
                        <Card class="col-span-4">
                            <CardHeader><Skeleton class="h-6 w-32" /></CardHeader>
                            <CardContent><Skeleton class="h-[200px] w-full" /></CardContent>
                        </Card>
                    </template>
                    <Card v-if="state_distribution && stats" class="col-span-4">
                        <CardHeader class="flex flex-row items-center">
                            <div class="grid gap-2">
                                <CardTitle>Geographic Breakdown</CardTitle>
                                <CardDescription>Alumni distribution across states.</CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="rounded-md border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>State</TableHead>
                                            <TableHead class="text-right">Count</TableHead>
                                            <TableHead class="text-right">Percentage</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="item in state_distribution.slice(0, 5)" :key="item.state">
                                            <TableCell class="font-medium">{{ item.state }}</TableCell>
                                            <TableCell class="text-right">{{ item.total }}</TableCell>
                                            <TableCell class="text-right">{{ Math.round((item.total / stats.total_alumni) * 100) }}%</TableCell>
                                        </TableRow>
                                        <TableRow v-if="state_distribution.length === 0">
                                            <TableCell colspan="3" class="text-center py-4 text-muted-foreground italic">No state data available</TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </CardContent>
                    </Card>
                </Deferred>

                <Deferred data="recent_alumni">
                    <template #fallback>
                        <Card class="col-span-3">
                            <CardHeader><Skeleton class="h-6 w-32" /></CardHeader>
                            <CardContent class="space-y-4 pt-4">
                                <div v-for="i in 5" :key="i" class="flex gap-4 items-center">
                                    <Skeleton class="h-9 w-9 rounded-full" />
                                    <div class="space-y-2">
                                        <Skeleton class="h-4 w-32" />
                                        <Skeleton class="h-3 w-48" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </template>
                    <Card v-if="recent_alumni" class="col-span-3">
                        <CardHeader>
                            <CardTitle>Recent Alumni</CardTitle>
                            <CardDescription>Latest members added.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-8">
                                <div v-for="alumnus in recent_alumni" :key="alumnus.id" class="flex items-center gap-4">
                                    <Avatar class="h-9 w-9">
                                        <AvatarFallback>{{ alumnus.initials }}</AvatarFallback>
                                    </Avatar>
                                    <div class="grid gap-1">
                                        <p class="text-sm font-medium leading-none">{{ alumnus.name }}</p>
                                        <p class="text-sm text-muted-foreground">{{ alumnus.email }}</p>
                                    </div>
                                    <div class="ml-auto font-medium text-xs text-muted-foreground">{{ alumnus.tenure }}</div>
                                </div>
                                <div v-if="recent_alumni.length === 0" class="flex h-full items-center justify-center py-10">
                                    <p class="text-sm text-muted-foreground italic">No alumni found yet.</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Deferred>
            </div>

            <Deferred data="state_unit_breakdown">
                <template #fallback>
                    <Card>
                        <CardHeader><Skeleton class="h-6 w-48" /></CardHeader>
                        <CardContent><Skeleton class="h-[300px] w-full" /></CardContent>
                    </Card>
                </template>
                <Card v-if="state_unit_breakdown">
                    <CardHeader class="flex flex-row items-center justify-between">
                        <div class="grid gap-1">
                            <CardTitle class="flex items-center gap-2">
                                <MapPin class="h-5 w-5 text-muted-foreground" />
                                Detailed State & Unit Breakdown
                            </CardTitle>
                            <CardDescription>A comprehensive view of alumni distribution by state and unit.</CardDescription>
                        </div>
                        <div class="relative w-64">
                             <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                             <Input
                               v-model="searchQuery"
                               type="search"
                               placeholder="Search state or unit..."
                               class="pl-8"
                             />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border max-h-[400px] overflow-auto">
                            <Table>
                                <TableHeader class="sticky top-0 bg-background z-10 shadow-sm font-bold">
                                    <TableRow>
                                        <TableHead>State</TableHead>
                                        <TableHead>Unit</TableHead>
                                        <TableHead class="text-right">Alumni Count</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="(item, index) in filteredBreakdown" :key="index">
                                        <TableCell class="font-medium font-bold text-blue-700 dark:text-blue-400">{{ item.state }}</TableCell>
                                        <TableCell>{{ item.unit }}</TableCell>
                                        <TableCell class="text-right font-medium">{{ item.total }}</TableCell>
                                    </TableRow>
                                    <TableRow v-if="filteredBreakdown.length === 0">
                                        <TableCell colspan="3" class="text-center py-8 text-muted-foreground italic">
                                            {{ state_unit_breakdown.length === 0 ? 'No distribution data available yet.' : 'No results matching your search.' }}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </Deferred>
        </div>
    </AppLayout>
</template>
