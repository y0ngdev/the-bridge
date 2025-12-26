<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Mail, Database, RefreshCcw, Download } from 'lucide-vue-next';
import { watch } from 'vue';
import { toast } from 'vue-sonner';

interface Backup {
    name: string;
    size: string;
    date: string;
}

const props = defineProps<{
    backups: Backup[];
    backupEmail: string;
}>();

const page = usePage();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Backup settings', href: '/settings/backup' },
];

const form = useForm({
    email: props.backupEmail || '',
});

const runBackup = () => {
    form.post('/settings/backup', {
        preserveScroll: true,
    });
};

// Watch for flash messages from server
watch(
    () => page.props.flash as { success?: string; error?: string } | undefined,
    (flash) => {
        if (flash?.success) {
            toast.success(flash.success);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
    },
    { immediate: true }
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Backup settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall 
                    title="Backup settings" 
                    description="Manage database backups (sent via email every 4 months)" 
                />

                <!-- Manual Backup -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Mail class="h-5 w-5" />
                            Email Backup
                        </CardTitle>
                        <CardDescription>
                            Create a backup and send it as an email attachment
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="email">Send backup to email</Label>
                            <Input 
                                id="email" 
                                v-model="form.email" 
                                type="email" 
                                placeholder="backup@example.com"
                                :class="{ 'border-destructive': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="text-sm text-destructive">
                                {{ form.errors.email }}
                            </p>
                        </div>
                        <Button @click="runBackup" :disabled="form.processing || !form.email">
                            <RefreshCcw v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                            <Mail v-else class="mr-2 h-4 w-4" />
                            {{ form.processing ? 'Sending Backup...' : 'Create & Send Backup' }}
                        </Button>
                    </CardContent>
                </Card>

                <!-- Backup History -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Database class="h-5 w-5" />
                            Backup History
                        </CardTitle>
                        <CardDescription>
                            Recent backups stored locally (also available for download)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table v-if="backups.length > 0">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>File Name</TableHead>
                                    <TableHead>Size</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="backup in backups" :key="backup.name">
                                    <TableCell class="font-mono text-sm">{{ backup.name }}</TableCell>
                                    <TableCell>
                                        <Badge variant="secondary">{{ backup.size }}</Badge>
                                    </TableCell>
                                    <TableCell>{{ backup.date }}</TableCell>
                                    <TableCell class="text-right">
                                        <a :href="`/settings/backup/download/${backup.name}`">
                                            <Button variant="outline" size="sm">
                                                <Download class="mr-2 h-4 w-4" />
                                                Download
                                            </Button>
                                        </a>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                        <div v-else class="text-center py-8 text-muted-foreground">
                            No backups found. Create a backup to get started.
                        </div>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>

