<script setup lang="ts">
import { index, show } from '@/actions/App/Http/Controllers/AlumnusController';
import { destroy as destroyLog } from '@/actions/App/Http/Controllers/CommunicationLogController';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import CommunicationLogForm from '@/components/CommunicationLogForm.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPhoneNumber } from '@/lib/utils';
import { type Alumnus, type BreadcrumbItem, type EnumOption } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Briefcase, Building, Calendar, Edit, GraduationCap, History, Mail, MapPin, Phone, Trash2, User } from 'lucide-vue-next';

const props = defineProps<{
    alumnus: Alumnus;
    departments: EnumOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboardIndex().url },
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
        <div class="mx-auto max-w-7xl px-4 py-6">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="index().url">
                        <Button variant="ghost" size="icon">
                            <ArrowLeft class="h-5 w-5" />
                        </Button>
                    </Link>
                    <!-- Photo Avatar with Preview -->
                    <Dialog v-if="alumnus.photo_url">
                        <DialogTrigger as-child>
                            <div
                                class="relative flex h-16 w-16 shrink-0 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-muted shadow-lg ring-2 ring-background transition-all hover:ring-primary"
                            >
                                <img :src="alumnus.photo_url" :alt="`${alumnus.name}'s photo`" class="h-full w-full object-cover" />
                            </div>
                        </DialogTrigger>
                        <DialogContent class="max-w-md overflow-hidden p-0">
                            <div class="aspect-square">
                                <img :src="alumnus.photo_url" :alt="`${alumnus.name}'s photo`" class="h-full w-full object-cover" />
                            </div>
                        </DialogContent>
                    </Dialog>
                    <!-- Fallback for no photo -->
                    <div
                        v-else
                        class="relative flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-muted shadow-lg ring-2 ring-background"
                    >
                        <span class="text-xl font-semibold text-muted-foreground">{{ alumnus.initials }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ alumnus.name }}</h1>
                        <p class="text-muted-foreground">
                            {{ alumnus.tenure?.year || 'No tenure' }} &bull; {{ alumnus.department?.name || 'Unknown department' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Badge v-if="alumnus.is_futa_staff" variant="secondary">FUTA Staff</Badge>
                    <Badge v-if="alumnus.gender" variant="outline">{{
                        alumnus.gender === 'M' ? 'Male' : alumnus.gender === 'F' ? 'Female' : alumnus.gender
                    }}</Badge>
                    <Link :href="`${index().url}?edit=${alumnus.id}`">
                        <Button variant="outline" size="sm">
                            <Edit class="mr-2 h-4 w-4" />
                            Edit
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Contact Information -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <User class="h-5 w-5" />
                            Contact Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <Mail class="mt-0.5 h-5 w-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm text-muted-foreground">Email</p>
                                <p class="font-medium">{{ alumnus.email || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Phone class="mt-0.5 h-5 w-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm text-muted-foreground">Phone(s)</p>
                                <div v-if="alumnus.phones && alumnus.phones.length" class="space-y-1">
                                    <p v-for="phone in alumnus.phones" :key="phone" class="font-medium">
                                        {{ formatPhoneNumber(phone) }}
                                    </p>
                                </div>
                                <p class="font-medium" v-else>—</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <MapPin class="mt-0.5 h-5 w-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm text-muted-foreground">Address</p>
                                <p class="font-medium">{{ alumnus.address || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <MapPin class="mt-0.5 h-5 w-5 text-muted-foreground" />
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
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <GraduationCap class="h-5 w-5" />
                            Academic & Professional
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-start gap-3">
                            <Building class="mt-0.5 h-5 w-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm text-muted-foreground">Department</p>
                                <p class="font-medium">{{ alumnus.department?.name || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Calendar class="mt-0.5 h-5 w-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm text-muted-foreground">Tenure</p>
                                <p class="font-medium">{{ alumnus.tenure?.year || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Briefcase class="mt-0.5 h-5 w-5 text-muted-foreground" />
                            <div>
                                <p class="text-sm text-muted-foreground">Unit</p>
                                <p class="font-medium">{{ alumnus.unit || '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <Calendar class="mt-0.5 h-5 w-5 text-muted-foreground" />
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
                        <CardTitle class="flex items-center gap-2 text-lg">
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

                <!-- Employment & Personal-->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-lg">
                            <Briefcase class="h-5 w-5" />
                            Employment & Personal Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <p class="text-sm text-muted-foreground">Marital Status</p>
                                <p class="font-medium">{{ alumnus.marital_status || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Occupation</p>
                                <p class="font-medium">{{ alumnus.occupation || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Current Employer</p>
                                <p class="font-medium">{{ alumnus.current_employer || '—' }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Communication Logs -->
                <div class="grid gap-6 md:col-span-2 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <CommunicationLogForm :alumnus-id="alumnus.id" />
                    </div>

                    <Card class="md:col-span-2">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-lg">
                                <History class="h-5 w-5" />
                                Communication History
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="alumnus.communication_logs && alumnus.communication_logs.length > 0" class="space-y-6">
                                <div
                                    v-for="log in alumnus.communication_logs"
                                    :key="log.id"
                                    class="relative border-l border-muted-foreground/30 pb-6 pl-6 last:border-0 last:pb-0"
                                >
                                    <div
                                        class="absolute top-0 left-0 h-3 w-3 -translate-x-1/2 rounded-full border bg-background"
                                        :class="{
                                            'border-green-500 bg-green-500': log.outcome === 'successful',
                                            'border-yellow-500 bg-yellow-500': ['pending', 'scheduled_callback'].includes(log.outcome),
                                            'border-red-500 bg-red-500': ['no_answer', 'wrong_number', 'busy'].includes(log.outcome),
                                            'border-blue-500 bg-blue-500': log.outcome === 'voicemail',
                                        }"
                                    ></div>

                                    <div class="-mt-1.5 flex flex-col gap-1">
                                        <div class="flex items-center justify-between">
                                            <p class="flex items-center gap-2 text-sm font-medium capitalize">
                                                {{ log.type }}
                                                <span class="font-normal text-muted-foreground"
                                                    >• {{ new Date(log.occurred_at).toLocaleString() }}</span
                                                >
                                            </p>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="h-6 w-6 text-destructive"
                                                @click="router.delete(destroyLog(log.id).url)"
                                            >
                                                <Trash2 class="h-3 w-3" />
                                            </Button>
                                        </div>
                                        <p
                                            class="text-xs font-semibold tracking-wider uppercase"
                                            :class="{
                                                'text-green-600': log.outcome === 'successful',
                                                'text-yellow-600': ['pending', 'scheduled_callback'].includes(log.outcome),
                                                'text-red-600': ['no_answer', 'wrong_number', 'busy'].includes(log.outcome),
                                                'text-blue-600': log.outcome === 'voicemail',
                                            }"
                                        >
                                            {{ log.outcome.replace('_', ' ') }}
                                        </p>
                                        <p v-if="log.notes" class="mt-1 text-sm text-muted-foreground">{{ log.notes }}</p>
                                        <p class="mt-1 text-xs text-muted-foreground">Logged by {{ log.user?.name || 'Unknown' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="py-8 text-center text-muted-foreground">
                                <History class="mx-auto mb-2 h-8 w-8 opacity-50" />
                                <p>No communication logs yet.</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
