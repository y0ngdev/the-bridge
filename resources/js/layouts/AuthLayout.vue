<script setup lang="ts">
import { GalleryVerticalEnd } from 'lucide-vue-next';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Form } from '@inertiajs/vue3';
import { Spinner } from '@/components/ui/spinner';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>
<template>


    <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <a href="#" class="flex items-center gap-2 self-center font-medium">
                <div class="bg-primary text-primary-foreground flex size-6 items-center justify-center rounded-md">
                    <GalleryVerticalEnd class="size-4" />
                </div>
                The Bridge
            </a>

            <Card>
                <CardHeader class="text-center">
                    <CardTitle class="text-xl">
                        {{title}}
                    </CardTitle>
                    <CardDescription>
                        {{description}}
                    </CardDescription>
                </CardHeader>
                <CardContent>

                    <slot />
                </CardContent>
            </Card>


        </div>
    </div>
</template>
