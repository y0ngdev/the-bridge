<script setup lang="ts">
import type { ChartConfig } from '@/components/ui/chart';
import { ChartContainer, ChartTooltip, ChartTooltipContent } from '@/components/ui/chart';
import { VisAxis, VisStackedBar, VisXYContainer } from '@unovis/vue';

const props = defineProps<{
    data: Array<{ type: string; count: number }>;
}>();

const chartConfig = {
    desktop: {
        label: 'Count',
        // color: 'var(--chart-1)',
        theme: {
            light: 'var(--chart-1)',
            dark: 'var(--chart-2)',
        },
    },
} satisfies ChartConfig;

const x = (d: any, i: number) => i;
const y = (d: any) => d.count;
</script>

<template>
    <ChartContainer :config="chartConfig" class="h-[200px] w-full">
        <VisXYContainer :data="data" :height="200">
            <VisStackedBar :x="x" :y="y" />
            <VisAxis
                type="x"
                :num-ticks="data.length"
                :tick-format="(val: number, i: number) => props.data[i]?.type || ''"
                :grid-line="false"
                :tick-line="false"
            />
            <VisAxis type="y" :tick-format="(val: number) => Math.round(val)" :grid-line="false" :tick-line="false" />
            <ChartTooltip :content="{ template: ChartTooltipContent }" />
        </VisXYContainer>
    </ChartContainer>
</template>
