<script setup lang="ts">
import { index, store, update } from '@/actions/App/Http/Controllers/TenureController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Edit, Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

interface Tenure {
    id: number;
    name: string;
    year: string;
}

interface PaginatedTenures {
    data: Tenure[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    current_page: number;
    last_page: number;
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
}

defineProps<{
    tenures: PaginatedTenures;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tenures', href: index().url },
];

const goToPage = (page: number) => {
    router.get(index().url, { page }, { preserveState: true, preserveScroll: true });
};

const addForm = useForm({
    name: '',
    year: '',
});
const editForm = useForm({
    name: '',
    year: '',
});
const showAddDialog = ref(false);
const showEditDialog = ref(false);

const editingTenure = ref<Tenure | null>(null);

function handleAddSubmit() {
    addForm.post(store().url, {
        onSuccess: () => {
            showAddDialog.value = false;
            addForm.reset();
            toast.success('Tenure created');
        },
    });
}

const openEditDialog = (tenure: Tenure) => {
    editingTenure.value = tenure;
    editForm.name = tenure.name;
    editForm.year = tenure.year;

    showEditDialog.value = true;
};

function handleEditSubmit() {
    if (!editingTenure.value) return;
    editForm.put(update(editingTenure.value.id).url, {
        onSuccess: () => {
            showEditDialog.value = false;
            editForm.reset();
            toast.success('Tenure record updated');
        },
    });
}
</script>

<template>
    <Head title="Tenures" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="mb-6 flex items-center justify-between">
                <HeadingSmall title="Tenures" description="Manage tenure records" />

                <Dialog v-model:open="showAddDialog">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Add Tenure
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>Add New Tenure</DialogTitle>
                            <DialogDescription>Add a new tenure record</DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="handleAddSubmit" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="space-y-2">
                                    <Label for="name">Tenure Name</Label>
                                    <Input
                                        id="name"
                                        v-model="addForm.name"
                                        type="text"
                                        placeholder="e.g., Servants of Christ"
                                        :class="addForm.errors.name && 'border-destructive'"
                                        required
                                    />

                                    <InputError :message="addForm.errors.name" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="space-y-2">
                                    <Label for="year">Year</Label>
                                    <Input
                                        id="year"
                                        v-model="addForm.year"
                                        type="text"
                                        placeholder="e.g., 2015-2016"
                                        :class="addForm.errors.year && 'border-destructive'"
                                        required
                                    />
                                    <InputError :message="addForm.errors.year" />
                                </div>
                            </div>

                            <DialogFooter>
                                <DialogClose as-child>
                                    <Button variant="outline" type="button">Cancel</Button>
                                </DialogClose>
                                <Button type="submit" :disabled="addForm.processing">
                                    {{ addForm.processing ? 'Creating...' : 'Create Tenure' }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Year</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="tenure in tenures.data" :key="tenure.id">
                            <TableCell class="font-medium">
                                {{ tenure.name || '—' }}
                            </TableCell>
                            <TableCell>{{ tenure.year }}</TableCell>
                            <TableCell class="text-right">
                                <Button variant="outline" size="sm" @click="openEditDialog(tenure)">
                                    <Edit class="h-4 w-4" />
                                    Edit
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="tenures.data.length === 0">
                            <TableCell colspan="3" class="py-8 text-center text-muted-foreground">
                                No tenures found. Create your first one!
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <Pagination
                v-if="tenures.last_page > 1"
                :total="tenures.last_page * 15"
                :items-per-page="15"
                :default-page="tenures.current_page"
                :sibling-count="1"
                show-edges
                class="mt-4"
                @update:page="goToPage"
            >
                <PaginationContent v-slot="{ items }" class="gap-4">
                    <PaginationFirst />
                    <PaginationPrevious />

                    <template v-for="(item, idx) in items" :key="idx">
                        <PaginationItem v-if="item.type === 'page'" :value="item.value" as-child>
                            <Button :variant="item.value === tenures.current_page ? 'default' : 'outline'" size="icon">
                                {{ item.value }}
                            </Button>
                        </PaginationItem>
                        <PaginationEllipsis v-else :index="idx" />
                    </template>

                    <PaginationNext />
                    <PaginationLast />
                </PaginationContent>
            </Pagination>

            <Dialog v-model:open="showEditDialog">
                <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Edit Tenure</DialogTitle>
                        <DialogDescription>Update tenure information</DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="handleEditSubmit" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-2">
                                <Label for="name">Tenure Name</Label>
                                <Input
                                    id="name"
                                    v-model="editForm.name"
                                    type="text"
                                    placeholder="e.g., Servants of Christ"
                                    :class="editForm.errors.name && 'border-destructive'"
                                    required
                                />

                                <InputError :message="editForm.errors.name" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-2">
                                <Label for="year">Year</Label>
                                <Input
                                    id="year"
                                    v-model="editForm.year"
                                    type="text"
                                    placeholder="e.g., 2015-2016"
                                    :class="editForm.errors.year && 'border-destructive'"
                                    required
                                />
                                <InputError :message="editForm.errors.year" />
                            </div>
                        </div>

                        <DialogFooter>
                            <DialogClose as-child>
                                <Button variant="outline" type="button">Cancel</Button>
                            </DialogClose>
                            <Button type="submit" :disabled="editForm.processing">
                                {{ editForm.processing ? 'Updating...' : 'Update record' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
