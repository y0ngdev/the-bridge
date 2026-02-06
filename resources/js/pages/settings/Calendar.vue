<script setup lang="ts">
import { index, sync, unsync } from '@/actions/App/Http/Controllers/Settings/CalendarController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { AlertTriangle, Calendar, CheckCircle, Clock, RefreshCcw, Trash2, Users, XCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface SyncStatus {
    status: 'running' | 'complete' | 'failed';
    message: string;
    synced?: number;
    failed?: number;
    total?: number;
    completed_at?: string;
    started_at?: string;
    failed_at?: string;
}

interface UnsyncStatus {
    status: 'running' | 'complete' | 'failed';
    message: string;
    deleted?: number;
    failed?: number;
    completed_at?: string;
    started_at?: string;
    failed_at?: string;
}

const props = defineProps<{
    isConfigured: boolean;
    alumniCount: number;
    calendarId: string;
    syncStatus: SyncStatus | null;
    unsyncStatus: UnsyncStatus | null;
}>();

const page = usePage();

const breadcrumbItems: BreadcrumbItem[] = [{ title: 'Calendar settings', href: index().url }];

const form = useForm({});
const unsyncForm = useForm({});
const showUnsyncDialog = ref(false);

const syncCalendar = () => {
    form.post(sync().url, {
        preserveScroll: true,
    });
};

const openUnsyncDialog = () => {
    showUnsyncDialog.value = true;
};

const handleUnsync = () => {
    showUnsyncDialog.value = false;
    unsyncForm.post(unsync().url, {
        preserveScroll: true,
    });
};

// Watch for flash messages from server
watch(
    () => page.props.flash as { success?: string; error?: string; warning?: string } | undefined,
    (flash) => {
        if (flash?.success) {
            toast.success(flash.success);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
        if (flash?.warning) {
            toast.warning(flash.warning);
        }
    },
    { immediate: true },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Calendar settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Google Calendar" description="Sync alumni birthdays to Google Calendar for reminders" />

                <!-- Configuration Status -->
                <Alert :variant="isConfigured ? 'default' : 'destructive'">
                    <CheckCircle v-if="isConfigured" class="h-4 w-4" />
                    <AlertTriangle v-else class="h-4 w-4" />
                    <AlertTitle>{{ isConfigured ? 'Connected' : 'Not Configured' }}</AlertTitle>
                    <AlertDescription v-if="isConfigured">
                        Google Calendar is connected. Calendar ID: <code class="rounded bg-muted px-1 text-xs">{{ calendarId }}</code>
                    </AlertDescription>
                    <AlertDescription v-else>
                        <p class="mb-2">To connect Google Calendar, you need to:</p>
                        <ol class="list-inside list-decimal space-y-1 text-sm">
                            <li>Create a Google Cloud project and enable Calendar API</li>
                            <li>Create a Service Account and download JSON credentials</li>
                            <li>
                                Place credentials at
                                <code class="rounded bg-muted px-1 text-xs">storage/app/google-calendar/service-account-credentials.json</code>
                            </li>
                            <li>Add <code class="rounded bg-muted px-1 text-xs">GOOGLE_CALENDAR_ID=your_calendar_id</code> to .env</li>
                            <li>Share your Google Calendar with the service account email</li>
                        </ol>
                    </AlertDescription>
                </Alert>

                <!-- Sync Status -->
                <Alert v-if="syncStatus" :variant="syncStatus.status === 'failed' ? 'destructive' : 'default'">
                    <RefreshCcw v-if="syncStatus.status === 'running'" class="h-4 w-4 animate-spin" />
                    <CheckCircle v-else-if="syncStatus.status === 'complete'" class="h-4 w-4 text-green-600" />
                    <XCircle v-else class="h-4 w-4" />
                    <AlertTitle>
                        {{
                            syncStatus.status === 'running'
                                ? 'Sync In Progress'
                                : syncStatus.status === 'complete'
                                  ? 'Last Sync Successful'
                                  : 'Last Sync Failed'
                        }}
                    </AlertTitle>
                    <AlertDescription>
                        <p>{{ syncStatus.message }}</p>
                        <p v-if="syncStatus.completed_at" class="mt-1 text-xs text-muted-foreground">Completed: {{ syncStatus.completed_at }}</p>
                        <p v-if="syncStatus.started_at && syncStatus.status === 'running'" class="mt-1 text-xs text-muted-foreground">
                            Started: {{ syncStatus.started_at }}
                        </p>
                    </AlertDescription>
                </Alert>

                <!-- Unsync Status -->
                <Alert v-if="unsyncStatus" :variant="unsyncStatus.status === 'failed' ? 'destructive' : 'default'">
                    <RefreshCcw v-if="unsyncStatus.status === 'running'" class="h-4 w-4 animate-spin" />
                    <CheckCircle v-else-if="unsyncStatus.status === 'complete'" class="h-4 w-4 text-green-600" />
                    <XCircle v-else class="h-4 w-4" />
                    <AlertTitle>
                        {{
                            unsyncStatus.status === 'running'
                                ? 'Removing Events...'
                                : unsyncStatus.status === 'complete'
                                  ? 'Events Removed'
                                  : 'Removal Failed'
                        }}
                    </AlertTitle>
                    <AlertDescription>
                        <p>{{ unsyncStatus.message }}</p>
                        <p v-if="unsyncStatus.completed_at" class="mt-1 text-xs text-muted-foreground">Completed: {{ unsyncStatus.completed_at }}</p>
                    </AlertDescription>
                </Alert>

                <!-- Sync Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5" />
                            Birthday Sync
                        </CardTitle>
                        <CardDescription> Sync alumni birthdays to your Google Calendar as events </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-muted-foreground">
                                <Users class="h-4 w-4" />
                                <span>{{ alumniCount }} alumni with birth dates</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4">
                            <Button
                                @click="syncCalendar"
                                :disabled="form.processing || !isConfigured || syncStatus?.status === 'running' || unsyncStatus?.status === 'running'"
                            >
                                <RefreshCcw v-if="form.processing || syncStatus?.status === 'running'" class="mr-2 h-4 w-4 animate-spin" />
                                <Calendar v-else class="mr-2 h-4 w-4" />
                                {{ form.processing || syncStatus?.status === 'running' ? 'Syncing...' : 'Sync Birthdays to Calendar' }}
                            </Button>

                            <Button
                                variant="outline"
                                class="text-destructive hover:text-destructive"
                                @click="openUnsyncDialog"
                                :disabled="
                                    unsyncForm.processing || !isConfigured || syncStatus?.status === 'running' || unsyncStatus?.status === 'running'
                                "
                            >
                                <RefreshCcw v-if="unsyncForm.processing || unsyncStatus?.status === 'running'" class="mr-2 h-4 w-4 animate-spin" />
                                <Trash2 v-else class="mr-2 h-4 w-4" />
                                {{ unsyncForm.processing || unsyncStatus?.status === 'running' ? 'Removing...' : 'Remove All Events' }}
                            </Button>

                            <Badge v-if="!isConfigured" variant="outline"> Configure first </Badge>
                            <Badge v-else-if="syncStatus?.status === 'running'" variant="secondary">
                                <Clock class="mr-1 h-3 w-3" />
                                Sync in progress
                            </Badge>
                            <Badge v-else-if="unsyncStatus?.status === 'running'" variant="secondary">
                                <Clock class="mr-1 h-3 w-3" />
                                Removal in progress
                            </Badge>
                        </div>

                        <p class="text-sm text-muted-foreground">
                            This will create calendar events for each alumnus's birthday. The sync runs automatically every Monday at 9 AM.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>

        <!-- Unsync Confirmation Dialog -->
        <Dialog v-model:open="showUnsyncDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Remove All Events</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to remove ALL birthday events from Google Calendar? This cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button variant="outline" @click="showUnsyncDialog = false">Cancel</Button>
                    <Button variant="destructive" @click="handleUnsync">Confirm Remove</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
