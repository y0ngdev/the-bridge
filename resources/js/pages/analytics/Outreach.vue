<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartContainer, ChartTooltip, ChartTooltipContent, componentToString } from '@/components/ui/chart';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Tenure } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Phone, CheckCircle, Users, TrendingUp } from 'lucide-vue-next';
import { index } from '@/actions/App/Http/Controllers/OutreachController';
import { home } from '@/routes';
import { VisAxis, VisStackedBar, VisXYContainer, VisDonut, VisSingleContainer } from '@unovis/vue';
import { Donut } from '@unovis/ts';
import { computed } from 'vue';

const props = defineProps<{
    active_session: Tenure | null;
    stats: {
        total_reached: number;
        total_logs: number;
        success_rate: number;
        total_alumni: number;
        response_rate: number;
    };
    tenure_breakdown: Array<{
        tenure_name: string;
        tenure_year: string;
        reached: number;
        total_logs: number;
    }>;
    type_breakdown: Array<{ type: string; count: number }>;
    outcome_breakdown: Array<{ outcome: string; count: number }>;
    gender_breakdown: Array<{ gender: string; count: number; fill?: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: home().url },
    { title: 'Outreach Analytics', href: index().url },
];

// Chart accessors
const xIndex = (_: any, i: number) => i;
const yCount = (d: any) => d.count;

const chartConfig = {
    count: { label: 'Count', theme: { light: 'var(--chart-1)', dark: 'var(--chart-2)' } },
};

// Gender pie chart config - dynamic based on data
const genderChartConfig = computed<ChartConfig>(() => {
    const config: ChartConfig = {
        count: { label: 'Count' },
    };
    props.gender_breakdown.forEach((item, i) => {
        config[item.gender.toLowerCase()] = {
            label: item.gender,
            color: `var(--chart-${i + 1})`,
        };
    });
    return config;
});

// Add fill property to gender data for coloring
const genderDataWithFill = computed(() =>
    props.gender_breakdown.map((item, i) => ({
        ...item,
        fill: `var(--color-${item.gender.toLowerCase()})`,
    }))
);

const totalGenderCount = computed(() => props.gender_breakdown.reduce((sum, g) => sum + g.count, 0));
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
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Alumni Reached</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_reached }}</div>
                        <p class="text-xs text-muted-foreground">of {{ stats.total_alumni }} total</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Response Rate</CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.response_rate }}%</div>
                        <p class="text-xs text-muted-foreground">alumni contacted</p>
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

                <!-- Type Breakdown Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>By Communication Type</CardTitle>
                        <CardDescription>Breakdown of contact methods used</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ChartContainer v-if="type_breakdown.length > 0" :config="chartConfig" class="h-[200px] w-full">
                            <VisXYContainer :data="type_breakdown" :height="180">
                                <VisStackedBar :x="xIndex" :y="yCount" :rounded-corners="10" />
                                <VisAxis
                                    type="x"
                                    :tick-values="type_breakdown.map((_: any, i: number) => i)"
                                    :tick-format="(v: number) => props.type_breakdown[v]?.type || ''"
                                    :grid-line="false"
                                    :tick-line="false"
                                />
                                <VisAxis type="y" :tick-format="(v: number) => Math.round(v)" :grid-line="false" :tick-line="false" />
                            </VisXYContainer>
                        </ChartContainer>
                        <div v-else class="text-center py-8 text-muted-foreground text-sm">No data</div>
                    </CardContent>
                </Card>

                <!-- Outcome Breakdown Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>By Outcome</CardTitle>
                        <CardDescription>Results of communication attempts</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ChartContainer v-if="outcome_breakdown.length > 0" :config="chartConfig" class="h-[200px] w-full">
                            <VisXYContainer :data="outcome_breakdown" :height="180">
                                <VisStackedBar :x="xIndex" :y="yCount" :rounded-corners="10"/>
                                <VisAxis
                                    type="x"
                                    :tick-values="outcome_breakdown.map((_: any, i: number) => i)"
                                    :tick-format="(v: number) => props.outcome_breakdown[v]?.outcome || ''"
                                    :grid-line="false"
                                    :tick-line="false"
                                />
                                <VisAxis type="y" :tick-format="(v: number) => Math.round(v)" :grid-line="false" :tick-line="false" />
                            </VisXYContainer>
                        </ChartContainer>
                        <div v-else class="text-center py-8 text-muted-foreground text-sm">No data</div>
                    </CardContent>
                </Card>

                <!-- Gender Breakdown Pie Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>By Gender</CardTitle>
                        <CardDescription>Reached alumni by gender</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ChartContainer 
                            v-if="gender_breakdown.length > 0" 
                            :config="genderChartConfig" 
                            class="mx-auto aspect-square max-h-[200px]"
                            :style="{
                                '--vis-donut-central-label-font-size': 'var(--text-2xl)',
                                '--vis-donut-central-label-font-weight': 'var(--font-weight-bold)',
                                '--vis-donut-central-label-text-color': 'var(--foreground)',
                                '--vis-donut-central-sub-label-text-color': 'var(--muted-foreground)',
                            }"
                        >
                            <VisSingleContainer :data="genderDataWithFill" :margin="{ top: 20, bottom: 20 }">
                                <VisDonut 
                                    :value="(d: any) => d.count" 
                                    :color="(d: any) => d.fill"
                                    :arc-width="30"
                                    :pad-angle="0.02"
                                    :central-label="totalGenderCount.toLocaleString()"
                                    central-sub-label="Reached"
                                />
                                <ChartTooltip
                                    :triggers="{
                                        [Donut.selectors.segment]: componentToString(genderChartConfig, ChartTooltipContent, { hideLabel: true })!,
                                    }"
                                />
                            </VisSingleContainer>
                        </ChartContainer>
                        <div v-if="gender_breakdown.length > 0" class="flex justify-center gap-4 mt-4 text-sm">
                            <div v-for="(item, i) in gender_breakdown" :key="item.gender" class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: `var(--chart-${i + 1})` }"></div>
                                <span>{{ item.gender }}: {{ item.count }}</span>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-muted-foreground text-sm">No data</div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
