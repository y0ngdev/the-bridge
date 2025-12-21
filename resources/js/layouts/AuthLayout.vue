<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { GalleryVerticalEnd } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>
<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <a href="#" class="flex items-center gap-2 self-center font-medium">
                <div class="flex size-6 items-center justify-center rounded-md bg-primary text-primary-foreground">
                    <GalleryVerticalEnd class="size-4" />
                </div>
                The Bridge
            </a>

            <Card>
                <CardHeader class="text-center">
                    <CardTitle class="text-xl">
                        {{ title }}
                    </CardTitle>
                    <CardDescription>
                        {{ description }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <slot />
                </CardContent>
            </Card>
        </div>
    </div>
</template>
