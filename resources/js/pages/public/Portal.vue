<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Search, UserPlus, Save, CheckCircle2, AlertCircle, RefreshCw } from 'lucide-vue-next';
import { type Tenure, type Department } from '@/types';

interface Props {
    tenures: Tenure[];
    departments: Department[];
}

const props = defineProps<Props>();
const page = usePage<any>();

// State
const mode = ref<'lookup' | 'update' | 'create'>('lookup');
const matchedAlumnus = ref<any>(null);

// Forms
const lookupForm = useForm({
    name: '',
    email: '',
    phone: '',
});

const updateForm = useForm({
    name: '',
    email: '',
    phones: [''],
    tenure_id: '',
    department_id: '',
    current_location: '',
    current_employer: '',
    state: '',
    unit: '',
    gender: '',
});

const createForm = useForm({
    name: '',
    email: '',
    phones: [''],
    tenure_id: '',
    department_id: '',
    current_location: '',
    current_employer: '',
    state: '',
    unit: '',
    gender: '',
});

// Computed from session
const successMessage = computed(() => page.props.flash?.success);
const matchFound = computed(() => page.props.flash?.match);
const noMatch = computed(() => page.props.flash?.no_match);

// Check for session flash data on load/update
console.log('Session Flash:', page.props.flash);

// Watch for flash messages to handle lookup results
watch(() => page.props.flash, (flash: any) => {
    if (flash?.match) {
        matchedAlumnus.value = flash.match;
        mode.value = 'update';
        
        // Populate update form
        const m = matchedAlumnus.value;
        updateForm.name = m.name;
        updateForm.email = m.email;
        updateForm.phones = m.phones && m.phones.length > 0 ? [...m.phones] : [''];
        updateForm.tenure_id = String(m.tenure_id || '');
        updateForm.department_id = String(m.department_id || '');
        updateForm.current_location = m.current_location || '';
        updateForm.current_employer = m.current_employer || '';
        updateForm.state = m.state || '';
        updateForm.unit = m.unit || '';
        updateForm.gender = m.gender || '';
    } 
    
    if (flash?.no_match) {
        mode.value = 'create';
    }
}, { deep: true, immediate: true });

function handleLookup() {
    lookupForm.post('/portal/lookup');
}

function handleUpdate() {
    updateForm.post(`/portal/update/${matchedAlumnus.value.id}`);
}

function handleCreate() {
    createForm.post('/portal/submit');
}

function addPhone(form: any) {
    form.phones.push('');
}

function removePhone(form: any, index: number) {
    form.phones.splice(index, 1);
}

function resetToLookup() {
    mode.value = 'lookup';
    lookupForm.reset();
    matchedAlumnus.value = null;
    // Clear flash manually if possible or just rely on new navigation
    // router.visit('/portal') // Clean reload
}
</script>

<template>
    <PublicLayout>
        <Head title="Alumni Portal" />

        <div class="max-w-3xl mx-auto space-y-8">
            <div class="text-center space-y-4">
                <h1 class="text-4xl font-extrabold tracking-tight lg:text-5xl">
                    Stay Connected
                </h1>
                <p class="text-xl text-muted-foreground">
                    Help us keep our records up to date. Search for your profile to update your details or add yourself to the network.
                </p>
            </div>

            <!-- Success Message -->
            <Alert v-if="successMessage" class="bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
                <CheckCircle2 class="h-4 w-4" />
                <AlertTitle>Success!</AlertTitle>
                <AlertDescription>{{ successMessage }}</AlertDescription>
                <div class="mt-4">
                    <Button variant="outline" size="sm" @click="resetToLookup" class="bg-transparent border-green-300 dark:border-green-700 hover:bg-green-100 dark:hover:bg-green-900/40">
                        Back to Search
                    </Button>
                </div>
            </Alert>

            <!-- Step 1: Lookup Form -->
            <Card v-if="mode === 'lookup' && !successMessage">
                <CardHeader>
                    <CardTitle>Find Your Record</CardTitle>
                    <CardDescription>
                        Enter your name and either email or phone number to look up your existing record.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="handleLookup" class="space-y-4">
                        <Alert v-if="noMatch" variant="destructive" class="mb-4">
                            <AlertCircle class="h-4 w-4" />
                            <AlertTitle>Record Not Found</AlertTitle>
                            <AlertDescription>
                                We couldn't find a record matching those details. You can recreate your search or 
                                <button type="button" @click="mode = 'create'" class="underline font-bold hover:text-white">
                                    create a new record
                                </button>.
                            </AlertDescription>
                        </Alert>

                        <div class="space-y-2">
                            <Label for="lookup_name">Full Name (Required)</Label>
                            <Input id="lookup_name" v-model="lookupForm.name" required placeholder="e.g. John Doe" />
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="lookup_email">Email Address</Label>
                                <Input id="lookup_email" type="email" v-model="lookupForm.email" placeholder="name@example.com" />
                            </div>
                            <div class="space-y-2">
                                <Label for="lookup_phone">Phone Number</Label>
                                <Input id="lookup_phone" v-model="lookupForm.phone" placeholder="+1234567890" />
                            </div>
                        </div>
                        
                        <p class="text-sm text-muted-foreground">
                            * Provide at least Name + (Email OR Phone) for best results.
                        </p>

                        <Button type="submit" class="w-full" :disabled="lookupForm.processing">
                            <Search class="mr-2 h-4 w-4" />
                            {{ lookupForm.processing ? 'Searching...' : 'Search Record' }}
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <!-- Step 2A: Update Form -->
            <Card v-if="mode === 'update' && !successMessage">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Update Your Information</CardTitle>
                            <CardDescription>
                                Found record for <strong class="text-primary">{{ matchedAlumnus.name }}</strong>. 
                                Updates will be reviewed by an administrator.
                            </CardDescription>
                        </div>
                        <Button variant="ghost" size="sm" @click="resetToLookup">
                            <RefreshCw class="mr-2 h-4 w-4" />
                            Not you?
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="handleUpdate" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Full Name</Label>
                                <Input v-model="updateForm.name" required />
                            </div>
                            <div class="space-y-2">
                                <Label>Email</Label>
                                <Input type="email" v-model="updateForm.email" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Phone Numbers</Label>
                            <div v-for="(phone, index) in updateForm.phones" :key="index" class="flex gap-2">
                                <Input v-model="updateForm.phones[index]" placeholder="Phone Number" />
                                <Button type="button" variant="outline" size="icon" @click="removePhone(updateForm, index)" v-if="updateForm.phones.length > 1">
                                    <span class="sr-only">Remove</span>
                                    <span aria-hidden="true">&times;</span>
                                </Button>
                            </div>
                            <Button type="button" variant="outline" size="sm" @click="addPhone(updateForm)" class="mt-1">
                                + Add Another Phone
                            </Button>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Tenure Year</Label>
                                <Select v-model="updateForm.tenure_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Tenure" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="t in tenures" :key="t.id" :value="String(t.id)">
                                            {{ t.name }} ({{ t.year }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Department</Label>
                                <Select v-model="updateForm.department_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Department" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="d in departments" :key="d.id" :value="String(d.id)">
                                            {{ d.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Current Location</Label>
                                <Input v-model="updateForm.current_location" placeholder="City, Country" />
                            </div>
                            <div class="space-y-2">
                                <Label>Current Employer</Label>
                                <Input v-model="updateForm.current_employer" placeholder="Company Name" />
                            </div>
                        </div>

                         <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label>State</Label>
                                <Input v-model="updateForm.state" />
                            </div>
                            <div class="space-y-2">
                                <Label>Unit</Label>
                                <Input v-model="updateForm.unit" />
                            </div>
                            <div class="space-y-2">
                                <Label>Gender</Label>
                                <Select v-model="updateForm.gender">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Gender" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="male">Male</SelectItem>
                                        <SelectItem value="female">Female</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <Button type="button" variant="outline" @click="resetToLookup">Cancel</Button>
                            <Button type="submit" :disabled="updateForm.processing">
                                <Save class="mr-2 h-4 w-4" />
                                Submit Updates
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Step 2B: Create Form -->
            <Card v-if="mode === 'create' && !successMessage">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Create New Record</CardTitle>
                            <CardDescription>
                                Join the alumni network by submitting your details below.
                            </CardDescription>
                        </div>
                        <Button variant="ghost" size="sm" @click="resetToLookup">
                            <RefreshCw class="mr-2 h-4 w-4" />
                            Back to Search
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="handleCreate" class="space-y-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Full Name *</Label>
                                <Input v-model="createForm.name" required />
                            </div>
                            <div class="space-y-2">
                                <Label>Email</Label>
                                <Input type="email" v-model="createForm.email" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label>Phone Numbers</Label>
                            <div v-for="(phone, index) in createForm.phones" :key="index" class="flex gap-2">
                                <Input v-model="createForm.phones[index]" placeholder="Phone Number" />
                                <Button type="button" variant="outline" size="icon" @click="removePhone(createForm, index)" v-if="createForm.phones.length > 1">
                                    <span class="sr-only">Remove</span>
                                    <span aria-hidden="true">&times;</span>
                                </Button>
                            </div>
                            <Button type="button" variant="outline" size="sm" @click="addPhone(createForm)" class="mt-1">
                                + Add Another Phone
                            </Button>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Tenure Year *</Label>
                                <Select v-model="createForm.tenure_id" required>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Tenure" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="t in tenures" :key="t.id" :value="String(t.id)">
                                            {{ t.name }} ({{ t.year }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Department</Label>
                                <Select v-model="createForm.department_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Department" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="d in departments" :key="d.id" :value="String(d.id)">
                                            {{ d.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Current Location</Label>
                                <Input v-model="createForm.current_location" placeholder="City, Country" />
                            </div>
                            <div class="space-y-2">
                                <Label>Current Employer</Label>
                                <Input v-model="createForm.current_employer" placeholder="Company Name" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label>State</Label>
                                <Input v-model="createForm.state" />
                            </div>
                            <div class="space-y-2">
                                <Label>Unit</Label>
                                <Input v-model="createForm.unit" />
                            </div>
                            <div class="space-y-2">
                                <Label>Gender</Label>
                                <Select v-model="createForm.gender">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Gender" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="male">Male</SelectItem>
                                        <SelectItem value="female">Female</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <Button type="button" variant="outline" @click="resetToLookup">Cancel</Button>
                            <Button type="submit" :disabled="createForm.processing">
                                <UserPlus class="mr-2 h-4 w-4" />
                                Create Record
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </PublicLayout>
</template>
