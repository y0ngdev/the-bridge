<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogClose } from '@/components/ui/dialog';
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
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ScrollArea } from '@/components/ui/scroll-area';
import { type BreadcrumbItem, type Tenure, type EnumOption, type AlumnusSummary, type PaginatedResponse } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { distribution, show } from '@/actions/App/Http/Controllers/AlumnusController';
import { MapPin, Search, Download, Eye, Filter, Check } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';

type PaginatedAlumni = PaginatedResponse<AlumnusSummary>;

const props = defineProps<{
    alumni: PaginatedAlumni;
    stateDistribution: Record<string, number>;
    units: EnumOption[];
    states: EnumOption[];
    tenures: EnumOption[];
    filters: {
        state?: string;
        unit?: string;
        tenure_id?: number;
        search?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Alumni', href: '/alumni' },
    { title: 'Distribution', href: distribution().url },
];

const searchQuery = ref(props.filters.search || '');
const selectedState = ref(props.filters.state || '');
const selectedUnit = ref(props.filters.unit || '');
const selectedTenure = ref(props.filters.tenure_id || '');
const isExportDialogOpen = ref(false);

// Export field selection
type ExportFieldKey = 'name' | 'email' | 'phones' | 'state' | 'unit' | 'department' | 'gender' | 'birth_date' | 'tenure' | 'current_exco_office' | 'past_exco_offices' | 'is_futa_staff' | 'address';

// Array of selected field keys (all selected by default)
const selectedExportFields = ref<ExportFieldKey[]>([
    'name', 'email', 'phones', 'state', 'unit', 'department', 
    'gender', 'birth_date', 'tenure', 'current_exco_office', 
    'past_exco_offices', 'is_futa_staff', 'address'
]);

const availableExportFields: Array<{ key: ExportFieldKey; label: string }> = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'phones', label: 'Phone Numbers' },
    { key: 'state', label: 'State' },
    { key: 'unit', label: 'Unit' },
    { key: 'department', label: 'Department' },
    { key: 'gender', label: 'Gender' },
    { key: 'birth_date', label: 'Birthday' },
    { key: 'tenure', label: 'Tenure' },
    { key: 'current_exco_office', label: 'Current Exco Office' },
    { key: 'past_exco_offices', label: 'Past Exco Offices' },
    { key: 'is_futa_staff', label: 'FUTA Staff' },
    { key: 'address', label: 'Address' },
];

const isFieldSelected = (field: ExportFieldKey) => selectedExportFields.value.includes(field);

const toggleField = (field: ExportFieldKey, value?: boolean) => {
    const index = selectedExportFields.value.indexOf(field);
    const shouldBeSelected = value !== undefined ? value : index === -1;
    
    if (shouldBeSelected && index === -1) {
        selectedExportFields.value.push(field);
    } else if (!shouldBeSelected && index > -1) {
        selectedExportFields.value.splice(index, 1);
    }
};

// Pre-created computed models for each export field to work with reka-ui Checkbox
const exportFieldModels = Object.fromEntries(
    availableExportFields.map(field => [
        field.key,
        computed({
            get: () => selectedExportFields.value.includes(field.key),
            set: (value: boolean) => toggleField(field.key, value)
        })
    ])
) as Record<ExportFieldKey, ReturnType<typeof computed<boolean>>>;

const totalAlumni = computed(() => {
    // Sum all counts from stateDistribution to get true total, not filtered total
    return Object.values(props.stateDistribution).reduce((sum: number, count) => sum + (count as number), 0);
});

const filteredAlumniCount = computed(() => props.alumni?.total ?? 0);

const applyFilters = () => {
    router.get(
        distribution().url,
        {
            state: selectedState.value || undefined,
            unit: selectedUnit.value || undefined,
            tenure_id: selectedTenure.value || undefined,
            search: searchQuery.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const clearFilters = () => {
    selectedState.value = '';
    selectedUnit.value = '';
    selectedTenure.value = '';
    searchQuery.value = '';
    router.get(distribution().url);
};

const selectState = (state: string) => {
    selectedState.value = state;
    applyFilters();
};

const handleExport = () => {
    if (selectedExportFields.value.length === 0) {
        toast.error('Please select at least one field to export');
        return;
    }

    // Build query parameters - use availableExportFields order to maintain consistent header order
    const params = new URLSearchParams();
    const orderedFields = availableExportFields
        .filter(f => selectedExportFields.value.includes(f.key))
        .map(f => f.key);
    orderedFields.forEach(field => params.append('fields[]', field));
    
    if (selectedState.value) params.append('state', selectedState.value);
    if (selectedUnit.value) params.append('unit', selectedUnit.value);
    if (selectedTenure.value) params.append('tenure_id', String(selectedTenure.value));

    // Redirect to export endpoint
    window.location.href = `/alumni/export?${params.toString()}`;
    
    isExportDialogOpen.value = false;
    toast.success('Export started! Download will begin shortly.');
};

const paginationLinks = computed(() => {
    if (!props.alumni?.links) return [];
    return props.alumni.links.filter(link => {
        // Remove "Previous" and "Next" text links
        return link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;';
    });
});
</script>

<template>
    <Head title="Alumni Distribution" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex gap-6">
            <!-- Sidebar with state distribution -->
            <Card class="w-80 h-fit sticky top-6">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <MapPin class="h-5 w-5" />
                        States
                    </CardTitle>
                    <CardDescription>{{ Object.keys(stateDistribution).length }} states with alumni</CardDescription>
                </CardHeader>
                <CardContent>
                    <ScrollArea class="h-[calc(100vh-240px)]">
                        <div class="space-y-1">
                            <Button
                                variant="ghost"
                                class="w-full justify-between"
                                :class="{ 'bg-accent': !selectedState }"
                                @click="selectState('')"
                            >
                                <span>All States</span>
                                <span class="text-muted-foreground">{{ totalAlumni }}</span>
                            </Button>
                            <Button
                                v-for="(count, state) in stateDistribution"
                                :key="String(state)"
                                variant="ghost"
                                class="w-full justify-between"
                                :class="{ 'bg-accent': selectedState === String(state) }"
                                @click="selectState(String(state))"
                            >
                                <span>{{ String(state) }}</span>
                                <span class="text-muted-foreground">{{ count }}</span>
                            </Button>
                        </div>
                    </ScrollArea>
                </CardContent>
            </Card>

            <!-- Main content area -->
            <div class="flex-1 space-y-6">
                <!-- Header with filters and export -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 space-y-1">
                        <HeadingSmall title="Alumni Distribution" />
                        <p class="text-sm text-muted-foreground">
                            View and filter alumni by state and unit. Export filters by selected state.
                        </p>
                    </div>
                    <Dialog v-model:open="isExportDialogOpen">
                        <DialogTrigger as-child>
                            <Button>
                                <Download class="mr-2 h-4 w-4" />
                                Export
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="max-w-md">
                            <DialogHeader>
                                <DialogTitle>Customize Export</DialogTitle>
                                <DialogDescription>
                                    Select the fields you want to include in your export
                                </DialogDescription>
                            </DialogHeader>
                            <ScrollArea class="h-[400px] pr-4">
                                <div class="space-y-4">
                                    <div v-for="field in availableExportFields" :key="field.key" class="flex items-center space-x-2">
                                        <Checkbox
                                            :id="field.key"
                                            :model-value="isFieldSelected(field.key)"
                                            @update:model-value="(val: boolean | 'indeterminate') => toggleField(field.key, val === true)"
                                        />
                                        <Label :for="field.key" class="cursor-pointer">{{ field.label }}</Label>
                                    </div>
                                </div>
                            </ScrollArea>
                            <DialogFooter>
                                <DialogClose as-child>
                                    <Button variant="outline">Cancel</Button>
                                </DialogClose>
                                <Button @click="handleExport">
                                    <Download class="mr-2 h-4 w-4" />
                                    Export
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>

                <!-- Filters -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Filter class="h-4 w-4" />
                            Filters
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex gap-4">
                            <div class="flex-1 space-y-2">
                                <Label for="search">Search</Label>
                                <div class="relative">
                                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        id="search"
                                        v-model="searchQuery"
                                        placeholder="Search by name or email..."
                                        class="pl-9"
                                        @keyup.enter="applyFilters"
                                    />
                                </div>
                            </div>
                            <div class="flex-1 space-y-2">
                                <Label for="unit">Unit</Label>
                                <Select v-model="selectedUnit">
                                    <SelectTrigger id="unit">
                                        <SelectValue placeholder="All Units" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="unit in units" :key="unit.value" :value="unit.value">
                                            {{ unit.label }}
                                        </SelectItem>
                                    </SelectContent>
                                    </Select>
                                </div>
                                <div class="flex-1 space-y-2">
                                    <Label for="tenure">Tenure</Label>
                                    <Select v-model="selectedTenure">
                                        <SelectTrigger id="tenure">
                                            <SelectValue placeholder="All Tenures" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="tenure in tenures" :key="tenure.value" :value="tenure.value">
                                                {{ tenure.label }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="flex items-end gap-2">
                                <Button @click="applyFilters">
                                    Apply Filters
                                </Button>
                                <Button variant="outline" @click="clearFilters">
                                    Clear
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Alumni table -->
                <Card>
                    <CardHeader>
                        <CardTitle>Alumni List</CardTitle>
                        <CardDescription>
                            <template v-if="alumni?.data?.length">
                                Showing {{ alumni.from }}-{{ alumni.to }} of {{ filteredAlumniCount }} alumni
                            </template>
                            <template v-else>
                                No alumni found
                            </template>
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>State</TableHead>
                                    <TableHead>Unit</TableHead>
                                    <TableHead>Tenure</TableHead>
                                    <TableHead class="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="alumni.data.length === 0">
                                    <TableCell colspan="6" class="text-center py-8 text-muted-foreground">
                                        No alumni found matching your filters
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="alumnus in alumni.data" :key="alumnus.id">
                                    <TableCell class="font-medium">{{ alumnus.name }}</TableCell>
                                    <TableCell>{{ alumnus.email || '-' }}</TableCell>
                                    <TableCell>{{ alumnus.state || '-' }}</TableCell>
                                    <TableCell>{{ alumnus.unit || '-' }}</TableCell>
                                    <TableCell>{{ alumnus.tenure?.year || '-' }}</TableCell>
                                    <TableCell class="text-right">
                                        <Link :href="show.url(alumnus.id)">
                                            <Button variant="ghost" size="sm">
                                                <Eye class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <!-- Pagination -->
                        <div v-if="alumni?.last_page && alumni.last_page > 1" class="mt-6 flex justify-center gap-2">
                            <Link 
                                v-if="alumni?.prev_page_url"
                                :href="alumni.first_page_url || '#'"
                                preserve-state
                                preserve-scroll
                            >
                                <Button variant="outline" size="sm">First</Button>
                            </Link>
                            <Link 
                                v-if="alumni?.prev_page_url"
                                :href="alumni.prev_page_url"
                                preserve-state
                                preserve-scroll
                            >
                                <Button variant="outline" size="sm">Previous</Button>
                            </Link>
                            
                            <template v-for="(link, index) in paginationLinks" :key="index">
                                <span v-if="link.label === '...'" class="px-3 py-2 text-sm text-muted-foreground">...</span>
                                <Link
                                    v-else
                                    :href="link.url || '#'"
                                    preserve-state
                                    preserve-scroll
                                >
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        :class="{ 'bg-accent': link.active }"
                                    >
                                        {{ link.label }}
                                    </Button>
                                </Link>
                            </template>

                            <Link 
                                v-if="alumni?.next_page_url"
                                :href="alumni.next_page_url"
                                preserve-state
                                preserve-scroll
                            >
                                <Button variant="outline" size="sm">Next</Button>
                            </Link>
                            <Link 
                                v-if="alumni?.next_page_url"
                                :href="alumni.last_page_url || '#'"
                                preserve-state
                                preserve-scroll
                            >
                                <Button variant="outline" size="sm">Last</Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
