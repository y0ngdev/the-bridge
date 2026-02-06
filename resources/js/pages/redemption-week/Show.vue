<script setup lang="ts">
import { index, show } from '@/actions/App/Http/Controllers/RedemptionWeekSessionController';
import { store as storeAttendance, destroy as destroyAttendance } from '@/actions/App/Http/Controllers/RedemptionWeekAttendanceController';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import type { AttendanceByDay, BreadcrumbItem, EventDay, RedemptionWeekSession } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { CalendarCheck, Crown, Search, Trash2, UserCheck, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface SimpleAlumnus {
    id: number;
    name: string;
    email: string | null;
}

const props = defineProps<{
    session: RedemptionWeekSession;
    attendanceByDay: Record<string, AttendanceByDay>;
    alumni: SimpleAlumnus[];
    stats: Record<string, { label: string; count: number }>;
    totalAlumni: number;
    totalAttendees: number;
    perfectAttendance: number;
    eventDays: EventDay[];
}>();

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.is_admin);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboardIndex().url },
    { title: 'Redemption Week', href: index().url },
    { title: props.session.name, href: show(props.session.id).url },
];

// Active Tab
const activeTab = ref(props.eventDays[0]?.value ?? 'cultural_day');

// Search filter
const searchQuery = ref('');
const filteredAlumni = computed(() => {
    if (!searchQuery.value) return props.alumni;
    const q = searchQuery.value.toLowerCase();
    return props.alumni.filter(
        (a) => a.name.toLowerCase().includes(q) || a.email?.toLowerCase().includes(q)
    );
});

// Get alumni who are marked present for active day
const presentAlumniIds = computed(() => {
    const day = props.attendanceByDay[activeTab.value];
    if (!day) return new Set<number>();
    return new Set(day.attendances.map((a) => a.alumnus_id));
});

// Selected alumni for bulk marking
const selectedAlumniIds = ref<number[]>([]);

function toggleAlumnus(id: number) {
    const idx = selectedAlumniIds.value.indexOf(id);
    if (idx === -1) {
        selectedAlumniIds.value.push(id);
    } else {
        selectedAlumniIds.value.splice(idx, 1);
    }
}

function selectAllFiltered() {
    const filtered = filteredAlumni.value.filter((a) => !presentAlumniIds.value.has(a.id));
    selectedAlumniIds.value = filtered.map((a) => a.id);
}

function clearSelection() {
    selectedAlumniIds.value = [];
}

// Clear selection when switching tabs
watch(activeTab, () => {
    selectedAlumniIds.value = [];
    searchQuery.value = '';
});

// Mark attendance
const attendanceForm = useForm({
    alumnus_ids: [] as number[],
    event_day: '',
});

function markAttendance() {
    if (selectedAlumniIds.value.length === 0) {
        toast.error('Select at least one alumnus');
        return;
    }

    attendanceForm.alumnus_ids = selectedAlumniIds.value;
    attendanceForm.event_day = activeTab.value;

    attendanceForm.post(storeAttendance(props.session.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            selectedAlumniIds.value = [];
            toast.success('Attendance marked');
        },
    });
}

// Remove attendance (admin only)
const showRemoveDialog = ref(false);
const attendanceToRemove = ref<number | null>(null);

function openRemoveDialog(attendanceId: number) {
    attendanceToRemove.value = attendanceId;
    showRemoveDialog.value = true;
}

function handleRemoveAttendance() {
    if (!attendanceToRemove.value) return;
    router.delete(destroyAttendance({ session: props.session.id, attendance: attendanceToRemove.value }).url, {
        preserveScroll: true,
        onSuccess: () => {
            showRemoveDialog.value = false;
            attendanceToRemove.value = null;
            toast.success('Attendance removed');
        },
    });
}

// Compute stats
const attendancePercentage = computed(() => {
    if (!props.totalAlumni) return 0;
    return Math.round((props.totalAttendees / props.totalAlumni) * 100);
});
</script>

<template>
    <Head :title="`${session.name} - Attendance`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <!-- Header -->
            <div class="mb-6">
                <HeadingSmall :title="session.name" :description="`${session.year} Redemption Week Attendance`" />
                <p v-if="session.description" class="mt-2 text-sm text-muted-foreground">{{ session.description }}</p>
            </div>

            <!-- Stats Cards -->
            <div class="mb-6 grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Total Alumni</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalAlumni }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Alumni Attended</CardTitle>
                        <UserCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalAttendees }}</div>
                        <Progress :model-value="attendancePercentage" class="mt-2 h-2" />
                        <p class="mt-1 text-xs text-muted-foreground">At least 1 day ({{ attendancePercentage }}%)</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Perfect Attendance</CardTitle>
                        <Crown class="h-4 w-4 text-yellow-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ perfectAttendance }}</div>
                        <p class="text-xs text-muted-foreground">All 7 days</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">Selected Event</CardTitle>
                        <CalendarCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-lg font-bold">{{ attendanceByDay[activeTab]?.label }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ attendanceByDay[activeTab]?.count ?? 0 }} present
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Tabs for each event day -->
            <Tabs v-model="activeTab" class="w-full">
                <TabsList class="mb-4 flex w-full flex-wrap justify-start gap-1">
                    <TabsTrigger v-for="day in eventDays" :key="day.value" :value="day.value" class="flex-shrink-0">
                        {{ day.label }}
                        <Badge variant="secondary" class="ml-2">
                            {{ attendanceByDay[day.value]?.count ?? 0 }}
                        </Badge>
                    </TabsTrigger>
                </TabsList>

                <TabsContent v-for="day in eventDays" :key="day.value" :value="day.value">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <!-- Mark Attendance Panel -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center justify-between">
                                    <span>Mark Attendance</span>
                                    <Badge variant="outline">{{ selectedAlumniIds.length }} selected</Badge>
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Search -->
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input
                                        v-model="searchQuery"
                                        placeholder="Search alumni..."
                                        class="pl-10"
                                    />
                                </div>

                                <!-- Select all / Clear -->
                                <div class="flex gap-2">
                                    <Button variant="outline" size="sm" @click="selectAllFiltered">
                                        Select All Unmarked
                                    </Button>
                                    <Button variant="outline" size="sm" @click="clearSelection">
                                        Clear
                                    </Button>
                                    <Button
                                        size="sm"
                                        :disabled="selectedAlumniIds.length === 0 || attendanceForm.processing"
                                        @click="markAttendance"
                                    >
                                        Mark Present
                                    </Button>
                                </div>

                                <!-- Alumni List -->
                                <ScrollArea class="h-[400px] rounded-md border">
                                    <div class="p-4">
                                        <div
                                            v-for="alumnus in filteredAlumni"
                                            :key="alumnus.id"
                                            class="flex items-center gap-3 rounded-lg p-2 hover:bg-muted/50"
                                            :class="{
                                                'bg-green-50 dark:bg-green-900/20': presentAlumniIds.has(alumnus.id),
                                                'opacity-60': presentAlumniIds.has(alumnus.id),
                                            }"
                                        >
                                            <Checkbox
                                                :id="`alumni-${alumnus.id}`"
                                                :checked="selectedAlumniIds.includes(alumnus.id)"
                                                :disabled="presentAlumniIds.has(alumnus.id)"
                                                @update:model-value="toggleAlumnus(alumnus.id)"
                                            />
                                            <Label
                                                :for="`alumni-${alumnus.id}`"
                                                class="flex flex-1 cursor-pointer items-center justify-between"
                                            >
                                                <div>
                                                    <div class="font-medium">{{ alumnus.name }}</div>
                                                    <div class="text-xs text-muted-foreground">{{ alumnus.email }}</div>
                                                </div>
                                                <Badge v-if="presentAlumniIds.has(alumnus.id)" variant="default" class="bg-green-600">
                                                    Present
                                                </Badge>
                                            </Label>
                                        </div>
                                        <div v-if="filteredAlumni.length === 0" class="py-8 text-center text-muted-foreground">
                                            No alumni found matching your search.
                                        </div>
                                    </div>
                                </ScrollArea>
                            </CardContent>
                        </Card>

                        <!-- Attendance List -->
                        <Card>
                            <CardHeader>
                                <CardTitle>{{ day.label }} - Present ({{ attendanceByDay[day.value]?.count ?? 0 }})</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <ScrollArea class="h-[500px]">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Name</TableHead>
                                                <TableHead>Marked At</TableHead>
                                                <TableHead v-if="isAdmin" class="w-12"></TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            <TableRow
                                                v-for="attendance in attendanceByDay[day.value]?.attendances ?? []"
                                                :key="attendance.id"
                                            >
                                                <TableCell class="font-medium">
                                                    {{ attendance.alumnus?.name ?? 'Unknown' }}
                                                </TableCell>
                                                <TableCell class="text-sm text-muted-foreground">
                                                    {{ new Date(attendance.created_at).toLocaleString() }}
                                                </TableCell>
                                                <TableCell v-if="isAdmin">
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        @click="openRemoveDialog(attendance.id)"
                                                    >
                                                        <Trash2 class="h-4 w-4 text-destructive" />
                                                    </Button>
                                                </TableCell>
                                            </TableRow>
                                            <TableRow v-if="(attendanceByDay[day.value]?.attendances ?? []).length === 0">
                                                <TableCell colspan="3" class="py-8 text-center text-muted-foreground">
                                                    No attendance recorded for {{ day.label }} yet.
                                                </TableCell>
                                            </TableRow>
                                        </TableBody>
                                    </Table>
                                </ScrollArea>
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>
            </Tabs>
        </div>

        <!-- Remove Attendance Confirmation Dialog -->
        <Dialog v-model:open="showRemoveDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Remove Attendance</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to remove this attendance record?
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button variant="outline" @click="showRemoveDialog = false">Cancel</Button>
                    <Button variant="destructive" @click="handleRemoveAttendance">Confirm Remove</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
