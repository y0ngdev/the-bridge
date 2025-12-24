<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { index, show } from '@/actions/App/Http/Controllers/AlumnusController';
import { ArrowLeft, Edit, Mail, Phone, MapPin, Calendar, Building, User, Briefcase, GraduationCap } from 'lucide-vue-next';

interface Tenure {
    id: number;
    year: string;
}

interface Alumnus {
    id: number;
    name: string;
    email: string | null;
    phones: string[] | null;
    department: string | null;
    gender: string | null;
    birth_date: string | null;
    tenure_id: number | null;
    unit: string | null;
    state: string | null;
    address: string | null;
    past_exco_office: string | null;
    current_exco_office: string | null;
    is_futa_staff: boolean;
    tenure?: Tenure | null;
}

const props = defineProps<{
    alumnus: Alumnus;
    departments: { value: string; label: string }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Alumni', href: index().url },
    { title: props.alumnus.name, href: show(props.alumnus.id).url },
];

function formatDate(date: string | null): string {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
    });
}
</script>

<template>
    <Head :title="alumnus.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <Link :href="index().url">
                        <Button variant="ghost" size="icon">
                            <ArrowLeft class="h-5 w-5" />
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold">{{ alumnus.name }}</h1>
                        <p class="text-muted-foreground">{{ alumnus.tenure?.year || 'No tenure' }} &bull; {{ departments.find(d => d.value === alumnus.department)?.label || alumnus.department || 'Unknown department' }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Badge v-if="alumnus.is_futa_staff" variant="secondary">FUTA Staff</Badge>
                    <Badge v-if="alumnus.gender" variant="outline">{{ alumnus.gender === 'M' ? 'Male' : alumnus.gender === 'F' ? 'Female' : alumnus.gender }}</Badge>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Contact Information -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <User class="h-5 w-5" />
                            Contact Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <Mail class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm text-muted-foreground">Email</p>
                                <p class="font-medium">{{ alumnus.email || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm text-muted-foreground">Phone(s)</p>
                                <p class="font-medium" v-if="alumnus.phones && alumnus.phones.length">
                                    {{ alumnus.phones.join(', ') }}
                                </p>
                                <p class="font-medium" v-else>—</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <MapPin class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm text-muted-foreground">Address</p>
                                <p class="font-medium">{{ alumnus.address || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <MapPin class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm text-muted-foreground">State</p>
                                <p class="font-medium">{{ alumnus.state || '—' }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Academic & Professional -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <GraduationCap class="h-5 w-5" />
                            Academic & Professional
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <Building class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm text-muted-foreground">Department</p>
                                <p class="font-medium">{{ departments.find(d => d.value === alumnus.department)?.label || alumnus.department || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm text-muted-foreground">Tenure</p>
                                <p class="font-medium">{{ alumnus.tenure?.year || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Briefcase class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm text-muted-foreground">Unit</p>
                                <p class="font-medium">{{ alumnus.unit || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Calendar class="h-5 w-5 text-muted-foreground mt-0.5" />
                            <div>
                                <p class="text-sm text-muted-foreground">Birth Date</p>
                                <p class="font-medium">{{ formatDate(alumnus.birth_date) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Exco History -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <Briefcase class="h-5 w-5" />
                            Leadership & Positions
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-sm text-muted-foreground">Past Exco Office (School)</p>
                                <p class="font-medium">{{ alumnus.past_exco_office || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Current Exco Office (Alumni)</p>
                                <p class="font-medium">{{ alumnus.current_exco_office || '—' }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
