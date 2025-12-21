<script setup lang="ts">
import { Command } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <Command class="size-4" :class="className" v-bind="$attrs" />
</template>
