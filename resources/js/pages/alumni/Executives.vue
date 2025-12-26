<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem, type Alumnus } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { executives, index, show } from '@/actions/App/Http/Controllers/AlumnusController';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import { Users, Mail, Phone, UserCircle, Crown, Briefcase } from 'lucide-vue-next';

const props = defineProps<{
    centralExco: Alumnus[];
    coordinators: Alumnus[];
    otherPositions: Alumnus[];
    totalCount: number;
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
        <div class="px-4 py-6 max-w-7xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <HeadingSmall 
                    title="Alumni Executives" 
                    :description="`${totalCount} alumni currently serving in executive positions`" 
                />
            </div>

            <!-- No Executives State -->
            <Card v-if="totalCount === 0" class="border-dashed">
                <CardContent class="py-16 text-center">
                    <Users class="mx-auto h-16 w-16 text-muted-foreground/30 mb-4" />
                    <h3 class="text-lg font-medium mb-2">No Executives Assigned</h3>
                    <p class="text-muted-foreground mb-4">
                        No alumni currently have executive positions assigned.
                    </p>
                    <Link :href="index().url">
                        <Button variant="outline">Go to Alumni List</Button>
                    </Link>
                </CardContent>
            </Card>

            <div v-else class="space-y-8">
                <!-- Central Exco -->
                <section v-if="centralExco.length > 0">
                    <div class="flex items-center gap-2 mb-4">
                        <Crown class="h-5 w-5 text-primary" />
                        <h2 class="text-xl font-semibold">Central Executive</h2>
                        <Badge variant="secondary">{{ centralExco.length }}</Badge>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <Link 
                            v-for="exec in centralExco" 
                            :key="exec.id" 
                            :href="show(exec.id).url"
                            class="block"
                        >
                            <Card class="hover:shadow-lg hover:border-primary/50 transition-all cursor-pointer h-full">
                                <CardContent class="p-6">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 shrink-0">
                                            <UserCircle class="h-6 w-6 text-primary" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold truncate">{{ exec.name }}</h3>
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
                <section v-if="coordinators.length > 0">
                    <div class="flex items-center gap-2 mb-4">
                        <Briefcase class="h-5 w-5 text-blue-500" />
                        <h2 class="text-xl font-semibold">Coordinators</h2>
                        <Badge variant="secondary">{{ coordinators.length }}</Badge>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <Link 
                            v-for="exec in coordinators" 
                            :key="exec.id" 
                            :href="show(exec.id).url"
                            class="block"
                        >
                            <Card class="hover:shadow-md hover:border-blue-500/50 transition-all cursor-pointer h-full">
                                <CardContent class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500/10 shrink-0">
                                            <UserCircle class="h-5 w-5 text-blue-500" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-medium truncate text-sm">{{ exec.name }}</h3>
                                            <p class="text-xs text-muted-foreground truncate">{{ exec.current_exco_office }}</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </Link>
                    </div>
                </section>

                <!-- Other Positions -->
                <section v-if="otherPositions.length > 0">
                    <div class="flex items-center gap-2 mb-4">
                        <Users class="h-5 w-5 text-muted-foreground" />
                        <h2 class="text-xl font-semibold">Other Positions</h2>
                        <Badge variant="secondary">{{ otherPositions.length }}</Badge>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <Link 
                            v-for="exec in otherPositions" 
                            :key="exec.id" 
                            :href="show(exec.id).url"
                            class="block"
                        >
                            <Card class="hover:shadow-md hover:border-muted-foreground/50 transition-all cursor-pointer h-full">
                                <CardContent class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-muted shrink-0">
                                            <UserCircle class="h-5 w-5 text-muted-foreground" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-medium truncate text-sm">{{ exec.name }}</h3>
                                            <p class="text-xs text-muted-foreground truncate">{{ exec.current_exco_office }}</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </Link>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
