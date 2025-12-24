<script setup lang="ts">
import { index, store, update, destroy } from '@/actions/App/Http/Controllers/DepartmentController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { 
    Dialog, 
    DialogContent, 
    DialogDescription, 
    DialogFooter, 
    DialogHeader, 
    DialogTitle, 
    DialogTrigger 
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { 
    Select, 
    SelectContent, 
    SelectItem, 
    SelectTrigger, 
    SelectValue 
} from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Edit, Plus, Trash2, Search, Building2 } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';

interface Department {
    id: number;
    code: string;
    name: string;
    school: string | null;
}

const props = defineProps<{
    departments: Department[];
    departmentsBySchool: Record<string, Department[]>;
    schools: string[];
    filters: {
        search?: string;
        school?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Departments', href: index().url },
];

// Search
const searchQuery = ref(props.filters.search || '');
const selectedSchool = ref(props.filters.school || 'ALL');

function applyFilters() {
    router.get(index().url, {
        search: searchQuery.value || undefined,
        school: (selectedSchool.value === 'ALL' || !selectedSchool.value) ? undefined : selectedSchool.value,
    }, { preserveState: true });
}

function clearFilters() {
    searchQuery.value = '';
    selectedSchool.value = 'ALL';
    router.get(index().url);
}

// Add form
const addForm = useForm({
    code: '',
    name: '',
    school: '',
});
const showAddDialog = ref(false);

function handleAddSubmit() {
    addForm.post(store().url, {
        onSuccess: () => {
            showAddDialog.value = false;
            addForm.reset();
            toast.success('Department created successfully!');
        },
    });
}

// Edit form
const editForm = useForm({
    code: '',
    name: '',
    school: '',
});
const showEditDialog = ref(false);
const editingDepartment = ref<Department | null>(null);

function openEditDialog(department: Department) {
    editingDepartment.value = department;
    editForm.code = department.code;
    editForm.name = department.name;
    editForm.school = department.school || '';
    showEditDialog.value = true;
}

function handleEditSubmit() {
    if (!editingDepartment.value) return;
    editForm.put(update(editingDepartment.value.id).url, {
        onSuccess: () => {
            showEditDialog.value = false;
            editForm.reset();
            toast.success('Department updated successfully!');
        },
    });
}

// Delete
const showDeleteDialog = ref(false);
const deletingDepartment = ref<Department | null>(null);

function openDeleteDialog(department: Department) {
    deletingDepartment.value = department;
    showDeleteDialog.value = true;
}

function handleDelete() {
    if (!deletingDepartment.value) return;
    router.delete(destroy(deletingDepartment.value.id).url, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            toast.success('Department deleted successfully!');
        },
        onError: () => {
            toast.error('Cannot delete department with assigned alumni.');
        },
    });
}

// Stats
const totalDepartments = computed(() => props.departments.length);
const totalSchools = computed(() => props.schools.length);
</script>

<template>
    <Head title="Departments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6 max-w-7xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <HeadingSmall 
                    title="Departments" 
                    :description="`${totalDepartments} departments across ${totalSchools} schools`" 
                />

                <!-- Add Dialog -->
                <Dialog v-model:open="showAddDialog">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Add Department
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="max-w-md">
                        <DialogHeader>
                            <DialogTitle>Add New Department</DialogTitle>
                            <DialogDescription>Create a new department record</DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="handleAddSubmit" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="code">Code</Label>
                                <Input
                                    id="code"
                                    v-model="addForm.code"
                                    placeholder="e.g., CSC"
                                    required
                                />
                                <InputError :message="addForm.errors.code" />
                            </div>
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="addForm.name"
                                    placeholder="e.g., Computer Science"
                                    required
                                />
                                <InputError :message="addForm.errors.name" />
                            </div>
                            <div class="space-y-2">
                                <Label for="school">School</Label>
                                <Input
                                    id="school"
                                    v-model="addForm.school"
                                    placeholder="e.g., SOC"
                                />
                                <InputError :message="addForm.errors.school" />
                            </div>
                            <DialogFooter>
                                <Button type="submit" :disabled="addForm.processing">
                                    {{ addForm.processing ? 'Creating...' : 'Create Department' }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Filters -->
            <Card class="mb-6">
                <CardContent class="pt-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                <Input
                                    v-model="searchQuery"
                                    placeholder="Search departments..."
                                    class="pl-10"
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                        </div>
                        <Select v-model="selectedSchool" @update:model-value="applyFilters">
                            <SelectTrigger class="w-full sm:w-48">
                                <SelectValue placeholder="All Schools" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="ALL">All Schools</SelectItem>
                                <SelectItem v-for="school in schools" :key="school" :value="school">
                                    {{ school }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Button variant="outline" @click="applyFilters">Search</Button>
                        <Button variant="ghost" @click="clearFilters" v-if="filters.search || filters.school">
                            Clear
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Departments by School -->
            <div class="space-y-6">
                <Card v-for="(depts, school) in departmentsBySchool" :key="school">
                    <CardHeader class="pb-3">
                        <CardTitle class="flex items-center gap-2">
                            <Building2 class="h-5 w-5" />
                            {{ school || 'No School' }}
                            <Badge variant="secondary">{{ depts.length }}</Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-24">Code</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead class="w-24 text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="dept in depts" :key="dept.id">
                                    <TableCell>
                                        <Badge variant="outline">{{ dept.code }}</Badge>
                                    </TableCell>
                                    <TableCell>{{ dept.name }}</TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-1">
                                            <Button variant="ghost" size="icon" @click="openEditDialog(dept)">
                                                <Edit class="h-4 w-4" />
                                            </Button>
                                            <Button 
                                                variant="ghost" 
                                                size="icon" 
                                                class="text-destructive hover:text-destructive"
                                                @click="openDeleteDialog(dept)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <!-- Empty State -->
                <Card v-if="Object.keys(departmentsBySchool).length === 0" class="border-dashed">
                    <CardContent class="py-16 text-center">
                        <Building2 class="mx-auto h-16 w-16 text-muted-foreground/30 mb-4" />
                        <h3 class="text-lg font-medium mb-2">No Departments Found</h3>
                        <p class="text-muted-foreground mb-4">
                            {{ filters.search || filters.school ? 'No departments match your filters.' : 'Create your first department to get started.' }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Edit Dialog -->
            <Dialog v-model:open="showEditDialog">
                <DialogContent class="max-w-md">
                    <DialogHeader>
                        <DialogTitle>Edit Department</DialogTitle>
                        <DialogDescription>Update department details</DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="handleEditSubmit" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="edit-code">Code</Label>
                            <Input
                                id="edit-code"
                                v-model="editForm.code"
                                placeholder="e.g., CSC"
                                required
                            />
                            <InputError :message="editForm.errors.code" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit-name">Name</Label>
                            <Input
                                id="edit-name"
                                v-model="editForm.name"
                                placeholder="e.g., Computer Science"
                                required
                            />
                            <InputError :message="editForm.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="edit-school">School</Label>
                            <Input
                                id="edit-school"
                                v-model="editForm.school"
                                placeholder="e.g., SOC"
                            />
                            <InputError :message="editForm.errors.school" />
                        </div>
                        <DialogFooter>
                            <Button type="submit" :disabled="editForm.processing">
                                {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <Dialog v-model:open="showDeleteDialog">
                <DialogContent class="max-w-sm">
                    <DialogHeader>
                        <DialogTitle>Delete Department</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to delete "{{ deletingDepartment?.name }}"? This action cannot be undone.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter class="gap-2">
                        <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
                        <Button variant="destructive" @click="handleDelete">Delete</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
