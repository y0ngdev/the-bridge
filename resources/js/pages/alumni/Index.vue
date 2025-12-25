<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogClose } from '@/components/ui/dialog';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationFirst,
    PaginationItem,
    PaginationLast,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { type BreadcrumbItem, type Tenure, type EnumOption, type Alumnus, type SimplePaginatedResponse } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { index, store, update, destroy, show, importStore } from '@/actions/App/Http/Controllers/AlumnusController';
import { Plus, Edit, Trash2, Check, ChevronsUpDown, Eye, Download, Upload, Info, X, MessageSquarePlus } from 'lucide-vue-next';
import CommunicationLogForm from '@/components/CommunicationLogForm.vue';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.is_admin ?? false);

type PaginatedAlumni = SimplePaginatedResponse<Alumnus>;

const props = defineProps<{
    alumni: PaginatedAlumni;
    tenures: Tenure[];
    units: EnumOption[];
    states: EnumOption[];
    pastExcoOffices: EnumOption[];
    departments: EnumOption[];
    filters: {
        search?: string;
        tenure_id?: string;
        unit?: string;
        state?: string;
        gender?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Alumni', href: index().url },
];

const showAddDialog = ref(false);
const showEditDialog = ref(false);
const showExportDialog = ref(false);
const showImportDialog = ref(false);
const showLogDialog = ref(false);
const editingAlumnus = ref<Alumnus | null>(null);
const selectedAlumnusForLog = ref<Alumnus | null>(null);

const openTenureCombobox = ref(false);
const openUnitCombobox = ref(false);
const openStateCombobox = ref(false);
const openEditTenureCombobox = ref(false);
const openEditUnitCombobox = ref(false);
const openEditStateCombobox = ref(false);
const openDepartmentCombobox = ref(false);
const openEditDepartmentCombobox = ref(false);

const exportFields = ref<string[]>([
    'name', 'email', 'phones', 'department', 'gender',
    'birth_date', 'tenure', 'unit', 'state', 'address',
    'past_exco_office', 'current_exco_office', 'is_futa_staff'
]);
const availableExportFields = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'phones', label: 'Phone(s)' },
    { key: 'department', label: 'Department' },
    { key: 'gender', label: 'Gender' },
    { key: 'birth_date', label: 'Birthday' },
    { key: 'tenure', label: 'Tenure' },
    { key: 'unit', label: 'Unit' },
    { key: 'state', label: 'State' },
    { key: 'address', label: 'Address' },
    { key: 'past_exco_office', label: 'Past Exco Office' },
    { key: 'current_exco_office', label: 'Current Exco Office (Alumni)' },
    { key: 'is_futa_staff', label: 'Is FUTA Staff' },
];
const exportTenureId = ref('');
const exportUnit = ref('');
const exportState = ref('');
const exportGender = ref('');

function toggleExportField(key: string, value?: boolean) {
    const index = exportFields.value.indexOf(key);
    const shouldBeSelected = value !== undefined ? value : index === -1;
    
    if (shouldBeSelected && index === -1) {
        exportFields.value.push(key);
    } else if (!shouldBeSelected && index > -1) {
        exportFields.value.splice(index, 1);
    }
}

const isExportFieldSelected = (key: string) => exportFields.value.includes(key);

function getExportUrl() {
    const params = new URLSearchParams();
    // Use availableExportFields order to maintain consistent header order
    const orderedFields = availableExportFields
        .filter(f => exportFields.value.includes(f.key))
        .map(f => f.key);
    orderedFields.forEach(f => params.append('fields[]', f));
    if (exportTenureId.value && exportTenureId.value !== '__all__') params.append('tenure_id', exportTenureId.value);
    if (exportUnit.value && exportUnit.value !== '__all__') params.append('unit', exportUnit.value);
    if (exportState.value && exportState.value !== '__all__') params.append('state', exportState.value);
    if (exportGender.value && exportGender.value !== '__all__') params.append('gender', exportGender.value);
    return `/alumni/export?${params.toString()}`;
}

// Filter state
const filterSearch = ref(props.filters?.search || '');
const filterTenureId = ref(props.filters?.tenure_id || '');
const filterUnit = ref(props.filters?.unit || '');
const filterState = ref(props.filters?.state || '');
const filterGender = ref(props.filters?.gender || '');

const addForm = useForm({
    name: '',
    email: '',
    phones: [] as string[],
    department: '',
    gender: '',
    birth_date: '',
    tenure_id: '' as string | number,
    unit: '',
    state: '',
    address: '',
    past_exco_office: '',
    current_exco_office: '',
    is_futa_staff: false,
});

const editForm = useForm({
    name: '',
    email: '',
    phones: [] as string[],
    department: '',
    gender: '',
    birth_date: '',
    tenure_id: '' as string | number,
    unit: '',
    state: '',
    address: '',
    past_exco_office: '',
    current_exco_office: '',
    is_futa_staff: false,
});

const deleteForm = useForm({});

const importForm = useForm<{ file: File | null; tenure_id: string | number }>({
    file: null,
    tenure_id: '',
});

const fileInputRef = ref<HTMLInputElement | null>(null);

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        importForm.file = target.files[0];
    }
}

function handleImportSubmit() {
    if (!importForm.file || !importForm.tenure_id) return;
    importForm.post(importStore().url, {
        forceFormData: true,
        onSuccess: () => {
            showImportDialog.value = false;
            importForm.reset();
            if (fileInputRef.value) fileInputRef.value.value = '';
            toast.success('Alumni imported successfully');
        },
    });
}

function getFilterParams() {
    const params: Record<string, string> = {};
    if (filterSearch.value) params.search = filterSearch.value;
    if (filterTenureId.value) params.tenure_id = filterTenureId.value;
    if (filterUnit.value) params.unit = filterUnit.value;
    if (filterState.value) params.state = filterState.value;
    if (filterGender.value) params.gender = filterGender.value;
    return params;
}

function applyFilters() {
    router.get(index().url, getFilterParams(), { preserveState: true, preserveScroll: true });
}

function clearFilters() {
    filterSearch.value = '';
    filterTenureId.value = '';
    filterUnit.value = '';
    filterState.value = '';
    filterGender.value = '';
    router.get(index().url, {}, { preserveState: true, preserveScroll: true });
}

const goToPage = (page: number) => {
    router.get(index().url, { ...getFilterParams(), page }, { preserveState: true, preserveScroll: true });
};

function handleAddSubmit() {
    addForm.post(store().url, {
        onSuccess: () => {
            showAddDialog.value = false;
            addForm.reset();
            toast.success('Alumnus added successfully');
        },
    });
}

// Format a stored birth date (ISO format) to user-friendly format (e.g., "20 Dec")
function formatBirthDateForEdit(dateString: string | null): string {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString; // Return as-is if invalid
        return `${date.getDate()} ${date.toLocaleDateString('en-US', { month: 'short' })}`;
    } catch {
        return dateString;
    }
}

function openEditDialog(alumnus: Alumnus) {
    editingAlumnus.value = alumnus;
    editForm.name = alumnus.name;
    editForm.email = alumnus.email || '';
    editForm.phones = alumnus.phones || [];
    editForm.department = alumnus.department || '';
    editForm.gender = alumnus.gender || '';
    editForm.birth_date = formatBirthDateForEdit(alumnus.birth_date);
    editForm.tenure_id = alumnus.tenure_id || '';
    editForm.unit = alumnus.unit || '';
    editForm.state = alumnus.state || '';
    editForm.address = alumnus.address || '';
    editForm.past_exco_office = alumnus.past_exco_office || '';
    editForm.current_exco_office = alumnus.current_exco_office || '';
    editForm.is_futa_staff = alumnus.is_futa_staff || false;
    showEditDialog.value = true;
}

function handleEditSubmit() {
    if (!editingAlumnus.value) return;
    editForm.put(update(editingAlumnus.value.id).url, {
        onSuccess: () => {
            showEditDialog.value = false;
            editForm.reset();
            toast.success('Alumnus updated successfully');
        },
    });
}

function handleDelete(alumnus: Alumnus) {
    if (!confirm(`Delete ${alumnus.name}?`)) return;
    deleteForm.delete(destroy(alumnus.id).url, {
        onSuccess: () => {
            toast.success('Alumnus deleted successfully');
        },
    });
}

function openLogDialog(alumnus: Alumnus) {
    selectedAlumnusForLog.value = alumnus;
    showLogDialog.value = true;
}
</script>

<template>
    <Head title="Alumni" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="flex items-center justify-between mb-4">
                <HeadingSmall title="Alumni" description="Manage alumni records" />
                <div class="flex gap-2">
                    <Dialog v-model:open="showImportDialog">
                        <DialogTrigger as-child>
                            <Button variant="outline">
                                <Upload class="h-4 w-4 mr-2" />
                                Import Alumni
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                            <DialogHeader>
                                <DialogTitle>Import Alumni</DialogTitle>
                                <DialogDescription>Upload an Excel file (.xlsx or .xls) to import alumni records for the selected tenure.</DialogDescription>
                            </DialogHeader>
                            <form @submit.prevent="handleImportSubmit" class="space-y-4">
                                <Alert class="bg-blue-50/50 dark:bg-blue-950/20 border-blue-200 dark:border-blue-900">
                                    <Info class="h-4 w-4 text-blue-600 font-bold" />
                                    <AlertTitle class="text-blue-700 dark:text-blue-400 font-bold">Import Requirements</AlertTitle>
                                    <AlertDescription class="mt-2 space-y-4 text-xs">
                                        <div>
                                            <p class="font-semibold mb-2 flex items-center gap-1.5"><Check class="size-3 text-green-600" /> 1. Required Headers</p>
                                            <p class="text-muted-foreground mb-2">The system looks for these specific column names in your first row:</p>
                                            <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                                                <div class="flex flex-col gap-0.5"><code class="w-fit bg-muted px-1 rounded text-[10px] font-bold">name</code> <span class="text-[9px] text-muted-foreground italic">Full Name </span></div>
                                                <div class="flex flex-col gap-0.5"><code class="w-fit bg-muted px-1 rounded text-[10px] font-bold">email</code> <span class="text-[9px] text-muted-foreground italic">Valid email address</span></div>
                                                <div class="flex flex-col gap-0.5"><code class="w-fit bg-muted px-1 rounded text-[10px] font-bold">phones</code> <span class="text-[9px] text-muted-foreground italic">Comma-separated numbers</span></div>
                                                <div class="flex flex-col gap-0.5"><code class="w-fit bg-muted px-1 rounded text-[10px] font-bold">gender</code> <span class="text-[9px] text-muted-foreground italic">M or F</span></div>
                                                <div class="flex flex-col gap-0.5"><code class="w-fit bg-muted px-1 rounded text-[10px] font-bold">birth_date</code> <span class="text-[9px] text-muted-foreground italic">Birthday</span></div>
                                                <div class="flex flex-col gap-0.5"><code class="w-fit bg-muted px-1 rounded text-[10px] font-bold">state</code> <span class="text-[9px] text-muted-foreground italic">State of residence</span></div>
                                                <div class="flex flex-col gap-0.5"><code class="w-fit bg-muted px-1 rounded text-[10px] font-bold">unit</code> <span class="text-[9px] text-muted-foreground italic">Assigned Unit</span></div>
                                                <div class="flex flex-col gap-0.5"><code class="w-fit bg-muted px-1 rounded text-[10px] font-bold">address</code> <span class="text-[9px] text-muted-foreground italic">Residential address</span></div>
                                            </div>
                                        </div>

                                        <div>
                                            <p class="text-muted-foreground mb-2">The system intelligently parses birthday formats:</p>
                                            <div class="flex flex-wrap gap-2">:
                                                <div class="flex flex-col items-center gap-1"><code class="bg-muted px-1.5 py-0.5 rounded font-bold">20-12</code><span class="text-[8px] text-muted-foreground">Day-Month</span></div>
                                                <div class="flex flex-col items-center gap-1"><code class="bg-muted px-1.5 py-0.5 rounded font-bold">20/12</code><span class="text-[8px] text-muted-foreground">Day/Month</span></div>
                                                <div class="flex flex-col items-center gap-1"><code class="bg-muted px-1.5 py-0.5 rounded font-bold">20 Dec</code><span class="text-[8px] text-muted-foreground">Day ShortMonth</span></div>
                                                <div class="flex flex-col items-center gap-1"><code class="bg-muted px-1.5 py-0.5 rounded font-bold">20 December</code><span class="text-[8px] text-muted-foreground">Day FullMonth</span></div>
                                            </div>
                                        </div>

                                        <div>
                                            
                                            <ul class="space-y-1.5 text-muted-foreground ml-1">
                                             
                                                <li class="flex gap-2"><span>•</span> <strong>Clean data:</strong> Ensure no empty rows exist between records.</li>
                                             
                                            </ul>
                                        </div>
                                    </AlertDescription>
                                </Alert>
                                <div class="space-y-2">
                                    <Label>Tenure *</Label>
                                    <Select v-model="importForm.tenure_id">
                                        <SelectTrigger :class="importForm.errors.tenure_id && 'border-destructive'">
                                            <SelectValue placeholder="Select tenure" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="t in tenures" :key="t.id" :value="String(t.id)">{{ t.year }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p class="text-xs text-muted-foreground">All imported alumni will be assigned to this tenure.</p>
                                    <p v-if="importForm.errors.tenure_id" class="text-sm text-destructive">{{ importForm.errors.tenure_id }}</p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="import_file">Excel File *</Label>
                                    <Input
                                        id="import_file"
                                        ref="fileInputRef"
                                        type="file"
                                        accept=".xlsx,.xls"
                                        @change="handleFileChange"
                                        :class="importForm.errors.file && 'border-destructive'"
                                    />
                                    <p v-if="importForm.errors.file" class="text-sm text-destructive">{{ importForm.errors.file }}</p>
                                </div>
                                <DialogFooter>
                                    <DialogClose as-child>
                                        <Button variant="outline" type="button">Cancel</Button>
                                    </DialogClose>
                                    <Button type="submit" :disabled="!importForm.file || !importForm.tenure_id || importForm.processing">
                                        <Upload class="h-4 w-4 mr-2" />
                                        {{ importForm.processing ? 'Importing...' : 'Import' }}
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                    <Dialog v-model:open="showExportDialog">
                        <DialogTrigger as-child>
                            <Button variant="outline">
                                <Download class="h-4 w-4 mr-2" />
                                Export
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="max-w-lg max-h-[85vh] overflow-y-auto">
                            <DialogHeader>
                                <DialogTitle>Export Alumni</DialogTitle>
                                <DialogDescription>Choose which fields to export and optionally filter the data.</DialogDescription>
                            </DialogHeader>
                            <div class="space-y-4">
                                <div>
                                    <Label class="text-base font-semibold">Fields to Export</Label>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div v-for="field in availableExportFields" :key="field.key" class="flex items-center gap-2">
                                            <Checkbox 
                                                :id="`export_${field.key}`" 
                                                :model-value="isExportFieldSelected(field.key)" 
                                                @update:model-value="(val: boolean | 'indeterminate') => toggleExportField(field.key, val === true)" 
                                            />
                                            <Label :for="`export_${field.key}`" class="text-sm cursor-pointer">{{ field.label }}</Label>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-t pt-4">
                                    <Label class="text-base font-semibold">Filter Data (Optional)</Label>
                                    <div class="grid grid-cols-2 gap-4 mt-2">
                                        <div class="space-y-1">
                                            <Label class="text-sm">Tenure</Label>
                                            <Select v-model="exportTenureId">
                                                <SelectTrigger><SelectValue placeholder="All tenures" /></SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="__all__">All tenures</SelectItem>
                                                    <SelectItem v-for="t in tenures" :key="t.id" :value="String(t.id)">{{ t.year }}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-sm">Gender</Label>
                                            <Select v-model="exportGender">
                                                <SelectTrigger><SelectValue placeholder="All genders" /></SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="__all__">All genders</SelectItem>
                                                    <SelectItem value="M">Male</SelectItem>
                                                    <SelectItem value="F">Female</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-sm">Unit</Label>
                                            <Select v-model="exportUnit">
                                                <SelectTrigger><SelectValue placeholder="All units" /></SelectTrigger>
                                                <SelectContent class="max-h-60">
                                                    <SelectItem value="__all__">All units</SelectItem>
                                                    <SelectItem v-for="u in units" :key="u.value" :value="u.value">{{ u.label }}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-sm">State</Label>
                                            <Select v-model="exportState">
                                                <SelectTrigger><SelectValue placeholder="All states" /></SelectTrigger>
                                                <SelectContent class="max-h-60">
                                                    <SelectItem value="__all__">All states</SelectItem>
                                                    <SelectItem v-for="s in states" :key="s.value" :value="s.value">{{ s.label }}</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <DialogFooter>
                                <DialogClose as-child>
                                    <Button variant="outline" type="button">Cancel</Button>
                                </DialogClose>
                                <a :href="getExportUrl()" @click="showExportDialog = false">
                                    <Button :disabled="exportFields.length === 0">
                                        <Download class="h-4 w-4 mr-2" />
                                        Download Excel
                                    </Button>
                                </a>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                    <Dialog v-model:open="showAddDialog">
                        <DialogTrigger as-child>
                            <Button>
                                <Plus class="h-4 w-4 mr-2" />
                                Add Alumnus
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                            <DialogHeader>
                                <DialogTitle>Add New Alumnus</DialogTitle>
                                <DialogDescription>Enter the alumni details below.</DialogDescription>
                            </DialogHeader>
                            <form @submit.prevent="handleAddSubmit" class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="name">Full Name *</Label>
                                    <Input id="name" v-model="addForm.name" required :class="addForm.errors.name && 'border-destructive'" />
                                    <p v-if="addForm.errors.name" class="text-sm text-destructive">{{ addForm.errors.name }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="email">Email</Label>
                                        <Input id="email" type="email" v-model="addForm.email" :class="addForm.errors.email && 'border-destructive'" />
                                        <p v-if="addForm.errors.email" class="text-sm text-destructive">{{ addForm.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Phone Numbers</Label>
                                        <div class="space-y-2">
                                            <div v-for="(phone, index) in addForm.phones" :key="index" class="flex gap-2">
                                                <Input 
                                                    v-model="addForm.phones[index]" 
                                                    type="tel" 
                                                    placeholder="e.g., +234 123 456 7890"
                                                    class="flex-1"
                                                />
                                                <Button 
                                                    type="button" 
                                                    variant="ghost" 
                                                    size="icon"
                                                    @click="addForm.phones.splice(index, 1)"
                                                    class="shrink-0"
                                                >
                                                    <X class="h-4 w-4" />
                                                </Button>
                                            </div>
                                            <Button 
                                                type="button" 
                                                variant="outline" 
                                                size="sm" 
                                                @click="addForm.phones.push('')"
                                                class="w-full"
                                            >
                                                <Plus class="h-4 w-4 mr-2" />
                                                Add Phone Number
                                            </Button>
                                        </div>
                                        <p v-if="addForm.errors.phones" class="text-sm text-destructive">{{ addForm.errors.phones }}</p>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <Label>Department</Label>
                                    <Popover v-model:open="openDepartmentCombobox">
                                        <PopoverTrigger as-child>
                                            <Button variant="outline" role="combobox" class="justify-between w-full" :class="!addForm.department && 'text-muted-foreground'" :aria-invalid="!!addForm.errors.department">
                                                {{ addForm.department ? departments.find(d => d.value === addForm.department)?.label : 'Select department...' }}
                                                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                            </Button>
                                        </PopoverTrigger>
                                        <PopoverContent class="!w-full p-0 max-h-[400px]">
                                            <Command>
                                                <CommandInput placeholder="Search department..." />
                                                <CommandEmpty>No department found.</CommandEmpty>
                                                <CommandList>
                                                    <CommandGroup>
                                                        <CommandItem v-for="d in departments" :key="d.value" :value="d.label" @select="() => { addForm.department = d.value; openDepartmentCombobox = false; }">
                                                            {{ d.label }}
                                                            <Check class="ml-auto h-4 w-4" :class="addForm.department === d.value ? 'opacity-100' : 'opacity-0'" />
                                                        </CommandItem>
                                                    </CommandGroup>
                                                </CommandList>
                                            </Command>
                                        </PopoverContent>
                                    </Popover>
                                    <p v-if="addForm.errors.department" class="text-sm text-destructive">{{ addForm.errors.department }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label>Gender</Label>
                                        <Select v-model="addForm.gender">
                                            <SelectTrigger :class="addForm.errors.gender && 'border-destructive'"><SelectValue placeholder="Select gender" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="M">Male</SelectItem>
                                                <SelectItem value="F">Female</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="addForm.errors.gender" class="text-sm text-destructive">{{ addForm.errors.gender }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="birth_date">Birthday</Label>
                                        <Input id="birth_date" v-model="addForm.birth_date" placeholder="e.g., 20-12, 20/12, 20 Dec" :class="addForm.errors.birth_date && 'border-destructive'" />
                                        <p v-if="addForm.errors.birth_date" class="text-sm text-destructive">{{ addForm.errors.birth_date }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2 flex flex-col">
                                        <Label>Tenure</Label>
                                        <Popover v-model:open="openTenureCombobox">
                                            <PopoverTrigger as-child>
                                                <Button variant="outline" role="combobox" class="justify-between" :class="!addForm.tenure_id && 'text-muted-foreground'">
                                                    {{ addForm.tenure_id ? tenures.find(t => t.id === Number(addForm.tenure_id))?.year : 'Select tenure...' }}
                                                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="!w-full p-0">
                                                <Command>
                                                    <CommandInput placeholder="Search tenure..." />
                                                    <CommandEmpty>No tenure found.</CommandEmpty>
                                                    <CommandList>
                                                        <CommandGroup>
                                                            <CommandItem v-for="t in tenures" :key="t.id" :value="t.year" @select="() => { addForm.tenure_id = t.id; openTenureCombobox = false; }">
                                                                {{ t.year }}
                                                                <Check class="ml-auto h-4 w-4" :class="addForm.tenure_id === t.id ? 'opacity-100' : 'opacity-0'" />
                                                            </CommandItem>
                                                        </CommandGroup>
                                                    </CommandList>
                                                </Command>
                                            </PopoverContent>
                                        </Popover>
                                    </div>
                                    <div class="space-y-2 flex flex-col">
                                        <Label>Unit</Label>
                                        <Popover v-model:open="openUnitCombobox">
                                            <PopoverTrigger as-child>
                                                <Button variant="outline" role="combobox" class="justify-between" :class="!addForm.unit && 'text-muted-foreground'">
                                                    {{ addForm.unit || 'Select unit...' }}
                                                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="!w-full p-0">
                                                <Command>
                                                    <CommandInput placeholder="Search unit..." />
                                                    <CommandEmpty>No unit found.</CommandEmpty>
                                                    <CommandList>
                                                        <CommandGroup>
                                                            <CommandItem v-for="u in units" :key="u.value" :value="u.label" @select="() => { addForm.unit = u.value; openUnitCombobox = false; }">
                                                                {{ u.label }}
                                                                <Check class="ml-auto h-4 w-4" :class="addForm.unit === u.value ? 'opacity-100' : 'opacity-0'" />
                                                            </CommandItem>
                                                        </CommandGroup>
                                                    </CommandList>
                                                </Command>
                                            </PopoverContent>
                                        </Popover>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2 flex flex-col">
                                        <Label>State</Label>
                                        <Popover v-model:open="openStateCombobox">
                                            <PopoverTrigger as-child>
                                                <Button variant="outline" role="combobox" class="justify-between" :class="!addForm.state && 'text-muted-foreground'">
                                                    {{ addForm.state || 'Select state...' }}
                                                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="!w-full p-0">
                                                <Command>
                                                    <CommandInput placeholder="Search state..." />
                                                    <CommandEmpty>No state found.</CommandEmpty>
                                                    <CommandList>
                                                        <CommandGroup>
                                                            <CommandItem v-for="s in states" :key="s.value" :value="s.label" @select="() => { addForm.state = s.value; openStateCombobox = false; }">
                                                                {{ s.label }}
                                                                <Check class="ml-auto h-4 w-4" :class="addForm.state === s.value ? 'opacity-100' : 'opacity-0'" />
                                                            </CommandItem>
                                                        </CommandGroup>
                                                    </CommandList>
                                                </Command>
                                            </PopoverContent>
                                        </Popover>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="address">Address</Label>
                                        <Input id="address" v-model="addForm.address" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label>Past Exco Office (School)</Label>
                                        <Select v-model="addForm.past_exco_office">
                                            <SelectTrigger><SelectValue placeholder="Select position" /></SelectTrigger>
                                            <SelectContent class="max-h-60">
                                                <SelectItem v-for="p in pastExcoOffices" :key="p.value" :value="p.value">{{ p.label }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="current_exco_office">Current Exco Office (Alumni)</Label>
                                        <Input id="current_exco_office" v-model="addForm.current_exco_office" placeholder="e.g., President, Secretary" />
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Checkbox id="is_futa_staff" v-model:checked="addForm.is_futa_staff" />
                                    <Label for="is_futa_staff" class="text-sm font-normal cursor-pointer">
                                        Is FUTA Staff
                                    </Label>
                                </div>
                                <DialogFooter>
                                    <DialogClose as-child>
                                        <Button variant="outline" type="button">Cancel</Button>
                                    </DialogClose>
                                    <Button type="submit" :disabled="addForm.processing">
                                        {{ addForm.processing ? 'Adding...' : 'Add Alumnus' }}
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>

                </div>
            </div>

            <!-- Filters -->
            <div class="mb-4 p-4 border rounded-md bg-muted/30 space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <Input v-model="filterSearch" placeholder="Search name/email..." @keyup.enter="applyFilters" />
                    <Select v-model="filterTenureId">
                        <SelectTrigger><SelectValue placeholder="All Tenures" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="t in tenures" :key="t.id" :value="String(t.id)">{{ t.year }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="filterUnit">
                        <SelectTrigger><SelectValue placeholder="All Units" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="u in units" :key="u.value" :value="u.value">{{ u.label }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="filterState">
                        <SelectTrigger><SelectValue placeholder="All States" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="s in states" :key="s.value" :value="s.value">{{ s.label }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="filterGender">
                        <SelectTrigger><SelectValue placeholder="All Genders" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="Male">Male</SelectItem>
                            <SelectItem value="Female">Female</SelectItem>
                        </SelectContent>
                    </Select>
                    <div class="flex gap-2">
                        <Button @click="applyFilters" class="flex-1">Filter</Button>
                        <Button variant="outline" @click="clearFilters">Clear</Button>
                    </div>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Department</TableHead>
                            <TableHead>Tenure</TableHead>
                            <TableHead>State</TableHead>
                            <TableHead class="w-[100px]">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="alumnus in alumni.data" :key="alumnus.id">
                            <TableCell class="font-medium">
                                {{ alumnus.name }}
                            </TableCell>
                            <TableCell>{{ alumnus.email || '—' }}</TableCell>
                            <TableCell>{{ departments.find(d => d.value === alumnus.department)?.label || alumnus.department || '—' }}</TableCell>
                            <TableCell>{{ alumnus.tenure?.year || '—' }}</TableCell>
                            <TableCell>{{ alumnus.state || '—' }}</TableCell>
                            <TableCell>
                                <div class="flex gap-1">
                                    <Link :href="show(alumnus.id).url">
                                        <Button variant="ghost" size="icon">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button variant="ghost" size="icon" @click="openLogDialog(alumnus)" title="Log Interaction">
                                        <MessageSquarePlus class="h-4 w-4 text-blue-600" />
                                    </Button>
                                    <Button v-if="isAdmin" variant="ghost" size="icon" @click="openEditDialog(alumnus)">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                    <Button v-if="isAdmin" variant="ghost" size="icon" @click="handleDelete(alumnus)">
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="alumni.data.length === 0">
                            <TableCell colspan="6" class="text-center text-muted-foreground py-8">
                                No alumni found. Add or import some records!
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <Pagination
                v-if="alumni.last_page > 1"
                v-slot="{ page }"
                :total="alumni.last_page * 15"
                :items-per-page="15"
                :default-page="alumni.current_page"
                :sibling-count="1"
                show-edges
                class="mt-4"
                @update:page="goToPage"
            >
                <PaginationContent v-slot="{ items }" class="gap-4">
                    <PaginationFirst class="cursor-pointer" />
                    <PaginationPrevious class="cursor-pointer"/>
                    <template v-for="(item, idx) in items" :key="idx">
                        <PaginationItem v-if="item.type === 'page'" :value="item.value" as-child>
                            <Button :variant="item.value === alumni.current_page ? 'default' : 'outline'" size="icon" class="cursor-pointer">
                                {{ item.value }}
                            </Button>
                        </PaginationItem>
                        <PaginationEllipsis v-else :index="idx" />
                    </template>
                    <PaginationNext class="cursor-pointer"/>
                    <PaginationLast class="cursor-pointer" />
                </PaginationContent>
            </Pagination>
        </div>

        <!-- Edit Dialog -->
        <Dialog v-model:open="showLogDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Log Interaction</DialogTitle>
                    <DialogDescription>Log a communication with {{ selectedAlumnusForLog?.name }}</DialogDescription>
                </DialogHeader>
                <CommunicationLogForm 
                    v-if="selectedAlumnusForLog" 
                    :alumnus-id="selectedAlumnusForLog.id" 
                    @success="showLogDialog = false" 
                />
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="showEditDialog">
            <DialogContent class="max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Edit Alumnus</DialogTitle>
                    <DialogDescription>Update the alumni details below.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="handleEditSubmit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="edit_name">Full Name *</Label>
                        <Input id="edit_name" v-model="editForm.name" required :class="editForm.errors.name && 'border-destructive'" />
                        <p v-if="editForm.errors.name" class="text-sm text-destructive">{{ editForm.errors.name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="edit_email">Email</Label>
                            <Input id="edit_email" type="email" v-model="editForm.email" :class="editForm.errors.email && 'border-destructive'" />
                            <p v-if="editForm.errors.email" class="text-sm text-destructive">{{ editForm.errors.email }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label>Phone Numbers</Label>
                            <div class="space-y-2">
                                <div v-for="(phone, index) in editForm.phones" :key="index" class="flex gap-2">
                                    <Input 
                                        v-model="editForm.phones[index]" 
                                        type="tel" 
                                        placeholder="e.g., +234 123 456 7890"
                                        class="flex-1"
                                    />
                                    <Button 
                                        type="button" 
                                        variant="ghost" 
                                        size="icon"
                                        @click="editForm.phones.splice(index, 1)"
                                        class="shrink-0"
                                    >
                                        <X class="h-4 w-4" />
                                    </Button>
                                </div>
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    size="sm" 
                                    @click="editForm.phones.push('')"
                                    class="w-full"
                                >
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Phone Number
                                </Button>
                            </div>
                            <p v-if="editForm.errors.phones" class="text-sm text-destructive">{{ editForm.errors.phones }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label>Department</Label>
                        <Popover v-model:open="openEditDepartmentCombobox">
                            <PopoverTrigger as-child>
                                <Button variant="outline" role="combobox" class="justify-between w-full" :class="!editForm.department && 'text-muted-foreground'" :aria-invalid="!!editForm.errors.department">
                                    {{ editForm.department ? departments.find(d => d.value === editForm.department)?.label : 'Select department...' }}
                                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="!w-full p-0 max-h-[400px]">
                                <Command>
                                    <CommandInput placeholder="Search department..." />
                                    <CommandEmpty>No department found.</CommandEmpty>
                                    <CommandList>
                                        <CommandGroup>
                                            <CommandItem v-for="d in departments" :key="d.value" :value="d.label" @select="() => { editForm.department = d.value; openEditDepartmentCombobox = false; }">
                                                {{ d.label }}
                                                <Check class="ml-auto h-4 w-4" :class="editForm.department === d.value ? 'opacity-100' : 'opacity-0'" />
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </PopoverContent>
                        </Popover>
                        <p v-if="editForm.errors.department" class="text-sm text-destructive">{{ editForm.errors.department }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Gender</Label>
                            <Select v-model="editForm.gender">
                                <SelectTrigger :class="editForm.errors.gender && 'border-destructive'"><SelectValue placeholder="Select gender" /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="M">Male</SelectItem>
                                    <SelectItem value="F">Female</SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="editForm.errors.gender" class="text-sm text-destructive">{{ editForm.errors.gender }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_birth_date">Birthday</Label>
                            <Input id="edit_birth_date" v-model="editForm.birth_date" placeholder="e.g., 20-12, 20/12, 20 Dec" :class="editForm.errors.birth_date && 'border-destructive'" />
                            <p v-if="editForm.errors.birth_date" class="text-sm text-destructive">{{ editForm.errors.birth_date }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2 flex flex-col">
                            <Label>Tenure</Label>
                            <Popover v-model:open="openEditTenureCombobox">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" role="combobox" class="justify-between" :class="!editForm.tenure_id && 'text-muted-foreground'">
                                        {{ editForm.tenure_id ? tenures.find(t => t.id === Number(editForm.tenure_id))?.year : 'Select tenure...' }}
                                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="!w-full p-0">
                                    <Command>
                                        <CommandInput placeholder="Search tenure..." />
                                        <CommandEmpty>No tenure found.</CommandEmpty>
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem v-for="t in tenures" :key="t.id" :value="t.year" @select="() => { editForm.tenure_id = t.id; openEditTenureCombobox = false; }">
                                                    {{ t.year }}
                                                    <Check class="ml-auto h-4 w-4" :class="editForm.tenure_id === t.id ? 'opacity-100' : 'opacity-0'" />
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                        </div>
                        <div class="space-y-2 flex flex-col">
                            <Label>Unit</Label>
                            <Popover v-model:open="openEditUnitCombobox">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" role="combobox" class="justify-between" :class="!editForm.unit && 'text-muted-foreground'">
                                        {{ editForm.unit || 'Select unit...' }}
                                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="!w-full p-0">
                                    <Command>
                                        <CommandInput placeholder="Search unit..." />
                                        <CommandEmpty>No unit found.</CommandEmpty>
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem v-for="u in units" :key="u.value" :value="u.label" @select="() => { editForm.unit = u.value; openEditUnitCombobox = false; }">
                                                    {{ u.label }}
                                                    <Check class="ml-auto h-4 w-4" :class="editForm.unit === u.value ? 'opacity-100' : 'opacity-0'" />
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2 flex flex-col">
                            <Label>State</Label>
                            <Popover v-model:open="openEditStateCombobox">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" role="combobox" class="justify-between" :class="!editForm.state && 'text-muted-foreground'">
                                        {{ editForm.state || 'Select state...' }}
                                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="!w-full p-0">
                                    <Command>
                                        <CommandInput placeholder="Search state..." />
                                        <CommandEmpty>No state found.</CommandEmpty>
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem v-for="s in states" :key="s.value" :value="s.label" @select="() => { editForm.state = s.value; openEditStateCombobox = false; }">
                                                    {{ s.label }}
                                                    <Check class="ml-auto h-4 w-4" :class="editForm.state === s.value ? 'opacity-100' : 'opacity-0'" />
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_address">Address</Label>
                            <Input id="edit_address" v-model="editForm.address" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Past Exco Office (School)</Label>
                            <Select v-model="editForm.past_exco_office">
                                <SelectTrigger><SelectValue placeholder="Select position" /></SelectTrigger>
                                <SelectContent class="max-h-60">
                                    <SelectItem v-for="p in pastExcoOffices" :key="p.value" :value="p.value">{{ p.label }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-2">
                            <Label for="edit_current_exco_office">Current Exco Office (Alumni)</Label>
                            <Input id="edit_current_exco_office" v-model="editForm.current_exco_office" placeholder="e.g., President, Secretary" />
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Checkbox id="edit_is_futa_staff" v-model:checked="editForm.is_futa_staff" />
                        <Label for="edit_is_futa_staff" class="text-sm font-normal cursor-pointer">
                            Is FUTA Staff
                        </Label>
                    </div>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="editForm.processing">
                            {{ editForm.processing ? 'Updating...' : 'Update Alumnus' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
