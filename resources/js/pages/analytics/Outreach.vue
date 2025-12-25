<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Tenure } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Phone, CheckCircle, Users } from 'lucide-vue-next';
import { index } from '@/actions/App/Http/Controllers/OutreachController';
import { home } from '@/routes';

defineProps<{
    active_session: Tenure | null;
    stats: {
        total_reached: number;
        total_logs: number;
        success_rate: number;
    };
    tenure_breakdown: Array<{
        tenure_name: string;
        tenure_year: string;
        reached: number;
        total_logs: number;
    }>;
    type_breakdown: Array<{ type: string; count: number }>;
    outcome_breakdown: Array<{ outcome: string; count: number }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: home().url },
    { title: 'Outreach Analytics', href: index().url },
];
</script>

<template>
    <Head title="Outreach Analytics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6 space-y-6">
            <div class="flex items-center justify-between">
                <HeadingSmall 
                    title="Outreach Analytics" 
                    :description="active_session ? `Statistics for ${active_session.name} (${active_session.year})` : 'No active session'" 
                />
                <Link :href="home().url">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Dashboard
                    </Button>
                </Link>
            </div>

            <!-- Summary Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Alumni Reached</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_reached }}</div>
                        <p class="text-xs text-muted-foreground">unique alumni contacted</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Total Interactions</CardTitle>
                        <Phone class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_logs }}</div>
                        <p class="text-xs text-muted-foreground">communication logs</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Success Rate</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.success_rate }}%</div>
                        <p class="text-xs text-muted-foreground">successful outcomes</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Tenure Breakdown -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Outreach by Class Set</CardTitle>
                        <CardDescription>Alumni reached grouped by their tenure/class</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table v-if="tenure_breakdown.length > 0">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Class Set</TableHead>
                                    <TableHead>Year</TableHead>
                                    <TableHead class="text-center">Reached</TableHead>
                                    <TableHead class="text-center">Interactions</TableHead>
                                    <TableHead class="w-[200px]">Progress</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="item in tenure_breakdown" :key="item.tenure_year">
                                    <TableCell class="font-medium">{{ item.tenure_name || '—' }}</TableCell>
                                    <TableCell>
                                        <Badge variant="secondary">{{ item.tenure_year }}</Badge>
                                    </TableCell>
                                    <TableCell class="text-center font-semibold">{{ item.reached }}</TableCell>
                                    <TableCell class="text-center text-muted-foreground">{{ item.total_logs }}</TableCell>
                                    <TableCell>
                                        <div class="h-2 w-full bg-muted rounded-full overflow-hidden">
                                            <div class="h-full bg-primary" :style="{ width: `${Math.min(item.reached * 2, 100)}%` }" />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                        <div v-else class="text-center py-8 text-muted-foreground">
                            No outreach data available for this session.
                        </div>
                    </CardContent>
                </Card>

                <!-- Type Breakdown -->
                <Card>
                    <CardHeader>
                        <CardTitle>By Communication Type</CardTitle>
                        <CardDescription>Breakdown of contact methods used</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="type_breakdown.length > 0" class="space-y-4">
                            <div v-for="item in type_breakdown" :key="item.type" class="flex items-center justify-between">
                                <span class="text-sm font-medium">{{ item.type }}</span>
                                <Badge>{{ item.count }}</Badge>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-muted-foreground text-sm">No data</div>
                    </CardContent>
                </Card>

                <!-- Outcome Breakdown -->
                <Card>
                    <CardHeader>
                        <CardTitle>By Outcome</CardTitle>
                        <CardDescription>Results of communication attempts</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="outcome_breakdown.length > 0" class="space-y-4">
                            <div v-for="item in outcome_breakdown" :key="item.outcome" class="flex items-center justify-between">
                                <span class="text-sm font-medium">{{ item.outcome }}</span>
                                <Badge variant="outline">{{ item.count }}</Badge>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-muted-foreground text-sm">No data</div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
