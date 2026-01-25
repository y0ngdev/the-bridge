<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { type BreadcrumbItem, type Alumnus } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import { index as Alumni Index, } from '@/actions/App/Http/Controllers/AlumnusController';
import { AlertTriangle, Users, X, Check } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    duplicateGroups: Alumnus[][];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboardIndex().url },
    { title: 'Alumni', href: alumniIndex().url },
    { title: 'Duplicates', href: '#' },
];

const showMergeDialog = ref(false);
const selectedGroup = ref<Alumnus[] | null>(null);
const selectedPrimary = ref<number | null>(null);

function openMergeDialog(group: Alumnus[]) {
    selectedGroup.value = group;
    selectedPrimary.value = group[0].id; // Default to first
    showMergeDialog.value = true;
}

function handleMerge() {
    if (!selectedGroup.value || !selectedPrimary.value) return;

    const group = selectedGroup.value;
    const primaryId = selectedPrimary.value;
    const primary = group.find(a => a.id === primaryId);
    const secondary = group.find(a => a.id !== primaryId);

    if (!primary || !secondary) return;

    router.post(`/alumni/${primary.id}/merge/${secondary.id}`, {
        primary_id: primaryId,
    }, {
        onSuccess: () => {
            showMergeDialog.value = false;
            selectedGroup.value = null;
            selectedPrimary.value = null;
        },
    });
}
</script>

<template>
    <Head title="Duplicate Alumni" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <HeadingSmall 
                title="Duplicate Detection" 
                description="Review and merge potential duplicate alumni records" 
            />

            <Alert v-if="duplicateGroups.length === 0" class="mt-6">
                <Check class="h-4 w-4" />
                <AlertDescription>
                    No duplicate records found! All alumni records appear to be unique.
                </AlertDescription>
            </Alert>

            <div v-else class="mt-6 space-y-6">
                <Alert>
                    <AlertTriangle class="h-4 w-4" />
                    <AlertDescription>
                        Found {{ duplicateGroups.length }} potential duplicate group(s). Review carefully before merging.
                    </AlertDescription>
                </Alert>

                <Card v-for="(group, index) in duplicateGroups" :key="index">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Users class="h-5 w-5" />
                            Potential Duplicates #{{ index + 1 }}
                        </CardTitle>
                        <CardDescription>{{ group.length }} records found</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div v-for="alumnus in group" :key="alumnus.id" class="p-4 border rounded-lg">
                                <h3 class="font-semibold text-lg">{{ alumnus.name }}</h3>
                                <div class="mt-2 space-y-1 text-sm">
                                    <p><span class="text-muted-foreground">Email:</span> {{ alumnus.email || '—' }}</p>
                                    <p><span class="text-muted-foreground">Phones:</span> {{ alumnus.phones?.join(', ') || '—' }}</p>
                                    <p><span class="text-muted-foreground">Department:</span> {{ alumnus.department?.name || '—' }}</p>
                                    <p><span class="text-muted-foreground">Tenure:</span> {{ alumnus.tenure?.year || '—' }}</p>
                                    <div class="mt-2">
                                        <Badge variant="outline">ID: {{ alumnus.id }}</Badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <Button @click="openMergeDialog(group)">
                                Merge Records
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Merge Dialog -->
        <Dialog v-model:open="showMergeDialog">
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Merge Duplicate Records</DialogTitle>
                    <DialogDescription>
                        Select which record to keep as the primary. Data will be combined.
                    </DialogDescription>
                </DialogHeader>

                <div v-if="selectedGroup" class="space-y-4">
                    <Alert>
                        <AlertTriangle class="h-4 w-4" />
                        <AlertDescription>
                            The merge will: combine phone numbers, transfer communication logs to the primary record, and mark the secondary as merged. This cannot be easily undone.
                        </AlertDescription>
                    </Alert>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Select Primary Record (to keep):</label>
                        <div class="grid gap-2">
                            <div 
                                v-for="alumnus in selectedGroup" 
                                :key="alumnus.id"
                                @click="selectedPrimary = alumnus.id"
                                class="p-3 border rounded cursor-pointer hover:bg-accent transition-colors"
                                :class="selectedPrimary === alumnus.id && 'border-primary bg-accent'"
                            >
                                <div class="flex items-center gap-2">
                                    <div 
                                        class="h-4 w-4 rounded-full border-2" 
                                        :class="selectedPrimary === alumnus.id ? 'border-primary bg-primary' : 'border-muted-foreground'"
                                    />
                                    <div class="flex-1">
                                        <p class="font-medium">{{ alumnus.name }}</p>
                                        <p class="text-sm text-muted-foreground">{{ alumnus.email || 'No email' }} • ID: {{ alumnus.id }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showMergeDialog = false">
                        <X class="h-4 w-4 mr-2" />
                        Cancel
                    </Button>
                    <Button @click="handleMerge" :disabled="!selectedPrimary">
                        <Check class="h-4 w-4 mr-2" />
                        Merge Records
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
