<script setup lang="ts">
import { executives, index, show } from '@/actions/App/Http/Controllers/AlumnusController';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import AppLayout from '@/layouts/AppLayout.vue';
import { type Alumnus, type BreadcrumbItem } from '@/types';
import { Deferred, Head, Link } from '@inertiajs/vue3';
import { Briefcase, Crown, Mail, Phone, UserCircle, Users } from 'lucide-vue-next';

const props = defineProps<{
    centralExco?: Alumnus[];
    coordinators?: Alumnus[];
    otherPositions?: Alumnus[];
    totalCount?: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboardIndex().url },
    { title: 'Alumni', href: index().url },
    { title: 'Executives', href: executives().url },
];
</script>

<template>
    <Head title="Alumni Executives" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl px-4 py-6">
            <div class="mb-6 flex items-center justify-between">
                <Deferred data="totalCount">
                    <template #fallback>
                        <HeadingSmall title="Alumni Executives" description="Loading executive positions..." />
                    </template>
                    <HeadingSmall title="Alumni Executives" :description="`${totalCount ?? 0} alumni currently serving in executive positions`" />
                </Deferred>
            </div>

            <!-- Loading State -->
            <Deferred data="centralExco">
                <template #fallback>
                    <div class="space-y-8">
                        <!-- Central Exco Skeleton -->
                        <section>
                            <div class="mb-4 flex items-center gap-2">
                                <Crown class="h-5 w-5 text-primary" />
                                <h2 class="text-xl font-semibold">Central Executive</h2>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <Skeleton v-for="i in 6" :key="i" class="h-32 rounded-lg" />
                            </div>
                        </section>

                        <!-- Coordinators Skeleton -->
                        <section>
                            <div class="mb-4 flex items-center gap-2">
                                <Briefcase class="h-5 w-5 text-blue-500" />
                                <h2 class="text-xl font-semibold">Coordinators</h2>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                <Skeleton v-for="i in 4" :key="i" class="h-20 rounded-lg" />
                            </div>
                        </section>
                    </div>
                </template>

                <!-- No Executives State -->
                <Card v-if="(totalCount ?? 0) === 0" class="border-dashed">
                    <CardContent class="py-16 text-center">
                        <Users class="mx-auto mb-4 h-16 w-16 text-muted-foreground/30" />
                        <h3 class="mb-2 text-lg font-medium">No Executives Assigned</h3>
                        <p class="mb-4 text-muted-foreground">No alumni currently have executive positions assigned.</p>
                        <Link :href="index().url">
                            <Button variant="outline">Go to Alumni List</Button>
                        </Link>
                    </CardContent>
                </Card>

                <div v-else class="space-y-8">
                    <!-- Central Exco -->
                    <section v-if="(centralExco ?? []).length > 0">
                        <div class="mb-4 flex items-center gap-2">
                            <Crown class="h-5 w-5 text-primary" />
                            <h2 class="text-xl font-semibold">Central Executive</h2>
                            <Badge variant="secondary">{{ (centralExco ?? []).length }}</Badge>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Link v-for="exec in centralExco" :key="exec.id" :href="show(exec.id).url" class="block">
                                <Card class="h-full cursor-pointer transition-all hover:border-primary/50 hover:shadow-lg">
                                    <CardContent class="p-6">
                                        <div class="flex items-start gap-4">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10">
                                                <UserCircle class="h-6 w-6 text-primary" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h3 class="truncate font-semibold">{{ exec.name }}</h3>
                                                <Badge variant="default" class="mt-1">{{ exec.current_exco_office }}</Badge>
                                                <div class="mt-3 space-y-1 text-sm text-muted-foreground">
                                                    <div v-if="exec.email" class="flex items-center gap-2 truncate">
                                                        <Mail class="h-4 w-4 shrink-0" />
                                                        <span class="truncate">{{ exec.email }}</span>
                                                    </div>
                                                    <div v-if="exec.phones?.length" class="flex items-center gap-2">
                                                        <Phone class="h-4 w-4 shrink-0" />
                                                        <span>{{ exec.phones[0] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </Link>
                        </div>
                    </section>

                    <!-- Coordinators -->
                    <Deferred data="coordinators">
                        <template #fallback>
                            <section>
                                <div class="mb-4 flex items-center gap-2">
                                    <Briefcase class="h-5 w-5 text-blue-500" />
                                    <h2 class="text-xl font-semibold">Coordinators</h2>
                                </div>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                    <Skeleton v-for="i in 4" :key="i" class="h-20 rounded-lg" />
                                </div>
                            </section>
                        </template>

                        <section v-if="(coordinators ?? []).length > 0">
                            <div class="mb-4 flex items-center gap-2">
                                <Briefcase class="h-5 w-5 text-blue-500" />
                                <h2 class="text-xl font-semibold">Coordinators</h2>
                                <Badge variant="secondary">{{ (coordinators ?? []).length }}</Badge>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                <Link v-for="exec in coordinators" :key="exec.id" :href="show(exec.id).url" class="block">
                                    <Card class="h-full cursor-pointer transition-all hover:border-blue-500/50 hover:shadow-md">
                                        <CardContent class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-500/10">
                                                    <UserCircle class="h-5 w-5 text-blue-500" />
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h3 class="truncate text-sm font-medium">{{ exec.name }}</h3>
                                                    <p class="truncate text-xs text-muted-foreground">{{ exec.current_exco_office }}</p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </Link>
                            </div>
                        </section>
                    </Deferred>

                    <!-- Other Positions -->
                    <Deferred data="otherPositions">
                        <template #fallback>
                            <section>
                                <div class="mb-4 flex items-center gap-2">
                                    <Users class="h-5 w-5 text-muted-foreground" />
                                    <h2 class="text-xl font-semibold">Other Positions</h2>
                                </div>
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                    <Skeleton v-for="i in 4" :key="i" class="h-20 rounded-lg" />
                                </div>
                            </section>
                        </template>

                        <section v-if="(otherPositions ?? []).length > 0">
                            <div class="mb-4 flex items-center gap-2">
                                <Users class="h-5 w-5 text-muted-foreground" />
                                <h2 class="text-xl font-semibold">Other Positions</h2>
                                <Badge variant="secondary">{{ (otherPositions ?? []).length }}</Badge>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                <Link v-for="exec in otherPositions" :key="exec.id" :href="show(exec.id).url" class="block">
                                    <Card class="h-full cursor-pointer transition-all hover:border-muted-foreground/50 hover:shadow-md">
                                        <CardContent class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-muted">
                                                    <UserCircle class="h-5 w-5 text-muted-foreground" />
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h3 class="truncate text-sm font-medium">{{ exec.name }}</h3>
                                                    <p class="truncate text-xs text-muted-foreground">{{ exec.current_exco_office }}</p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </Link>
                            </div>
                        </section>
                    </Deferred>
                </div>
            </Deferred>
        </div>
    </AppLayout>
</template>
