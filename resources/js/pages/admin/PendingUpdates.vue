<script setup lang="ts">
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowRight, Check, Clock, UserCheck, X } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

interface PendingUpdate {
    id: number;
    alumnus_id: number;
    alumnus: any;
    changes: Record<string, any>;
    status: string;
    created_at: string;
    ip_address: string;
}

defineProps<{
    updates: PendingUpdate[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboardIndex().url },
    { title: 'Pending Updates', href: '#' },
];

const selectedUpdate = ref<PendingUpdate | null>(null);
const showRejectDialog = ref(false);
const showApproveDialog = ref(false);

function openApproveDialog(update: PendingUpdate) {
    selectedUpdate.value = update;
    showApproveDialog.value = true;
}

function handleApprove() {
    if (!selectedUpdate.value) return;

    router.post(
        `/admin/pending-updates/${selectedUpdate.value.id}/approve`,
        {},
        {
            onSuccess: () => {
                showApproveDialog.value = false;
                toast.success('Update approved successfully');
                selectedUpdate.value = null;
            },
        },
    );
}

function openRejectDialog(update: PendingUpdate) {
    selectedUpdate.value = update;
    showRejectDialog.value = true;
}

function handleReject() {
    if (!selectedUpdate.value) return;

    router.post(
        `/admin/pending-updates/${selectedUpdate.value.id}/reject`,
        {},
        {
            onSuccess: () => {
                showRejectDialog.value = false;
                toast.success('Update rejected');
                selectedUpdate.value = null;
            },
        },
    );
}

function formatValue(value: any): string {
    if (Array.isArray(value)) return value.join(', ');
    if (value === null || value === undefined) return '—';
    return String(value);
}

function getLabel(key: string): string {
    const labels: Record<string, string> = {
        name: 'Name',
        email: 'Email',
        phones: 'Phone Numbers',
        current_location: 'Current Location',
        current_employer: 'Current Employer',
        state: 'State',
        unit: 'Unit',
        gender: 'Gender',
        tenure_id: 'Tenure ID',
        department_id: 'Department ID',
    };
    return labels[key] || key;
}
</script>

<template>
    <Head title="Pending Updates" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <HeadingSmall title="Pending Updates" description="Review profile update requests submitted by alumni" />

            <Alert v-if="updates.length === 0" class="mt-6">
                <Check class="h-4 w-4" />
                <AlertDescription> No pending updates! All caught up. </AlertDescription>
            </Alert>

            <div v-else class="mt-6 space-y-6">
                <Card v-for="update in updates" :key="update.id">
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <UserCheck class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <CardTitle>
                                        {{ update.alumnus.name }}
                                    </CardTitle>
                                    <CardDescription>
                                        ID: {{ update.alumnus.id }} • {{ new Date(update.created_at).toLocaleString() }}
                                    </CardDescription>
                                </div>
                            </div>
                            <Badge variant="outline" class="flex items-center gap-1">
                                <Clock class="h-3 w-3" />
                                Pending
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="divide-y rounded-md border">
                            <div v-for="(newValue, key) in update.changes" :key="key" class="grid grid-cols-1 gap-2 p-3 text-sm md:grid-cols-3">
                                <div class="font-medium text-muted-foreground">
                                    {{ getLabel(String(key)) }}
                                </div>
                                <div class="flex items-center gap-3 md:col-span-2">
                                    <div
                                        class="flex-1 rounded bg-red-50 p-2 text-red-600 line-through decoration-red-400/50 dark:bg-red-950/30 dark:text-red-400"
                                    >
                                        {{ formatValue(update.alumnus[key]) }}
                                    </div>
                                    <ArrowRight class="h-4 w-4 shrink-0 text-muted-foreground" />
                                    <div class="flex-1 rounded bg-green-50 p-2 font-medium text-green-700 dark:bg-green-950/30 dark:text-green-300">
                                        {{ formatValue(newValue) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-3">
                            <Button variant="outline" class="text-destructive hover:bg-destructive/10" @click="openRejectDialog(update)">
                                <X class="mr-2 h-4 w-4" />
                                Reject
                            </Button>
                            <Button class="bg-green-600 text-white hover:bg-green-700" @click="openApproveDialog(update)">
                                <Check class="mr-2 h-4 w-4" />
                                Approve
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Reject Confirmation Dialog -->
        <Dialog v-model:open="showRejectDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Reject Update</DialogTitle>
                    <DialogDescription> Are you sure you want to reject this update request? The changes will be discarded. </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button variant="outline" @click="showRejectDialog = false">Cancel</Button>
                    <Button variant="destructive" @click="handleReject">Confirm Reject</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Approve Confirmation Dialog -->
        <Dialog v-model:open="showApproveDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Approve Update</DialogTitle>
                    <DialogDescription> Are you sure you want to approve and apply these changes? </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button variant="outline" @click="showApproveDialog = false">Cancel</Button>
                    <Button class="bg-green-600 text-white hover:bg-green-700" @click="handleApprove">Confirm Approve</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
