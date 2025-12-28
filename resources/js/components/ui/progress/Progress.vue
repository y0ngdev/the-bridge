<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: number;
        max?: number;
        class?: string;
    }>(),
    {
        modelValue: 0,
        max: 100,
    }
);

const percentage = computed(() => {
    return Math.min(100, Math.max(0, (props.modelValue / props.max) * 100));
});
</script>

<template>
    <div
        class="relative h-2 w-full overflow-hidden rounded-full bg-primary/20"
        :class="props.class"
        role="progressbar"
        :aria-valuenow="modelValue"
        :aria-valuemin="0"
        :aria-valuemax="max"
    >
        <div
            class="h-full bg-primary transition-all duration-300 ease-in-out"
            :style="{ width: `${percentage}%` }"
        />
    </div>
</template>
