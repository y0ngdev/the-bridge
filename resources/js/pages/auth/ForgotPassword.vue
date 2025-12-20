<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { GalleryVerticalEnd } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

import { email } from '@/routes/password';

import { Spinner } from '@/components/ui/spinner';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';

defineProps<{
    status?: string;

}>();
</script>
<template>
    <Head title="Forgot password" />

    <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <a href="#" class="flex items-center gap-2 self-center font-medium">
                <div class="bg-primary text-primary-foreground flex size-6 items-center justify-center rounded-md">
                    <GalleryVerticalEnd class="size-4" />
                </div>
                The Bridge
            </a>


            <div class="flex flex-col gap-6">
                <Head title="Log in" />
                <Card>
                    <CardHeader class="text-center">
                        <CardTitle class="text-xl">
                            Forgot password
                        </CardTitle>
                        <CardDescription>
                            Enter your email to receive a password reset link
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="status"
                            class="mb-4 text-center text-sm font-medium text-green-600"
                        >
                            {{ status }}
                        </div>
                        <Form
                            class="flex flex-col gap-6"
                            v-bind="email.form()"
                            v-slot="{ errors, processing }"
                        >
                            <div class="grid gap-6">
                                <div class="grid gap-2">
                                    <Label for="email">Email address</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        autocomplete="off"
                                        autofocus
                                        placeholder="email@example.com"
                                    />
                                    <InputError :message="errors.email" />
                                </div>


                                <Button
                                    class="w-full"
                                    :disabled="processing"
                                    data-test="email-password-reset-link-button"
                                >
                                    <Spinner v-if="processing" />
                                    Email password reset link
                                </Button>
                            </div>
                        </Form>


                        <div class="space-x-2  my-4 text-center text-sm text-muted-foreground">
                            <span>Or, return to</span>
                            <TextLink :href="login()">log in</TextLink>
                        </div>

                    </CardContent>
                </Card>

            </div>


        </div>
    </div>
</template>
