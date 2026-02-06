<script setup lang="ts">
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Shield, Trash2, UserPlus } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    users: User[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboardIndex().url },
    { title: 'User Management', href: '#' },
];

const showAddDialog = ref(false);
const showEditDialog = ref(false);
const showDeleteDialog = ref(false);
const selectedUser = ref<User | null>(null);

const addForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
});

const editForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
});

function openAddDialog() {
    addForm.reset();
    showAddDialog.value = true;
}

function openEditDialog(user: User) {
    selectedUser.value = user;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.password = '';
    editForm.password_confirmation = '';
    editForm.is_admin = user.is_admin ?? false;
    showEditDialog.value = true;
}

function openDeleteDialog(user: User) {
    selectedUser.value = user;
    showDeleteDialog.value = true;
}

function handleAdd() {
    addForm.post('/admin/users', {
        onSuccess: () => {
            showAddDialog.value = false;
            addForm.reset();
        },
    });
}

function handleEdit() {
    if (!selectedUser.value) return;
    editForm.put(`/admin/users/${selectedUser.value.id}`, {
        onSuccess: () => {
            showEditDialog.value = false;
            selectedUser.value = null;
        },
    });
}

function handleDelete() {
    if (!selectedUser.value) return;
    editForm.delete(`/admin/users/${selectedUser.value.id}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            selectedUser.value = null;
        },
    });
}
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="mb-6 flex items-center justify-between">
                <HeadingSmall title="User Management" description="Manage user accounts and permissions" />
                <Button @click="openAddDialog">
                    <UserPlus class="mr-2 h-4 w-4" />
                    Add User
                </Button>
            </div>

            <Card>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead>Created</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in users" :key="user.id">
                                <TableCell class="font-medium">{{ user.name }}</TableCell>
                                <TableCell>{{ user.email }}</TableCell>
                                <TableCell>
                                    <Badge v-if="user.is_admin" variant="default">
                                        <Shield class="mr-1 h-3 w-3" />
                                        Admin
                                    </Badge>
                                    <Badge v-else variant="secondary">Member</Badge>
                                </TableCell>
                                <TableCell>{{ new Date(user.created_at).toLocaleDateString() }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="icon" @click="openEditDialog(user)">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon" @click="openDeleteDialog(user)">
                                            <Trash2 class="h-4 w-4 text-destructive" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>

        <!-- Add User Dialog -->
        <Dialog v-model:open="showAddDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Add New User</DialogTitle>
                    <DialogDescription>Create a new user account.</DialogDescription>
                </DialogHeader>

                <form @submit.prevent="handleAdd" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="add_name">Name</Label>
                        <Input id="add_name" v-model="addForm.name" required :class="addForm.errors.name && 'border-destructive'" />
                        <p v-if="addForm.errors.name" class="text-sm text-destructive">{{ addForm.errors.name }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="add_email">Email</Label>
                        <Input id="add_email" type="email" v-model="addForm.email" required :class="addForm.errors.email && 'border-destructive'" />
                        <p v-if="addForm.errors.email" class="text-sm text-destructive">{{ addForm.errors.email }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="add_password">Password</Label>
                        <Input
                            id="add_password"
                            type="password"
                            v-model="addForm.password"
                            required
                            :class="addForm.errors.password && 'border-destructive'"
                        />
                        <p v-if="addForm.errors.password" class="text-sm text-destructive">{{ addForm.errors.password }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="add_password_confirm">Confirm Password</Label>
                        <Input id="add_password_confirm" type="password" v-model="addForm.password_confirmation" required />
                    </div>
                    <div class="flex items-center space-x-2">
                        <Checkbox id="add_is_admin" v-model:checked="addForm.is_admin" />
                        <Label for="add_is_admin" class="cursor-pointer text-sm font-normal">Administrator</Label>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showAddDialog = false">Cancel</Button>
                        <Button type="submit" :disabled="addForm.processing">Create User</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Edit User Dialog -->
        <Dialog v-model:open="showEditDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Edit User</DialogTitle>
                    <DialogDescription>Update user account details.</DialogDescription>
                </DialogHeader>

                <form @submit.prevent="handleEdit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="edit_name">Name</Label>
                        <Input id="edit_name" v-model="editForm.name" required :class="editForm.errors.name && 'border-destructive'" />
                        <p v-if="editForm.errors.name" class="text-sm text-destructive">{{ editForm.errors.name }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="edit_email">Email</Label>
                        <Input
                            id="edit_email"
                            type="email"
                            v-model="editForm.email"
                            required
                            :class="editForm.errors.email && 'border-destructive'"
                        />
                        <p v-if="editForm.errors.email" class="text-sm text-destructive">{{ editForm.errors.email }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="edit_password">New Password (leave blank to keep current)</Label>
                        <Input
                            id="edit_password"
                            type="password"
                            v-model="editForm.password"
                            :class="editForm.errors.password && 'border-destructive'"
                        />
                        <p v-if="editForm.errors.password" class="text-sm text-destructive">{{ editForm.errors.password }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="edit_password_confirm">Confirm New Password</Label>
                        <Input id="edit_password_confirm" type="password" v-model="editForm.password_confirmation" />
                    </div>
                    <div class="flex items-center space-x-2">
                        <Checkbox id="edit_is_admin" v-model:checked="editForm.is_admin" />
                        <Label for="edit_is_admin" class="cursor-pointer text-sm font-normal">Administrator</Label>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showEditDialog = false">Cancel</Button>
                        <Button type="submit" :disabled="editForm.processing">Save Changes</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Delete User</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete <strong>{{ selectedUser?.name }}</strong
                        >? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
                    <Button variant="destructive" @click="handleDelete">Delete User</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
