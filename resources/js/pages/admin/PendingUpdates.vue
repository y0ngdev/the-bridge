<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import { Check, X, ArrowRight, UserCheck, Clock } from 'lucide-vue-next';
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

const props = defineProps<{
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
    
    router.post(`/admin/pending-updates/${selectedUpdate.value.id}/approve`, {}, {
        onSuccess: () => {
            showApproveDialog.value = false;
            toast.success('Update approved successfully');
            selectedUpdate.value = null;
        },
    });
}

function openRejectDialog(update: PendingUpdate) {
    selectedUpdate.value = update;
    showRejectDialog.value = true;
}

function handleReject() {
    if (!selectedUpdate.value) return;
    
    router.post(`/admin/pending-updates/${selectedUpdate.value.id}/reject`, {}, {
        onSuccess: () => {
            showRejectDialog.value = false;
            toast.success('Update rejected');
            selectedUpdate.value = null;
        },
    });
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
            <HeadingSmall 
                title="Pending Updates" 
                description="Review profile update requests submitted by alumni" 
            />

            <Alert v-if="updates.length === 0" class="mt-6">
                <Check class="h-4 w-4" />
                <AlertDescription>
                    No pending updates! All caught up.
                </AlertDescription>
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
                        <div class="border rounded-md divide-y">
                            <div 
                                v-for="(newValue, key) in update.changes" 
                                :key="key"
                                class="grid grid-cols-1 md:grid-cols-3 p-3 gap-2 text-sm"
                            >
                                <div class="font-medium text-muted-foreground">
                                    {{ getLabel(String(key)) }}
                                </div>
                                <div class="md:col-span-2 flex items-center gap-3">
                                    <div class="flex-1 bg-red-50 dark:bg-red-950/30 p-2 rounded text-red-600 dark:text-red-400 line-through decoration-red-400/50">
                                        {{ formatValue(update.alumnus[key]) }}
                                    </div>
                                    <ArrowRight class="h-4 w-4 text-muted-foreground shrink-0" />
                                    <div class="flex-1 bg-green-50 dark:bg-green-950/30 p-2 rounded text-green-700 dark:text-green-300 font-medium">
                                        {{ formatValue(newValue) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-3">
                            <Button variant="outline" class="text-destructive hover:bg-destructive/10" @click="openRejectDialog(update)">
                                <X class="h-4 w-4 mr-2" />
                                Reject
                            </Button>
                            <Button class="bg-green-600 hover:bg-green-700 text-white" @click="openApproveDialog(update)">
                                <Check class="h-4 w-4 mr-2" />
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
                    <DialogDescription>
                        Are you sure you want to reject this update request? The changes will be discarded.
                    </DialogDescription>
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
                    <DialogDescription>
                        Are you sure you want to approve and apply these changes?
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button variant="outline" @click="showApproveDialog = false">Cancel</Button>
                    <Button class="bg-green-600 hover:bg-green-700 text-white" @click="handleApprove">Confirm Approve</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
