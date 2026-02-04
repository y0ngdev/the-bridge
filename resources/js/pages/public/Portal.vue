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
import { Checkbox } from '@/components/ui/checkbox';
import Combobox from '@/components/Combobox.vue';

interface Props {
    tenures: Tenure[];
    departments: Department[];
    states: { value: string; label: string }[];
    units: { value: string; label: string }[];
    pastExcoOffices: { value: string; label: string }[];
}

const props = defineProps<Props>();
const page = usePage<any>();

const tenureOptions = computed(() => props.tenures.map(t => ({
    value: String(t.id),
    label: `${t.name} (${t.year})`
})));

const departmentOptions = computed(() => props.departments.map(d => ({
    value: String(d.id),
    label: d.name
})));

const pastExcoOfficeOptions = computed(() => props.pastExcoOffices);

// State
const mode = ref<'lookup' | 'disambiguation' | 'update' | 'create'>('lookup');
const matchedAlumnus = ref<any>(null);
const possibleMatches = ref<any[]>([]);

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
    address: '',
    current_employer: '',
    state: '',
    unit: '',
    gender: '',
    birth_date: '',
    past_exco_office: '',
    current_exco_office: '',
    is_futa_staff: false,
    marital_status: '',
    occupation: '',
});

const createForm = useForm({
    name: '',
    email: '',
    phones: [''],
    tenure_id: '',
    department_id: '',
    address: '',
    current_employer: '',
    state: '',
    unit: '',
    gender: '',
    birth_date: '',
    past_exco_office: '',
    current_exco_office: '',
    is_futa_staff: false,
    marital_status: '',
    occupation: '',
});

// Computed from session
const successMessage = computed(() => page.props.flash?.success);
const matchFound = computed(() => page.props.flash?.match);
const noMatch = computed(() => page.props.flash?.no_match);

// Check for session flash data on load/update
// console.log('Session Flash:', page.props.flash);

// Watch for flash messages to handle lookup results
watch(() => page.props.flash, (flash: any) => {
    if (flash?.possible_matches) {
        mode.value = 'disambiguation';
        possibleMatches.value = flash.possible_matches;
    }

    if (flash?.match) {
        selectMatch(flash.match);
    } 
    
    if (flash?.no_match) {
        proceedToCreate();
    }
}, { deep: true, immediate: true });

function selectMatch(alumnus: any) {
    matchedAlumnus.value = alumnus;
    mode.value = 'update';
    
    // Populate update form
    const m = matchedAlumnus.value;
    updateForm.name = m.name;
    updateForm.email = m.email; // Keep DB email by default
    updateForm.phones = m.phones && m.phones.length > 0 ? [...m.phones] : [''];
    
    // PRESERVE INPUT: If user searched with a phone number that isn't in the list, add it
    if (lookupForm.phone) {
        // Normalize for comparison (basic strip)
        const normalize = (p: string) => p.replace(/\D/g, '');
        const searchedPhone = normalize(lookupForm.phone);
        const exists = updateForm.phones.some((p: string) => normalize(p) === searchedPhone);
        
        if (!exists && searchedPhone.length > 0) {
            // Append the new phone number from search
            updateForm.phones.push(lookupForm.phone);
        }
    }

    // PRESERVE INPUT: If user searched with diverse email and DB is empty, fill it
    if (lookupForm.email && !updateForm.email) {
        updateForm.email = lookupForm.email;
    }

    updateForm.tenure_id = String(m.tenure_id || '');
    updateForm.department_id = String(m.department_id || '');
    updateForm.address = m.address || '';
    updateForm.current_employer = m.current_employer || '';
    updateForm.state = m.state || '';
    updateForm.unit = m.unit || '';
    updateForm.gender = m.gender || '';
    updateForm.birth_date = m.birth_date || '';
    updateForm.past_exco_office = m.past_exco_office || '';
    updateForm.current_exco_office = m.current_exco_office || '';
    updateForm.is_futa_staff = Boolean(m.is_futa_staff);
    updateForm.marital_status = m.marital_status || '';
    updateForm.occupation = m.occupation || '';
}

function proceedToCreate() {
    mode.value = 'create';
    
    // Prefill create form with lookup data
    createForm.name = lookupForm.name;
    createForm.email = lookupForm.email;
    if (lookupForm.phone) {
        createForm.phones = [lookupForm.phone];
    }
}

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
    possibleMatches.value = [];
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
                        <Alert v-if="lookupForm.hasErrors" variant="destructive" class="mb-4">
                            <AlertCircle class="h-4 w-4" />
                            <AlertTitle>Validation Error</AlertTitle>
                            <AlertDescription>
                                Please fix the errors highlighted below.
                            </AlertDescription>
                        </Alert>

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
                            <p v-if="lookupForm.errors.name" class="text-sm text-destructive font-medium">
                                {{ lookupForm.errors.name }}
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="lookup_email">Email Address</Label>
                                <Input id="lookup_email" type="email" v-model="lookupForm.email" placeholder="name@example.com" />
                                <p v-if="lookupForm.errors.email" class="text-sm text-destructive font-medium">
                                    {{ lookupForm.errors.email }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="lookup_phone">Phone Number</Label>
                                <Input id="lookup_phone" v-model="lookupForm.phone" placeholder="+1234567890" />
                                <p v-if="lookupForm.errors.phone" class="text-sm text-destructive font-medium">
                                    {{ lookupForm.errors.phone }}
                                </p>
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

            <!-- Step 1.5: Disambiguation (Multiple Matches) -->
            <Card v-if="mode === 'disambiguation' && !successMessage">
                <CardHeader>
                    <CardTitle>Multiple Records Found</CardTitle>
                    <CardDescription>
                        We found multiple alumni details matching your search. Please identify which one is you.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4">
                    <div v-for="match in possibleMatches" :key="match.id" 
                        class="flex items-center justify-between p-4 border rounded-lg hover:bg-slate-50 dark:hover:bg-slate-900 cursor-pointer transition-colors"
                        @click="selectMatch(match)"
                    >
                        <div class="space-y-1">
                            <p class="font-medium">{{ match.name }}</p>
                            <p class="text-sm text-muted-foreground">
                                <span v-if="match.department">{{ match.department.name }}</span>
                                <span v-if="match.department && match.tenure"> • </span>
                                <span v-if="match.tenure">{{ match.tenure.name }}</span>
                            </p>
                        </div>
                        <Button size="sm" variant="secondary">This is me</Button>
                    </div>

                    <div class="relative py-2">
                        <div class="absolute inset-0 flex items-center"><span class="w-full border-t" /></div>
                        <div class="relative flex justify-center text-xs uppercase"><span class="bg-background px-2 text-muted-foreground">Or</span></div>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-muted-foreground mb-2">None of these are me?</p>
                        <Button variant="outline" class="w-full" @click="proceedToCreate">
                            Create a new record
                        </Button>
                    </div>
                </CardContent>
                <CardFooter>
                     <Button variant="ghost" size="sm" class="w-full" @click="resetToLookup">
                        <RefreshCw class="mr-2 h-4 w-4" />
                        Back to Search
                    </Button>
                </CardFooter>
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
                                <Combobox 
                                    v-model="updateForm.tenure_id" 
                                    :options="tenureOptions"
                                    placeholder="Select Tenure"
                                    search-placeholder="Search tenure..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Department</Label>
                                <Combobox 
                                    v-model="updateForm.department_id" 
                                    :options="departmentOptions"
                                    placeholder="Select Department"
                                    search-placeholder="Search department..."
                                />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Address</Label>
                                <Input v-model="updateForm.address" placeholder="Residential Address" />
                            </div>
                            <div class="space-y-2">
                                <Label>Current Employer</Label>
                                <Input v-model="updateForm.current_employer" placeholder="Company Name" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Occupation</Label>
                                <Input v-model="updateForm.occupation" placeholder="Your Occupation" />
                            </div>
                            <div class="space-y-2">
                                <Label>Birth Date</Label>
                                <Input type="date" v-model="updateForm.birth_date" />
                            </div>
                        </div>

                         <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label>State</Label>
                                <Combobox 
                                    v-model="updateForm.state" 
                                    :options="states"
                                    placeholder="Select State"
                                    search-placeholder="Search state..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Unit</Label>
                                <Combobox 
                                    v-model="updateForm.unit" 
                                    :options="units"
                                    placeholder="Select Unit"
                                    search-placeholder="Search unit..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Gender</Label>
                                <Select v-model="updateForm.gender">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Gender" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="M">Male</SelectItem>
                                        <SelectItem value="F">Female</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                             <div class="space-y-2">
                                <Label>Past Exco Office</Label>
                                <Combobox 
                                    v-model="updateForm.past_exco_office" 
                                    :options="pastExcoOfficeOptions"
                                    placeholder="Select Exco Office"
                                    search-placeholder="Search office..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Current Exco Office</Label>
                                <Input v-model="updateForm.current_exco_office" placeholder="If applicable" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Marital Status</Label>
                                <Select v-model="updateForm.marital_status">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Single">Single</SelectItem>
                                        <SelectItem value="Married">Married</SelectItem>
                                        <SelectItem value="Divorced">Divorced</SelectItem>
                                        <SelectItem value="Widowed">Widowed</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="flex items-end space-x-2 pb-2">
                                <Checkbox id="is_futa_staff" :checked="updateForm.is_futa_staff" @update:checked="(v: boolean) => updateForm.is_futa_staff = v" />
                                <Label for="is_futa_staff" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    I am currently a FUTA Staff
                                </Label>
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
                                <Combobox 
                                    v-model="createForm.tenure_id" 
                                    :options="tenureOptions"
                                    placeholder="Select Tenure"
                                    search-placeholder="Search tenure..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Department</Label>
                                <Combobox 
                                    v-model="createForm.department_id" 
                                    :options="departmentOptions"
                                    placeholder="Select Department"
                                    search-placeholder="Search department..."
                                />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Address</Label>
                                <Input v-model="createForm.address" placeholder="Residential Address" />
                            </div>
                            <div class="space-y-2">
                                <Label>Current Employer</Label>
                                <Input v-model="createForm.current_employer" placeholder="Company Name" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Occupation</Label>
                                <Input v-model="createForm.occupation" placeholder="Your Occupation" />
                            </div>
                            <div class="space-y-2">
                                <Label>Birth Date</Label>
                                <Input type="date" v-model="createForm.birth_date" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label>State</Label>
                                <Combobox 
                                    v-model="createForm.state" 
                                    :options="states"
                                    placeholder="Select State"
                                    search-placeholder="Search state..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Unit</Label>
                                <Combobox 
                                    v-model="createForm.unit" 
                                    :options="units"
                                    placeholder="Select Unit"
                                    search-placeholder="Search unit..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Gender</Label>
                                <Select v-model="createForm.gender">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Gender" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="M">Male</SelectItem>
                                        <SelectItem value="F">Female</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                         <div class="grid gap-4 md:grid-cols-2">
                             <div class="space-y-2">
                                <Label>Past Exco Office</Label>
                                <Combobox 
                                    v-model="createForm.past_exco_office" 
                                    :options="pastExcoOfficeOptions"
                                    placeholder="Select Exco Office"
                                    search-placeholder="Search office..."
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Current Exco Office</Label>
                                <Input v-model="createForm.current_exco_office" placeholder="If applicable" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Marital Status</Label>
                                <Select v-model="createForm.marital_status">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Single">Single</SelectItem>
                                        <SelectItem value="Married">Married</SelectItem>
                                        <SelectItem value="Divorced">Divorced</SelectItem>
                                        <SelectItem value="Widowed">Widowed</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                             <div class="flex items-end space-x-2 pb-2">
                                <Checkbox id="create_is_futa_staff" :checked="createForm.is_futa_staff" @update:checked="(v: boolean) => createForm.is_futa_staff = v" />
                                <Label for="create_is_futa_staff" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    I am currently a FUTA Staff
                                </Label>
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
