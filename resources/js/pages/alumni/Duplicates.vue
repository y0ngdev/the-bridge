<script setup lang="ts">
import { index as alumniIndex, destroy } from '@/actions/App/Http/Controllers/AlumnusController';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { type Alumnus, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { AlertTriangle, Check, Trash2, Users, X } from 'lucide-vue-next';
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

function dismissGroup(group: Alumnus[]) {
    const ids = group.map((a) => a.id);
    router.post('/alumni/duplicates/dismiss', { ids });
}

function handleMerge() {
    if (!selectedGroup.value || !selectedPrimary.value) return;

    const group = selectedGroup.value;
    const primaryId = selectedPrimary.value;
    const primary = group.find((a) => a.id === primaryId);
    const secondary = group.find((a) => a.id !== primaryId);

    if (!primary || !secondary) return;

    router.post(
        `/alumni/${primary.id}/merge/${secondary.id}`,
        {
            primary_id: primaryId,
        },
        {
            onSuccess: () => {
                showMergeDialog.value = false;
                selectedGroup.value = null;
                selectedPrimary.value = null;
            },
        },
    );
}

// Delete dialog state
const showDeleteDialog = ref(false);
const deletingAlumnus = ref<Alumnus | null>(null);
const deletePassword = ref('');
const deletePasswordError = ref('');

function openDeleteDialog(alumnus: Alumnus) {
    deletingAlumnus.value = alumnus;
    deletePassword.value = '';
    deletePasswordError.value = '';
    showDeleteDialog.value = true;
}

function handleDelete() {
    if (!deletingAlumnus.value) return;

    if (!deletePassword.value) {
        deletePasswordError.value = 'Please enter your password.';
        return;
    }

    deletePasswordError.value = '';

    router.delete(destroy(deletingAlumnus.value.id).url, {
        data: { password: deletePassword.value },
        onSuccess: () => {
            showDeleteDialog.value = false;
            deletingAlumnus.value = null;
            deletePassword.value = '';
        },
        onError: (errors) => {
            if (errors.password) {
                deletePasswordError.value = errors.password;
            } else {
                deletePasswordError.value = 'Failed to delete. Please try again.';
            }
        },
    });
}
</script>

<template>
    <Head title="Duplicate Alumni" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <HeadingSmall title="Duplicate Detection" description="Review and merge potential duplicate alumni records" />

            <Alert v-if="duplicateGroups.length === 0" class="mt-6">
                <Check class="h-4 w-4" />
                <AlertDescription> No duplicate records found! All alumni records appear to be unique. </AlertDescription>
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
                            <div v-for="alumnus in group" :key="alumnus.id" class="rounded-lg border p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-muted">
                                            <img
                                                v-if="alumnus.photo_url"
                                                :src="alumnus.photo_url"
                                                :alt="`${alumnus.name}'s photo`"
                                                class="h-full w-full object-cover"
                                            />
                                            <span v-else class="text-sm font-semibold text-muted-foreground">{{ alumnus.initials }}</span>
                                        </div>
                                        <h3 class="text-lg font-semibold">{{ alumnus.name }}</h3>
                                    </div>
                                    <Button variant="ghost" size="icon" class="h-8 w-8 text-destructive" @click="openDeleteDialog(alumnus)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                                <div class="mt-2 space-y-1 text-sm">
                                    <p><span class="text-muted-foreground">Email:</span> {{ alumnus.email || '—' }}</p>
                                    <p><span class="text-muted-foreground">Phones:</span> {{ alumnus.phones?.join(', ') || '—' }}</p>
                                    <p><span class="text-muted-foreground">Department:</span> {{ alumnus.department?.name || '—' }}</p>
                                    <p><span class="text-muted-foreground">Tenure:</span> {{ alumnus.tenure?.year || alumnus.tenure_id || '—' }}</p>
                                    <div class="mt-2">
                                        <Badge variant="outline">ID: {{ alumnus.id }}</Badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <Button variant="outline" @click="dismissGroup(group)">
                                <X class="mr-2 h-4 w-4" />
                                Not Duplicates
                            </Button>
                            <Button @click="openMergeDialog(group)"> Merge Records </Button>
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
                    <DialogDescription> Select which record to keep as the primary. Data will be combined. </DialogDescription>
                </DialogHeader>

                <div v-if="selectedGroup" class="space-y-4">
                    <Alert>
                        <AlertTriangle class="h-4 w-4" />
                        <AlertDescription>
                            The merge will: combine phone numbers, transfer communication logs to the primary record, and mark the secondary as
                            merged. This cannot be easily undone.
                        </AlertDescription>
                    </Alert>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Select Primary Record (to keep):</label>
                        <div class="grid gap-2">
                            <div
                                v-for="alumnus in selectedGroup"
                                :key="alumnus.id"
                                @click="selectedPrimary = alumnus.id"
                                class="cursor-pointer rounded border p-3 transition-colors hover:bg-accent"
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
                        <X class="mr-2 h-4 w-4" />
                        Cancel
                    </Button>
                    <Button @click="handleMerge" :disabled="!selectedPrimary">
                        <Check class="mr-2 h-4 w-4" />
                        Merge Records
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Alumni Record</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete <strong>{{ deletingAlumnus?.name }}</strong
                        >? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <Alert variant="destructive">
                        <AlertTriangle class="h-4 w-4" />
                        <AlertDescription> This will permanently delete this record and all associated communication logs. </AlertDescription>
                    </Alert>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Confirm with your password:</label>
                        <input
                            type="password"
                            v-model="deletePassword"
                            class="w-full rounded-md border px-3 py-2 text-sm"
                            placeholder="Enter your password"
                        />
                        <p v-if="deletePasswordError" class="text-sm text-destructive">{{ deletePasswordError }}</p>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">
                        <X class="mr-2 h-4 w-4" />
                        Cancel
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
