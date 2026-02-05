<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
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

import { isValidPhoneNumber } from 'libphonenumber-js';

// ... (keep text empty line or previous imports)

function validatePhones(form: any): boolean {
    let isValid = true;
    // Clear only phone-related errors, not all errors
    form.clearErrors('phones');
    form.phones.forEach((_: string, index: number) => {
        form.clearErrors(`phones.${index}`);
    });
    // Ensure at least one non-empty phone exists
    const nonEmptyPhones = form.phones.filter((p: string) => p && p.trim() !== '');
    if (nonEmptyPhones.length === 0) {
        form.setError('phones', 'At least one phone number is required.');
        isValid = false;
    }

    form.phones.forEach((phone: string, index: number) => {
        if (!phone || phone.trim() === '') return;

        // Use libphonenumber-js for robust validation
        // 'NG' is the default country if no international format (+234) is used
        if (!isValidPhoneNumber(phone, 'NG')) {
            form.setError(`phones.${index}`, 'Invalid phone number.');
            isValid = false;
        }
    });

    return isValid;
}

function scrollToFirstError() {
    setTimeout(() => {
        const firstError = document.querySelector('.text-destructive');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
             // Fallback to top of form if alert is valid
             const alert = document.querySelector('[role="alert"]');
             if (alert) alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, 100);
}

function handleUpdate() {
    if (!validatePhones(updateForm)) {
        scrollToFirstError();
        return;
    }
    updateForm.post(`/portal/update/${matchedAlumnus.value.id}`, {
        onError: () => scrollToFirstError(),
    });
}

function handleCreate() {
    if (!validatePhones(createForm)) {
        scrollToFirstError();
        return;
    }
    createForm.post('/portal/submit', {
        onError: () => scrollToFirstError(),
    });
}

function addPhone(form: any) {
    // Only add a new field if the last one is not empty (optional UX improvement, but sticking to basic for now)
    form.phones.push('');
}

function removePhone(form: any, index: number) {
    form.phones.splice(index, 1);
}

function resetToLookup() {
    if (successMessage.value) {
        router.get('/portal');
        return;
    }

    mode.value = 'lookup';
    lookupForm.reset();
    matchedAlumnus.value = null;
    possibleMatches.value = [];
}
</script>

<template>
    <PublicLayout>
        <Head title="Alumni Portal" />

        <div class="min-h-[80vh] flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="text-center space-y-4 mb-8 max-w-2xl">
                <h1 class="text-4xl font-extrabold tracking-tight lg:text-5xl bg-gradient-to-r from-primary to-blue-600 bg-clip-text text-transparent">
                    The Bridge
                </h1>
                <p class="text-xl text-muted-foreground animate-in fade-in slide-in-from-bottom-4 duration-700">
                    Reconnect with your  RCFFUTA.Update your records and stay in the loop.
                </p>
            </div>

            <!-- Transition Wrapper -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
                mode="out-in"
            >
                <!-- Success State -->
                <div v-if="successMessage" class="w-full max-w-md">
                    <Card class="border-green-200 bg-green-50 dark:bg-green-900/10 dark:border-green-800">
                        <CardContent class="pt-6 text-center space-y-4">
                            <div class="mx-auto rounded-full bg-green-100 dark:bg-green-900/20 p-3 w-12 h-12 flex items-center justify-center">
                                <CheckCircle2 class="h-6 w-6 text-green-600 dark:text-green-400" />
                            </div>
                            <h2 class="text-2xl font-bold text-green-800 dark:text-green-400">Success!</h2>
                            <p class="text-green-700 dark:text-green-300">{{ successMessage }}</p>
                            <Button class="w-full mt-4" @click="resetToLookup">Return to Home</Button>
                        </CardContent>
                    </Card>
                </div>

                <!-- Lookup Form -->
                <Card v-else-if="mode === 'lookup'" class="w-full max-w-md shadow-lg border-t-4 border-t-primary">
                    <CardHeader>
                        <CardTitle>Find Your Record</CardTitle>
                        <CardDescription>Search by name and email or phone.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="handleLookup" class="space-y-4">
                            <Alert v-if="lookupForm.hasErrors" variant="destructive">
                                <AlertCircle class="h-4 w-4" />
                                <AlertTitle>Check Details</AlertTitle>
                                <AlertDescription>Please fix the errors below.</AlertDescription>
                            </Alert>

                             <Alert v-if="noMatch" variant="destructive">
                                <AlertCircle class="h-4 w-4" />
                                <AlertTitle>No Record Found</AlertTitle>
                                <AlertDescription>
                                    We couldn't find you. 
                                    <button type="button" @click="proceedToCreate" class="underline font-bold">Create new?</button>
                                </AlertDescription>
                            </Alert>

                            <div class="space-y-2">
                                <Label>Full Name</Label>
                                <div class="relative">
                                    <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                                    <Input v-model="lookupForm.name" class="pl-9" placeholder="e.g. John Doe" required />
                                </div>
                                <p v-if="lookupForm.errors.name" class="text-destructive text-xs">{{ lookupForm.errors.name }}</p>
                            </div>

                            <div class="space-y-4 pt-2">
                                <div class="relative flex items-center">
                                    <span class="w-full border-t" />
                                    <span class="px-2 text-xs text-muted-foreground uppercase bg-background">And</span>
                                    <span class="w-full border-t" />
                                </div>
                                
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="space-y-2">
                                        <Label>Email</Label>
                                        <Input type="email" v-model="lookupForm.email" placeholder="john@example.com" />
                                        <p v-if="lookupForm.errors.email" class="text-destructive text-xs">{{ lookupForm.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Phone</Label>
                                        <Input v-model="lookupForm.phone" placeholder="080..." />
                                        <p v-if="lookupForm.errors.phone" class="text-destructive text-xs">{{ lookupForm.errors.phone }}</p>
                                    </div>
                                </div>
                            </div>

                            <Button type="submit" class="w-full mt-2" :disabled="lookupForm.processing" size="lg">
                                {{ lookupForm.processing ? 'Searching...' : 'Search Record' }}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- Disambiguation -->
                <Card v-else-if="mode === 'disambiguation'" class="w-full max-w-lg shadow-lg">
                    <CardHeader>
                        <CardTitle>Multiple Matches Found</CardTitle>
                        <CardDescription>Select your profile from the list below.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-3">
                         <div v-for="match in possibleMatches" :key="match.id" 
                            class="group flex items-center justify-between p-4 border rounded-xl hover:bg-accent/50 cursor-pointer transition-all hover:scale-[1.01] hover:shadow-sm"
                            @click="selectMatch(match)"
                        >
                            <div class="flex-1 min-w-0 pr-4">
                                <p class="font-bold text-lg truncate">{{ match.name }}</p>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 text-sm text-muted-foreground mt-1">
                                    <span class="flex items-center gap-1.5 truncat">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary/40 shrink-0"></span>
                                        <span class="truncate">{{ match.department?.name || 'No Dept' }}</span>
                                    </span>
                                    <span class="hidden sm:inline text-muted-foreground/30">•</span>
                                    <span class="flex items-center gap-1.5 truncate">
                                        <span class="truncate">{{ match.tenure?.name || 'Unknown Tenure' }}</span>
                                    </span>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-muted group-hover:bg-primary/10 text-muted-foreground group-hover:text-primary transition-colors">
                                <CheckCircle2 class="h-6 w-6" />
                            </div>
                        </div>
                        
                        <div class="text-center pt-4">
                            <Button variant="link" @click="proceedToCreate">None of these are me</Button>
                        </div>
                    </CardContent>
                </Card>

                 <!-- Update Form -->
                <Card v-else-if="mode === 'update'" class="w-full max-w-3xl shadow-xl">
                    <CardHeader class="border-b bg-muted/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Update Profile</CardTitle>
                                <CardDescription>Update your details for review.</CardDescription>
                            </div>
                            <Button variant="outline" size="sm" @click="resetToLookup">Cancel</Button>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6 md:p-8">
                        <form @submit.prevent="handleUpdate" class="space-y-6" novalidate>
                            <Alert v-if="updateForm.hasErrors" variant="destructive">
                                <AlertCircle class="h-4 w-4" />
                                <AlertTitle>Validation Error</AlertTitle>
                                <AlertDescription>Please fix the highlighted errors below.</AlertDescription>
                            </Alert>

                            <!-- Group 1: Personal Details -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold border-b pb-2">Personal Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label>Full Name *</Label>
                                        <Input v-model="updateForm.name" required />
                                        <p v-if="updateForm.errors.name" class="text-destructive text-xs">{{ updateForm.errors.name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Gender *</Label>
                                        <Select v-model="updateForm.gender" required>
                                            <SelectTrigger><SelectValue placeholder="Select Gender" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="M">Male</SelectItem>
                                                <SelectItem value="F">Female</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="updateForm.errors.gender" class="text-destructive text-xs">{{ updateForm.errors.gender }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Birth Date *</Label>
                                        <Input type="date" v-model="updateForm.birth_date" required />
                                        <p v-if="updateForm.errors.birth_date" class="text-destructive text-xs">{{ updateForm.errors.birth_date }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Marital Status</Label>
                                         <Select v-model="updateForm.marital_status">
                                            <SelectTrigger><SelectValue placeholder="Select Status" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Single">Single</SelectItem>
                                                <SelectItem value="Married">Married</SelectItem>
                                                <SelectItem value="Divorced">Divorced</SelectItem>
                                                <SelectItem value="Widowed">Widowed</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="updateForm.errors.marital_status" class="text-destructive text-xs">{{ updateForm.errors.marital_status }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 2: Contact Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold border-b pb-2">Contact Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label>Email</Label>
                                        <Input v-model="updateForm.email" type="email" />
                                        <p v-if="updateForm.errors.email" class="text-destructive text-xs">{{ updateForm.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <Label>Phone Numbers *</Label>
                                        <div v-for="(phone, index) in updateForm.phones" :key="index" class="space-y-1">
                                            <div class="flex gap-2">
                                                <Input v-model="updateForm.phones[index]" placeholder="e.g. 08012345678" />
                                                <Button type="button" variant="ghost" size="icon" @click="removePhone(updateForm, index)" v-if="updateForm.phones.length > 1">
                                                    <span class="sr-only">Delete</span>
                                                    <span aria-hidden="true">&times;</span>
                                                </Button>
                                            </div>
                                            <p v-if="updateForm.errors[`phones.${index}`]" class="text-destructive text-xs">{{ updateForm.errors[`phones.${index}`] }}</p>
                                        </div>
                                        <Button type="button" variant="outline" size="sm" class="mt-2" @click="addPhone(updateForm)">+ Add Phone</Button>
                                        <p v-if="updateForm.errors.phones" class="text-destructive text-xs">{{ updateForm.errors.phones }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Residential State</Label>
                                        <Combobox v-model="updateForm.state" :options="states" placeholder="Select State" />
                                        <p v-if="updateForm.errors.state" class="text-destructive text-xs">{{ updateForm.errors.state }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Address</Label>
                                        <Input v-model="updateForm.address" />
                                        <p v-if="updateForm.errors.address" class="text-destructive text-xs">{{ updateForm.errors.address }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 3: Academic & Professional -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold border-b pb-2">Academic & Professional</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label>Tenure Year *</Label>
                                        <Combobox v-model="updateForm.tenure_id" :options="tenureOptions" placeholder="Select Tenure" />
                                        <p v-if="updateForm.errors.tenure_id" class="text-destructive text-xs">{{ updateForm.errors.tenure_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Department</Label>
                                        <Combobox v-model="updateForm.department_id" :options="departmentOptions" placeholder="Select Department" />
                                        <p v-if="updateForm.errors.department_id" class="text-destructive text-xs">{{ updateForm.errors.department_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Current Employer</Label>
                                        <Input v-model="updateForm.current_employer" />
                                        <p v-if="updateForm.errors.current_employer" class="text-destructive text-xs">{{ updateForm.errors.current_employer }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Occupation</Label>
                                        <Input v-model="updateForm.occupation" />
                                        <p v-if="updateForm.errors.occupation" class="text-destructive text-xs">{{ updateForm.errors.occupation }}</p>
                                    </div>
                                    <div class="flex items-end pb-2 md:col-span-2">
                                        <div class="flex items-center space-x-2">
                                            <Checkbox id="update_is_futa_staff" :checked="updateForm.is_futa_staff" @update:checked="(v: boolean) => updateForm.is_futa_staff = v" />
                                            <Label for="update_is_futa_staff">I'm currently a FUTA Staff</Label>
                                        </div>
                                        <p v-if="updateForm.errors.is_futa_staff" class="text-destructive text-xs ml-2">{{ updateForm.errors.is_futa_staff }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 4: Association Details -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold border-b pb-2">Unit Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label>Unit</Label>
                                        <Combobox v-model="updateForm.unit" :options="units" placeholder="Select Unit" />
                                        <p v-if="updateForm.errors.unit" class="text-destructive text-xs">{{ updateForm.errors.unit }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Past Exco Office</Label>
                                        <Combobox v-model="updateForm.past_exco_office" :options="pastExcoOfficeOptions" placeholder="Select Office" />
                                        <p v-if="updateForm.errors.past_exco_office" class="text-destructive text-xs">{{ updateForm.errors.past_exco_office }}</p>
                                    </div>
                                   
                                </div>
                            </div>

                            <div class="flex justify-end pt-6">
                                <Button type="submit" size="lg" :disabled="updateForm.processing" class="w-full md:w-auto">
                                    Submit Updates
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                 <!-- Create Form -->
                <Card v-else-if="mode === 'create'" class="w-full max-w-3xl shadow-xl">
                    <CardHeader class="border-b bg-muted/30">
                         <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Join The Bridge</CardTitle>
                                <CardDescription>Create your alumni profile.</CardDescription>
                            </div>
                            <Button variant="ghost" @click="resetToLookup">Back</Button>
                        </div>
                    </CardHeader>
                    <CardContent class="p-6 md:p-8">
                         <form @submit.prevent="handleCreate" class="space-y-6" novalidate>
                            <Alert v-if="createForm.hasErrors" variant="destructive">
                                <AlertCircle class="h-4 w-4" />
                                <AlertTitle>Validation Error</AlertTitle>
                                <AlertDescription>Please fix the highlighted errors below.</AlertDescription>
                            </Alert>

                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-sm text-blue-800 dark:text-blue-300 mb-6">
                                Please fill in as much information as possible to help us keep in touch with you.
                            </div>
                             
                            <!-- Group 1: Personal Details -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold border-b pb-2">Personal Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label>Full Name *</Label>
                                        <Input v-model="createForm.name" required />
                                        <p v-if="createForm.errors.name" class="text-destructive text-xs">{{ createForm.errors.name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Gender *</Label>
                                        <Select v-model="createForm.gender" required>
                                            <SelectTrigger><SelectValue placeholder="Select Gender" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="M">Male</SelectItem>
                                                <SelectItem value="F">Female</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="createForm.errors.gender" class="text-destructive text-xs">{{ createForm.errors.gender }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Birth Date *</Label>
                                        <Input type="date" v-model="createForm.birth_date" required />
                                        <p v-if="createForm.errors.birth_date" class="text-destructive text-xs">{{ createForm.errors.birth_date }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Marital Status</Label>
                                         <Select v-model="createForm.marital_status">
                                            <SelectTrigger><SelectValue placeholder="Select Status" /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Single">Single</SelectItem>
                                                <SelectItem value="Married">Married</SelectItem>
                                                <SelectItem value="Divorced">Divorced</SelectItem>
                                                <SelectItem value="Widowed">Widowed</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="createForm.errors.marital_status" class="text-destructive text-xs">{{ createForm.errors.marital_status }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Group 2: Contact Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold border-b pb-2">Contact Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label>Email</Label>
                                        <Input v-model="createForm.email" type="email" />
                                        <p v-if="createForm.errors.email" class="text-destructive text-xs">{{ createForm.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <Label>Phone Numbers *</Label>
                                        <div v-for="(phone, index) in createForm.phones" :key="index" class="space-y-1">
                                            <div class="flex gap-2">
                                                <Input v-model="createForm.phones[index]" placeholder="e.g. 08012345678" />
                                                <Button type="button" variant="ghost" size="icon" @click="removePhone(createForm, index)" v-if="createForm.phones.length > 1">
                                                    <span class="sr-only">Delete</span>
                                                    <span aria-hidden="true">&times;</span>
                                                </Button>
                                            </div>
                                            <p v-if="createForm.errors[`phones.${index}`]" class="text-destructive text-xs">{{ createForm.errors[`phones.${index}`] }}</p>
                                        </div>
                                        <Button type="button" variant="outline" size="sm" class="mt-2" @click="addPhone(createForm)">+ Add Phone</Button>
                                        <p v-if="createForm.errors.phones" class="text-destructive text-xs">{{ createForm.errors.phones }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Residential State</Label>
                                        <Combobox v-model="createForm.state" :options="states" placeholder="Select State" />
                                        <p v-if="createForm.errors.state" class="text-destructive text-xs">{{ createForm.errors.state }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Address</Label>
                                        <Input v-model="createForm.address" />
                                        <p v-if="createForm.errors.address" class="text-destructive text-xs">{{ createForm.errors.address }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 3: Academic & Professional -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold border-b pb-2">Academic & Professional</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label>Tenure Year *</Label>
                                        <Combobox v-model="createForm.tenure_id" :options="tenureOptions" required placeholder="Select Tenure" />
                                        <p v-if="createForm.errors.tenure_id" class="text-destructive text-xs">{{ createForm.errors.tenure_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Department</Label>
                                        <Combobox v-model="createForm.department_id" :options="departmentOptions" placeholder="Select Department" />
                                        <p v-if="createForm.errors.department_id" class="text-destructive text-xs">{{ createForm.errors.department_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Current Employer</Label>
                                        <Input v-model="createForm.current_employer" />
                                        <p v-if="createForm.errors.current_employer" class="text-destructive text-xs">{{ createForm.errors.current_employer }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Occupation</Label>
                                        <Input v-model="createForm.occupation" />
                                        <p v-if="createForm.errors.occupation" class="text-destructive text-xs">{{ createForm.errors.occupation }}</p>
                                    </div>
                                    <div class="flex items-end pb-2 md:col-span-2">
                                         <div class="flex items-center space-x-2">
                                            <Checkbox id="create_is_futa_staff" :checked="createForm.is_futa_staff" @update:checked="(v: boolean) => createForm.is_futa_staff = v" />
                                            <Label for="create_is_futa_staff">I'm currently a FUTA Staff</Label>
                                        </div>
                                        <p v-if="createForm.errors.is_futa_staff" class="text-destructive text-xs ml-2">{{ createForm.errors.is_futa_staff }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Group 4: Association Details -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold border-b pb-2">Unit Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <Label>Unit</Label>
                                        <Combobox v-model="createForm.unit" :options="units" placeholder="Select Unit" />
                                        <p v-if="createForm.errors.unit" class="text-destructive text-xs">{{ createForm.errors.unit }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Past Exco Office</Label>
                                        <Combobox v-model="createForm.past_exco_office" :options="pastExcoOfficeOptions" placeholder="Select Office" />
                                        <p v-if="createForm.errors.past_exco_office" class="text-destructive text-xs">{{ createForm.errors.past_exco_office }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end pt-6">
                                <Button type="submit" size="lg" :disabled="createForm.processing" class="w-full md:w-auto">
                                    Join The Bridge
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </Transition>
        </div>
    </PublicLayout>
</template>
