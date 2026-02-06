<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Deferred, Head, Link } from '@inertiajs/vue3';
import {
    Activity,
    ArrowRight,
    Award,
    Building,
    Cake,
    Calendar,
    Clock,
    Eye,
    GraduationCap,
    Phone,
    TrendingUp,
    UserPlus,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';

import { index as alumniIndex, birthdays, distribution, index } from '@/actions/App/Http/Controllers/AlumnusController';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import { index as analyticsOutreach } from '@/actions/App/Http/Controllers/OutreachController';

interface Stat {
    title: string;
    value?: number;
    icon: any;
    description: string;
    color?: string;
}

const props = defineProps<{
    stats?: {
        total_alumni: number;
        total_tenures: number;
        birthdays_today: number;
        new_this_month: number;
        futa_staff: number;
        with_contact: number;
    };
    gender_distribution?: {
        male: number;
        female: number;
        unspecified: number;
    };
    unit_distribution?: Array<{ unit: string; total: number }>;
    recent_alumni?: Array<{ id: number; name: string; email: string; tenure: string; initials: string }>;
    upcoming_birthdays?: Array<{ id: number; name: string; birth_date: string; days_until: number; initials: string }>;
    current_executives?: Array<{ id: number; name: string; office: string; initials: string }>;
    department_distribution?: Array<{ department: string; code: string; total: number }>;
    activity_summary?: { session: string | null; total_logs: number; alumni_reached: number };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboardIndex().url }];

const statConfigs = computed((): Stat[] => [
    {
        title: 'Total Alumni',
        value: props.stats?.total_alumni,
        icon: Users,
        description: 'Registered members',
        color: 'text-blue-600',
    },
    {
        title: 'Sessions',
        value: props.stats?.total_tenures,
        icon: Calendar,
        description: 'Academic sessions',
        color: 'text-purple-600',
    },
    {
        title: 'Birthdays Today',
        value: props.stats?.birthdays_today,
        icon: Cake,
        description: 'Celebrating today',
        color: 'text-pink-600',
    },
    {
        title: 'New This Month',
        value: props.stats?.new_this_month,
        icon: TrendingUp,
        description: 'Recent additions',
        color: 'text-green-600',
    },
]);

const extraStats = computed(() => [
    {
        title: 'FUTA Staff',
        value: props.stats?.futa_staff,
        icon: Building,
        color: 'text-amber-600',
    },
    {
        title: 'With Contact',
        value: props.stats?.with_contact,
        icon: Phone,
        color: 'text-teal-600',
        percentage: props.stats?.total_alumni ? Math.round((props.stats.with_contact / props.stats.total_alumni) * 100) : 0,
    },
]);

const totalGender = computed(
    () => (props.gender_distribution?.male ?? 0) + (props.gender_distribution?.female ?? 0) + (props.gender_distribution?.unspecified ?? 0),
);

const quickActions = [
    { title: 'Add Alumni', href: index().url, icon: UserPlus, color: 'bg-blue-500' },
    { title: 'View Birthdays', href: birthdays().url, icon: Cake, color: 'bg-pink-500' },
    { title: 'Location Distribution', href: distribution().url, icon: Eye, color: 'bg-purple-500' },
    { title: 'Outreach', href: analyticsOutreach().url, icon: Activity, color: 'bg-green-500' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <!-- Main Stats -->
            <Deferred data="stats">
                <template #fallback>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <Card v-for="i in 4" :key="i">
                            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                                <Skeleton class="h-4 w-24" />
                                <Skeleton class="h-4 w-4 rounded-full" />
                            </CardHeader>
                            <CardContent>
                                <Skeleton class="mb-1 h-8 w-16" />
                                <Skeleton class="h-3 w-32" />
                            </CardContent>
                        </Card>
                    </div>
                </template>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="stat in statConfigs" :key="stat.title">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">{{ stat.title }}</CardTitle>
                            <component :is="stat.icon" class="h-4 w-4" :class="stat.color" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ stat.value ?? 0 }}</div>
                            <p class="text-xs text-muted-foreground">{{ stat.description }}</p>
                        </CardContent>
                    </Card>
                </div>
            </Deferred>

            <!-- Quick Actions + Extra Stats -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Quick Actions -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm">Quick Actions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-2">
                            <Link v-for="action in quickActions" :key="action.title" :href="action.href">
                                <Button variant="outline" class="h-auto w-full justify-start gap-2 py-3">
                                    <div :class="[action.color, 'rounded-md p-1.5']">
                                        <component :is="action.icon" class="h-3.5 w-3.5 text-white" />
                                    </div>
                                    <span class="text-xs">{{ action.title }}</span>
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <!-- Extra Stats (FUTA Staff, Contact Info) -->
                <Deferred data="stats">
                    <template #fallback>
                        <Card>
                            <CardContent class="pt-6">
                                <Skeleton class="h-20" />
                            </CardContent>
                        </Card>
                    </template>
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-sm">Highlights</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-for="stat in extraStats" :key="stat.title" class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <component :is="stat.icon" class="h-4 w-4" :class="stat.color" />
                                    <span class="text-sm">{{ stat.title }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">{{ stat.value ?? 0 }}</span>
                                    <Badge v-if="stat.percentage" variant="secondary" class="text-xs"> {{ stat.percentage }}% </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Deferred>

                <!-- Activity Summary -->
                <Deferred data="activity_summary">
                    <template #fallback>
                        <Card>
                            <CardContent class="pt-6">
                                <Skeleton class="h-20" />
                            </CardContent>
                        </Card>
                    </template>
                    <Card v-if="activity_summary">
                        <CardHeader class="pb-2">
                            <CardTitle class="flex items-center gap-2 text-sm">
                                <Activity class="h-4 w-4 text-green-600" />
                                Session Activity
                            </CardTitle>
                            <CardDescription v-if="activity_summary.session">
                                {{ activity_summary.session }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="activity_summary.session" class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-green-600">{{ activity_summary.alumni_reached }}</div>
                                    <p class="text-xs text-muted-foreground">Alumni Reached</p>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold">{{ activity_summary.total_logs }}</div>
                                    <p class="text-xs text-muted-foreground">Total Logs</p>
                                </div>
                            </div>
                            <p v-else class="py-4 text-center text-sm text-muted-foreground">No active session set</p>
                        </CardContent>
                    </Card>
                </Deferred>
            </div>

            <!-- Middle Row: Birthdays + Gender + Executives -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Upcoming Birthdays -->
                <Deferred data="upcoming_birthdays">
                    <template #fallback>
                        <Card>
                            <CardHeader><Skeleton class="h-5 w-40" /></CardHeader>
                            <CardContent class="space-y-3">
                                <div v-for="i in 4" :key="i" class="flex items-center gap-3">
                                    <Skeleton class="h-8 w-8 rounded-full" />
                                    <div class="space-y-1.5">
                                        <Skeleton class="h-3 w-24" />
                                        <Skeleton class="h-2 w-16" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </template>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between">
                            <div>
                                <CardTitle class="flex items-center gap-2 text-sm">
                                    <Cake class="h-4 w-4 text-pink-500" />
                                    Upcoming Birthdays
                                </CardTitle>
                            </div>
                            <Link :href="birthdays().url">
                                <Button variant="ghost" size="sm" class="h-7 text-xs"> View All <ArrowRight class="ml-1 h-3 w-3" /> </Button>
                            </Link>
                        </CardHeader>
                        <CardContent>
                            <div v-if="upcoming_birthdays && upcoming_birthdays.length" class="space-y-3">
                                <div v-for="person in upcoming_birthdays" :key="person.id" class="flex items-center gap-3">
                                    <Avatar class="h-8 w-8">
                                        <AvatarFallback class="text-xs">{{ person.initials }}</AvatarFallback>
                                    </Avatar>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium">{{ person.name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ person.birth_date }}</p>
                                    </div>
                                    <Badge :variant="person.days_until === 0 ? 'default' : 'secondary'" class="shrink-0">
                                        <Clock v-if="person.days_until > 0" class="mr-1 h-3 w-3" />
                                        {{ person.days_until === 0 ? 'Today!' : `${person.days_until}d` }}
                                    </Badge>
                                </div>
                            </div>
                            <p v-else class="py-6 text-center text-sm text-muted-foreground">No upcoming birthdays in the next 2 weeks</p>
                        </CardContent>
                    </Card>
                </Deferred>

                <!-- Gender Distribution -->
                <Deferred data="gender_distribution">
                    <template #fallback>
                        <Card>
                            <CardHeader><Skeleton class="h-5 w-40" /></CardHeader>
                            <CardContent><Skeleton class="h-24" /></CardContent>
                        </Card>
                    </template>
                    <Card v-if="gender_distribution && stats">
                        <CardHeader>
                            <CardTitle class="text-sm">Gender Distribution</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                                <span class="flex-1 text-sm">Male</span>
                                <span class="font-semibold">{{ gender_distribution.male }}</span>
                                <span class="w-10 text-right text-xs text-muted-foreground">
                                    {{ totalGender ? Math.round((gender_distribution.male / totalGender) * 100) : 0 }}%
                                </span>
                            </div>
                            <Progress
                                :model-value="totalGender ? (gender_distribution.male / totalGender) * 100 : 0"
                                class="h-2 bg-muted [&>div]:bg-blue-500"
                            />

                            <div class="flex items-center gap-3">
                                <div class="h-3 w-3 rounded-full bg-pink-500"></div>
                                <span class="flex-1 text-sm">Female</span>
                                <span class="font-semibold">{{ gender_distribution.female }}</span>
                                <span class="w-10 text-right text-xs text-muted-foreground">
                                    {{ totalGender ? Math.round((gender_distribution.female / totalGender) * 100) : 0 }}%
                                </span>
                            </div>
                            <Progress
                                :model-value="totalGender ? (gender_distribution.female / totalGender) * 100 : 0"
                                class="h-2 bg-muted [&>div]:bg-pink-500"
                            />
                        </CardContent>
                    </Card>
                </Deferred>

                <!-- Current Executives -->
                <Deferred data="current_executives">
                    <template #fallback>
                        <Card>
                            <CardHeader><Skeleton class="h-5 w-40" /></CardHeader>
                            <CardContent class="space-y-3">
                                <div v-for="i in 4" :key="i" class="flex items-center gap-3">
                                    <Skeleton class="h-8 w-8 rounded-full" />
                                    <div class="space-y-1.5">
                                        <Skeleton class="h-3 w-24" />
                                        <Skeleton class="h-2 w-16" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </template>
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between">
                            <CardTitle class="flex items-center gap-2 text-sm">
                                <Award class="h-4 w-4 text-amber-500" />
                                Current Executives
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="current_executives && current_executives.length" class="space-y-3">
                                <div v-for="exec in current_executives" :key="exec.id" class="flex items-center gap-3">
                                    <Avatar class="h-8 w-8">
                                        <AvatarFallback class="bg-amber-100 text-xs dark:bg-amber-900">{{ exec.initials }}</AvatarFallback>
                                    </Avatar>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium">{{ exec.name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ exec.office }}</p>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="py-6 text-center text-sm text-muted-foreground">No current executives set</p>
                        </CardContent>
                    </Card>
                </Deferred>
            </div>

            <!-- Bottom Row: Departments + Units + Recent Alumni -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Top Departments -->
                <Deferred data="department_distribution">
                    <template #fallback>
                        <Card>
                            <CardHeader><Skeleton class="h-5 w-40" /></CardHeader>
                            <CardContent><Skeleton class="h-32" /></CardContent>
                        </Card>
                    </template>
                    <Card v-if="department_distribution && stats">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-sm">
                                <GraduationCap class="h-4 w-4 text-purple-500" />
                                Top Departments
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-for="dept in department_distribution" :key="dept.department" class="flex items-center gap-3">
                                <Badge variant="outline" class="w-14 justify-center text-[10px]">
                                    {{ dept.code }}
                                </Badge>
                                <div class="min-w-0 flex-1">
                                    <p class="mb-1 truncate text-xs">{{ dept.department }}</p>
                                    <div class="h-2 overflow-hidden rounded-full bg-muted">
                                        <div class="h-full bg-purple-500" :style="{ width: `${(dept.total / stats.total_alumni) * 100}%` }"></div>
                                    </div>
                                </div>
                                <span class="w-8 text-right text-sm font-medium">{{ dept.total }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </Deferred>

                <!-- Top Units -->
                <Deferred data="unit_distribution">
                    <template #fallback>
                        <Card>
                            <CardHeader><Skeleton class="h-5 w-32" /></CardHeader>
                            <CardContent><Skeleton class="h-32" /></CardContent>
                        </Card>
                    </template>
                    <Card v-if="unit_distribution && stats">
                        <CardHeader>
                            <CardTitle class="text-sm">Top Units</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-for="item in unit_distribution" :key="item.unit" class="flex items-center gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-muted text-[8px] font-bold">
                                    {{
                                        item.unit
                                            .split(' ')
                                            .map((w) => w[0])
                                            .join('')
                                            .substring(0, 2)
                                    }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs">{{ item.unit }}</p>
                                    <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-muted">
                                        <div class="h-full bg-primary" :style="{ width: `${(item.total / stats.total_alumni) * 100}%` }"></div>
                                    </div>
                                </div>
                                <span class="text-xs font-medium">{{ item.total }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </Deferred>

                <!-- Recent Alumni -->
                <Deferred data="recent_alumni">
                    <template #fallback>
                        <Card>
                            <CardHeader><Skeleton class="h-5 w-32" /></CardHeader>
                            <CardContent class="space-y-3">
                                <div v-for="i in 5" :key="i" class="flex items-center gap-3">
                                    <Skeleton class="h-8 w-8 rounded-full" />
                                    <div class="space-y-1.5">
                                        <Skeleton class="h-3 w-24" />
                                        <Skeleton class="h-2 w-32" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </template>
                    <Card v-if="recent_alumni">
                        <CardHeader class="flex flex-row items-center justify-between">
                            <CardTitle class="text-sm">Recent Alumni</CardTitle>
                            <Link :href="alumniIndex().url">
                                <Button variant="ghost" size="sm" class="h-7 text-xs"> View All <ArrowRight class="ml-1 h-3 w-3" /> </Button>
                            </Link>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div v-for="alumnus in recent_alumni" :key="alumnus.id" class="flex items-center gap-3">
                                    <Avatar class="h-8 w-8">
                                        <AvatarFallback class="text-xs">{{ alumnus.initials }}</AvatarFallback>
                                    </Avatar>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium">{{ alumnus.name }}</p>
                                        <p class="truncate text-xs text-muted-foreground">{{ alumnus.email || 'No email' }}</p>
                                    </div>
                                    <Badge variant="outline" class="shrink-0 text-xs">{{ alumnus.tenure }}</Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Deferred>
            </div>
        </div>
    </AppLayout>
</template>
