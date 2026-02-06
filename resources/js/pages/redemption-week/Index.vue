<script setup lang="ts">
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import { destroy, index, show as showSession, store, update } from '@/actions/App/Http/Controllers/RedemptionWeekSessionController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Calendar as CalendarComponent } from '@/components/ui/calendar';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Progress } from '@/components/ui/progress';
import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import type { BreadcrumbItem, EventDay, RedemptionWeekSession, SimplePaginatedResponse } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { CalendarDate } from '@internationalized/date';
import { Calendar, CalendarIcon, Edit, Eye, Plus, Users } from 'lucide-vue-next';
import type { DateValue } from 'reka-ui';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

type PaginatedSessions = SimplePaginatedResponse<RedemptionWeekSession>;

defineProps<{
    sessions: PaginatedSessions;
    totalAlumni: number;
    eventDays: EventDay[];
}>();

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.is_admin);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboardIndex().url },
    { title: 'Redemption Week', href: index().url },
];

// Add Session Dialog
const showAddDialog = ref(false);
const addForm = useForm({
    name: '',
    year: new Date().getFullYear(),
    start_date: '',
    end_date: '',
    description: '',
    is_active: false,
});

// Date picker refs for add form
const addStartDate = ref<DateValue>();
const addEndDate = ref<DateValue>();

function formatDateForApi(dateValue: DateValue | undefined): string {
    if (!dateValue) return '';
    return `${dateValue.year}-${String(dateValue.month).padStart(2, '0')}-${String(dateValue.day).padStart(2, '0')}`;
}

function formatDateForDisplay(dateValue: DateValue | undefined): string {
    if (!dateValue) return 'Pick a date';
    return new Date(dateValue.year, dateValue.month - 1, dateValue.day).toLocaleDateString();
}

function parseStringToDateValue(dateStr: string | null | undefined): DateValue | undefined {
    if (!dateStr) return undefined;
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return undefined;
    return new CalendarDate(date.getFullYear(), date.getMonth() + 1, date.getDate());
}

function handleAddSubmit() {
    addForm.start_date = formatDateForApi(addStartDate.value);
    addForm.end_date = formatDateForApi(addEndDate.value);

    addForm.post(store().url, {
        onSuccess: () => {
            showAddDialog.value = false;
            addForm.reset();
            addStartDate.value = undefined;
            addEndDate.value = undefined;
            toast.success('Session created');
        },
    });
}

// Edit Session Dialog
const showEditDialog = ref(false);
const editingSession = ref<RedemptionWeekSession | null>(null);
const editForm = useForm({
    name: '',
    year: new Date().getFullYear(),
    start_date: '',
    end_date: '',
    description: '',
    is_active: true,
});

// Date picker refs for edit form
const editStartDate = ref<DateValue>();
const editEndDate = ref<DateValue>();

function openEditDialog(session: RedemptionWeekSession) {
    editingSession.value = session;
    editForm.name = session.name;
    editForm.year = session.year;
    editForm.start_date = session.start_date ?? '';
    editForm.end_date = session.end_date ?? '';
    editForm.description = session.description ?? '';
    editForm.is_active = Boolean(session.is_active);
    editStartDate.value = parseStringToDateValue(session.start_date);
    editEndDate.value = parseStringToDateValue(session.end_date);
    showEditDialog.value = true;
}

function handleEditSubmit() {
    if (!editingSession.value) return;

    editForm
        .transform((data) => ({
            ...data,
            start_date: formatDateForApi(editStartDate.value),
            end_date: formatDateForApi(editEndDate.value),
            is_active: Boolean(data.is_active),
        }))
        .put(update(editingSession.value.id).url, {
            onSuccess: () => {
                showEditDialog.value = false;
                editForm.reset();
                editStartDate.value = undefined;
                editEndDate.value = undefined;
                toast.success('Session updated');
            },
        });
}

// Delete Session
const showDeleteDialog = ref(false);
const sessionToDelete = ref<RedemptionWeekSession | null>(null);

function handleDelete() {
    if (!sessionToDelete.value) return;
    router.delete(destroy(sessionToDelete.value.id).url, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            sessionToDelete.value = null;
            toast.success('Session deleted');
        },
    });
}


// Stats helpers
function getTotalAttendance(session: RedemptionWeekSession): number {
    return session.attendances_count ?? 0;
}
</script>

<template>
    <Head title="Redemption Week" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="mb-6 flex items-center justify-between">
                <HeadingSmall title="Redemption Week" description="Manage RW sessions and track attendance" />

                <Dialog v-if="isAdmin" v-model:open="showAddDialog">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            New Session
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="max-w-lg">
                        <DialogHeader>
                            <DialogTitle>Create RW Session</DialogTitle>
                            <DialogDescription>Add a new Redemption Week session</DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="handleAddSubmit" class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="name">Session Name</Label>
                                    <Input
                                        id="name"
                                        v-model="addForm.name"
                                        placeholder="e.g., RW'25"
                                        :class="addForm.errors.name && 'border-destructive'"
                                        required
                                    />
                                    <InputError :message="addForm.errors.name" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="year">Year</Label>
                                    <Input id="year" v-model.number="addForm.year" type="number" min="2000" max="2100" required />
                                    <InputError :message="addForm.errors.year" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label>Start Date</Label>
                                    <Popover>
                                        <PopoverTrigger as-child>
                                            <Button
                                                variant="outline"
                                                :class="cn('w-full justify-start text-left font-normal', !addStartDate && 'text-muted-foreground')"
                                            >
                                                <CalendarIcon class="mr-2 h-4 w-4" />
                                                {{ formatDateForDisplay(addStartDate) }}
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent class="w-auto p-0">
                                            <CalendarComponent v-model="addStartDate" :initial-focus="true" />
                                        </PopoverContent>
                                    </Popover>
                                    <InputError :message="addForm.errors.start_date" />
                                </div>
                                <div class="space-y-2">
                                    <Label>End Date</Label>
                                    <Popover>
                                        <PopoverTrigger as-child>
                                            <Button
                                                variant="outline"
                                                :class="cn('w-full justify-start text-left font-normal', !addEndDate && 'text-muted-foreground')"
                                            >
                                                <CalendarIcon class="mr-2 h-4 w-4" />
                                                {{ formatDateForDisplay(addEndDate) }}
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent class="w-auto p-0">
                                            <CalendarComponent v-model="addEndDate" :initial-focus="true" />
                                        </PopoverContent>
                                    </Popover>
                                    <InputError :message="addForm.errors.end_date" />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox id="is_active" v-model="addForm.is_active" />
                                <Label for="is_active" class="cursor-pointer">Mark as Active Session</Label>
                            </div>
                            <DialogFooter>
                                <DialogClose as-child>
                                    <Button variant="outline" type="button">Cancel</Button>
                                </DialogClose>
                                <Button type="submit" :disabled="addForm.processing">
                                    {{ addForm.processing ? 'Creating...' : 'Create Session' }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Sessions Grid -->
            <div v-if="sessions.data.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="session in sessions.data" :key="session.id" class="relative overflow-hidden">
                    <Badge v-if="session.is_active" class="absolute top-4 right-4" variant="default"> Active </Badge>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar class="h-5 w-5 text-muted-foreground" />
                            {{ session.name }}
                        </CardTitle>
                        <CardDescription>
                            {{ session.year }}
                            <span v-if="session.start_date && session.end_date">
                                • {{ new Date(session.start_date).toLocaleDateString() }} -
                                {{ new Date(session.end_date).toLocaleDateString() }}
                            </span>
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Attendance Stats -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-1 text-muted-foreground">
                                    <Users class="h-4 w-4" />
                                    Alumni Attended
                                </span>
                                <span class="font-medium">{{ session.unique_attendees ?? 0 }} / {{ totalAlumni }}</span>
                            </div>
                            <Progress :model-value="((session.unique_attendees ?? 0) / totalAlumni) * 100" class="h-2" />
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Total Attendances</span>
                            <span class="font-medium">{{ getTotalAttendance(session) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Perfect Attendance</span>
                            <Badge variant="secondary">{{ session.perfect_count ?? 0 }}</Badge>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-2">
                            <Button as-child variant="default" size="sm" class="flex-1">
                                <Link :href="showSession(session.id).url">
                                    <Eye class="mr-1 h-4 w-4" />
                                    View
                                </Link>
                            </Button>
                            <Button v-if="isAdmin" variant="outline" size="sm" @click="openEditDialog(session)">
                                <Edit class="h-4 w-4" />
                            </Button>
                            <!-- <Button v-if="isAdmin" variant="outline" size="sm" @click="handleDelete(session)">
                                <Trash2 class="h-4 w-4 text-destructive" />
                            </Button> -->
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center rounded-lg border border-dashed p-12">
                <Calendar class="h-12 w-12 text-muted-foreground" />
                <h3 class="mt-4 text-lg font-semibold">No Sessions Yet</h3>
                <p class="mt-2 text-sm text-muted-foreground">Create your first Redemption Week session to get started.</p>
                <Button v-if="isAdmin" class="mt-4" @click="showAddDialog = true">
                    <Plus class="mr-2 h-4 w-4" />
                    Create Session
                </Button>
            </div>

            <!-- Edit Dialog -->
            <Dialog v-model:open="showEditDialog">
                <DialogContent class="max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Edit Session</DialogTitle>
                        <DialogDescription>Update session details</DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="handleEditSubmit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="edit_name">Session Name</Label>
                                <Input id="edit_name" v-model="editForm.name" required />
                                <InputError :message="editForm.errors.name" />
                            </div>
                            <div class="space-y-2">
                                <Label for="edit_year">Year</Label>
                                <Input id="edit_year" v-model.number="editForm.year" type="number" required />
                                <InputError :message="editForm.errors.year" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Start Date</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn('w-full justify-start text-left font-normal', !editStartDate && 'text-muted-foreground')"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ formatDateForDisplay(editStartDate) }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <CalendarComponent v-model="editStartDate" :initial-focus="true" />
                                    </PopoverContent>
                                </Popover>
                            </div>
                            <div class="space-y-2">
                                <Label>End Date</Label>
                                <Popover>
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            :class="cn('w-full justify-start text-left font-normal', !editEndDate && 'text-muted-foreground')"
                                        >
                                            <CalendarIcon class="mr-2 h-4 w-4" />
                                            {{ formatDateForDisplay(editEndDate) }}
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-auto p-0">
                                        <CalendarComponent v-model="editEndDate" :initial-focus="true" />
                                    </PopoverContent>
                                </Popover>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Checkbox id="edit_active" v-model="editForm.is_active" />
                            <Label for="edit_active" class="cursor-pointer">Mark as Active Session</Label>
                        </div>
                        <DialogFooter>
                            <DialogClose as-child>
                                <Button variant="outline" type="button">Cancel</Button>
                            </DialogClose>
                            <Button type="submit" :disabled="editForm.processing">
                                {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <Dialog v-model:open="showDeleteDialog">
                <DialogContent class="max-w-md">
                    <DialogHeader>
                        <DialogTitle>Delete Session</DialogTitle>
                        <DialogDescription>
                            Delete "{{ sessionToDelete?.name }}" and all its attendance records? This cannot be undone.
                        </DialogDescription>
                    </DialogHeader>

                    <DialogFooter>
                        <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
                        <Button variant="destructive" @click="handleDelete">Confirm Delete</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
