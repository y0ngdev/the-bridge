<script setup lang="ts">
import Combobox from '@/components/Combobox.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { type Department, type Tenure } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, CheckCircle2, Search } from 'lucide-vue-next';
import { computed, ref, useTemplateRef, watch } from 'vue';

interface Props {
    tenures: Tenure[];
    departments: Department[];
    states: { value: string; label: string }[];
    units: { value: string; label: string }[];
    pastExcoOffices: { value: string; label: string }[];
}

const props = defineProps<Props>();
const page = usePage<any>();

const tenureOptions = computed(() =>
    props.tenures.map((t) => ({
        value: String(t.id),
        label: `${t.name} (${t.year})`,
    })),
);

const departmentOptions = computed(() =>
    props.departments.map((d) => ({
        value: String(d.id),
        label: d.name,
    })),
);

const pastExcoOfficeOptions = computed(() => props.pastExcoOffices);

// State
const mode = ref<'lookup' | 'disambiguation' | 'update' | 'create'>('lookup');
const matchedAlumnus = ref<any>(null);
const possibleMatches = ref<any[]>([]);

// Photo upload state
const updatePhotoPreview = ref<string | null>(null);
const createPhotoPreview = ref<string | null>(null);
const updateDragActive = ref(false);
const createDragActive = ref(false);
const updatePhotoInput = useTemplateRef<HTMLInputElement>('updatePhotoInput');
const createPhotoInput = useTemplateRef<HTMLInputElement>('createPhotoInput');

function processPhotoFile(file: File, form: any, previewRef: typeof updatePhotoPreview): boolean {
    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        form.setError('photo', 'Please upload a JPEG, PNG, or GIF image.');
        return false;
    }
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        form.setError('photo', 'Image must be less than 2MB.');
        return false;
    }
    form.clearErrors('photo');
    form.photo = file;
    previewRef.value = URL.createObjectURL(file);
    return true;
}

function handleUpdatePhotoChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        processPhotoFile(file, updateForm, updatePhotoPreview);
    }
}

function handleUpdateDrop(event: DragEvent) {
    event.preventDefault();
    updateDragActive.value = false;
    const file = event.dataTransfer?.files?.[0];
    if (file) {
        processPhotoFile(file, updateForm, updatePhotoPreview);
    }
}

function clearUpdatePhoto() {
    updateForm.photo = null;
    updatePhotoPreview.value = null;
}

function handleCreatePhotoChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        processPhotoFile(file, createForm, createPhotoPreview);
    }
}

function handleCreateDrop(event: DragEvent) {
    event.preventDefault();
    createDragActive.value = false;
    const file = event.dataTransfer?.files?.[0];
    if (file) {
        processPhotoFile(file, createForm, createPhotoPreview);
    }
}

function clearCreatePhoto() {
    createForm.photo = null;
    createPhotoPreview.value = null;
}

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
    photo: null as File | null,
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
    photo: null as File | null,
    is_futa_staff: false,
    marital_status: '',
    occupation: '',
});

// Computed from session
const successMessage = computed(() => page.props.flash?.success);
const noMatch = computed(() => page.props.flash?.no_match);

// Check for session flash data on load/update
// console.log('Session Flash:', page.props.flash);

// Watch for flash messages to handle lookup results
watch(
    () => page.props.flash,
    (flash: any) => {
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
    },
    { deep: true, immediate: true },
);

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

        <div class="flex min-h-[80vh] flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="mb-8 max-w-2xl space-y-4 text-center">
                <h1
                    class="bg-linear-to-r from-primary to-blue-600 bg-clip-text text-4xl font-extrabold tracking-tight text-transparent lg:text-5xl"
                >
                    The Bridge
                </h1>
                <p class="animate-in text-xl text-muted-foreground duration-700 fade-in slide-in-from-bottom-4">
                    Reconnect with RCFFUTA. Update your records and stay in the loop.
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
                    <Card class="border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/10">
                        <CardContent class="space-y-4 pt-6 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 p-3 dark:bg-green-900/20">
                                <CheckCircle2 class="h-6 w-6 text-green-600 dark:text-green-400" />
                            </div>
                            <h2 class="text-2xl font-bold text-green-800 dark:text-green-400">Success!</h2>
                            <p class="text-green-700 dark:text-green-300">{{ successMessage }}</p>
                            <Button class="mt-4 w-full" @click="resetToLookup">Return to Home</Button>
                        </CardContent>
                    </Card>
                </div>

                <!-- Lookup Form -->
                <Card v-else-if="mode === 'lookup'" class="w-full max-w-md border-t-4 border-t-primary shadow-lg">
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
                                    <button type="button" @click="proceedToCreate" class="font-bold underline">Create new?</button>
                                </AlertDescription>
                            </Alert>

                            <div class="space-y-2">
                                <Label>Full Name</Label>
                                <div class="relative">
                                    <Search class="absolute top-3 left-3 h-4 w-4 text-muted-foreground" />
                                    <Input v-model="lookupForm.name" class="pl-9" placeholder="e.g. John Doe" required />
                                </div>
                                <p v-if="lookupForm.errors.name" class="text-xs text-destructive">{{ lookupForm.errors.name }}</p>
                            </div>

                            <div class="space-y-4 pt-2">
                                <div class="relative flex items-center">
                                    <span class="w-full border-t" />
                                    <span class="bg-background px-2 text-xs text-muted-foreground uppercase">And</span>
                                    <span class="w-full border-t" />
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="space-y-2">
                                        <Label>Email</Label>
                                        <Input type="email" v-model="lookupForm.email" placeholder="john@example.com" />
                                        <p v-if="lookupForm.errors.email" class="text-xs text-destructive">{{ lookupForm.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Phone</Label>
                                        <Input v-model="lookupForm.phone" placeholder="080..." />
                                        <p v-if="lookupForm.errors.phone" class="text-xs text-destructive">{{ lookupForm.errors.phone }}</p>
                                    </div>
                                </div>
                            </div>

                            <Button type="submit" class="mt-2 w-full" :disabled="lookupForm.processing" size="lg">
                                {{ lookupForm.processing ? 'Searching...' : 'Search Record' }}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- Disambiguation -->
                <Card v-else-if="mode === 'disambiguation'" class="w-full max-w-md border-t-4 border-t-primary shadow-xl">
                    <CardHeader>
                        <CardTitle>Multiple Matches Found</CardTitle>
                        <CardDescription>Select your profile from the list below.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-2">
                        <div
                            v-for="match in possibleMatches"
                            :key="match.id"
                            class="group flex cursor-pointer items-center justify-between rounded-lg border p-3 transition-all hover:bg-accent/50 hover:shadow-sm"
                            @click="selectMatch(match)"
                        >
                            <div class="min-w-0 flex-1 pr-3">
                                <p class="truncate font-semibold">{{ match.name }}</p>
                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-muted-foreground">
                                    <span class="flex items-center gap-1">
                                        <span class="h-1 w-1 shrink-0 rounded-full bg-primary/50"></span>
                                        <span class="truncate">{{ match.department?.name || 'No Dept' }}</span>
                                    </span>
                                    <span class="text-muted-foreground/30">•</span>
                                    <span class="truncate">{{ match.tenure?.name || 'Unknown' }}</span>
                                </div>
                            </div>
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-muted text-muted-foreground transition-colors group-hover:bg-primary/10 group-hover:text-primary"
                            >
                                <CheckCircle2 class="h-4 w-4" />
                            </div>
                        </div>

                        <div class="pt-3 text-center">
                            <Button variant="link" size="sm" @click="proceedToCreate">None of these are me</Button>
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
                                <h3 class="border-b pb-2 text-lg font-semibold">Personal Details</h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Full Name *</Label>
                                        <Input v-model="updateForm.name" required />
                                        <p v-if="updateForm.errors.name" class="text-xs text-destructive">{{ updateForm.errors.name }}</p>
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
                                        <p v-if="updateForm.errors.gender" class="text-xs text-destructive">{{ updateForm.errors.gender }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Birth Date *</Label>
                                        <Input type="date" v-model="updateForm.birth_date" required />
                                        <p v-if="updateForm.errors.birth_date" class="text-xs text-destructive">{{ updateForm.errors.birth_date }}</p>
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
                                        <p v-if="updateForm.errors.marital_status" class="text-xs text-destructive">
                                            {{ updateForm.errors.marital_status }}
                                        </p>
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <Label>Recent Photo</Label>
                                        <div
                                            class="relative cursor-pointer rounded-lg border-2 border-dashed p-6 transition-colors"
                                            :class="[
                                                updateDragActive
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-muted-foreground/25 hover:border-primary/50',
                                                updatePhotoPreview ? 'bg-muted/30' : '',
                                            ]"
                                            @dragover.prevent="updateDragActive = true"
                                            @dragleave.prevent="updateDragActive = false"
                                            @drop="handleUpdateDrop"
                                            @click="updatePhotoInput?.click()"
                                        >
                                            <input
                                                ref="updatePhotoInput"
                                                type="file"
                                                accept="image/jpeg,image/png,image/jpg,image/gif"
                                                class="hidden"
                                                @change="handleUpdatePhotoChange"
                                            />
                                            <div v-if="updatePhotoPreview" class="flex items-center gap-4">
                                                <img
                                                    :src="updatePhotoPreview"
                                                    alt="Photo preview"
                                                    class="h-20 w-20 rounded-full border-2 border-background object-cover shadow-md"
                                                />
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium">Photo selected</p>
                                                    <p class="text-xs text-muted-foreground">Click to change or drag a new photo</p>
                                                </div>
                                                <Button type="button" variant="outline" size="sm" @click.stop="clearUpdatePhoto"> Remove </Button>
                                            </div>
                                            <div v-else class="text-center">
                                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-6 w-6 text-muted-foreground"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                        />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-medium text-muted-foreground">
                                                    <span class="text-primary">Click to upload</span> or drag and drop
                                                </p>
                                                <p class="mt-1 text-xs text-muted-foreground">JPEG, PNG, GIF (max 2MB)</p>
                                            </div>
                                        </div>
                                        <p v-if="updateForm.errors.photo" class="text-xs text-destructive">{{ updateForm.errors.photo }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 2: Contact Information -->
                            <div class="space-y-4">
                                <h3 class="border-b pb-2 text-lg font-semibold">Contact Information</h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Email</Label>
                                        <Input v-model="updateForm.email" type="email" />
                                        <p v-if="updateForm.errors.email" class="text-xs text-destructive">{{ updateForm.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <Label>Phone Numbers *</Label>
                                        <div v-for="(phone, index) in updateForm.phones" :key="index" class="space-y-1">
                                            <div class="flex gap-2">
                                                <Input v-model="updateForm.phones[index]" placeholder="e.g. 08012345678" />
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    @click="removePhone(updateForm, index)"
                                                    v-if="updateForm.phones.length > 1"
                                                >
                                                    <span class="sr-only">Delete</span>
                                                    <span aria-hidden="true">&times;</span>
                                                </Button>
                                            </div>
                                            <p v-if="updateForm.errors[`phones.${index}`]" class="text-xs text-destructive">
                                                {{ updateForm.errors[`phones.${index}`] }}
                                            </p>
                                        </div>
                                        <Button type="button" variant="outline" size="sm" class="mt-2" @click="addPhone(updateForm)"
                                            >+ Add Phone</Button
                                        >
                                        <p v-if="updateForm.errors.phones" class="text-xs text-destructive">{{ updateForm.errors.phones }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Residential State</Label>
                                        <Combobox v-model="updateForm.state" :options="states" placeholder="Select State" />
                                        <p v-if="updateForm.errors.state" class="text-xs text-destructive">{{ updateForm.errors.state }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Address</Label>
                                        <Input v-model="updateForm.address" />
                                        <p v-if="updateForm.errors.address" class="text-xs text-destructive">{{ updateForm.errors.address }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 3: Academic & Professional -->
                            <div class="space-y-4">
                                <h3 class="border-b pb-2 text-lg font-semibold">Academic & Professional</h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Tenure Year *</Label>
                                        <Combobox v-model="updateForm.tenure_id" :options="tenureOptions" placeholder="Select Tenure" />
                                        <p v-if="updateForm.errors.tenure_id" class="text-xs text-destructive">{{ updateForm.errors.tenure_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Department</Label>
                                        <Combobox v-model="updateForm.department_id" :options="departmentOptions" placeholder="Select Department" />
                                        <p v-if="updateForm.errors.department_id" class="text-xs text-destructive">
                                            {{ updateForm.errors.department_id }}
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Current Employer</Label>
                                        <Input v-model="updateForm.current_employer" />
                                        <p v-if="updateForm.errors.current_employer" class="text-xs text-destructive">
                                            {{ updateForm.errors.current_employer }}
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Occupation</Label>
                                        <Input v-model="updateForm.occupation" />
                                        <p v-if="updateForm.errors.occupation" class="text-xs text-destructive">{{ updateForm.errors.occupation }}</p>
                                    </div>
                                    <div class="flex items-end pb-2 md:col-span-2">
                                        <div class="flex items-center space-x-2">
                                            <Checkbox
                                                id="update_is_futa_staff"
                                                :checked="updateForm.is_futa_staff"
                                                @update:checked="(v: boolean) => (updateForm.is_futa_staff = v)"
                                            />
                                            <Label for="update_is_futa_staff">I'm currently a FUTA Staff</Label>
                                        </div>
                                        <p v-if="updateForm.errors.is_futa_staff" class="ml-2 text-xs text-destructive">
                                            {{ updateForm.errors.is_futa_staff }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 4: Association Details -->
                            <div class="space-y-4">
                                <h3 class="border-b pb-2 text-lg font-semibold">Unit Details</h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Unit *</Label>
                                        <Combobox v-model="updateForm.unit" :options="units" placeholder="Select Unit" />
                                        <p v-if="updateForm.errors.unit" class="text-xs text-destructive">{{ updateForm.errors.unit }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Past Exco Office</Label>
                                        <Combobox
                                            v-model="updateForm.past_exco_office"
                                            :options="pastExcoOfficeOptions"
                                            placeholder="Select Office"
                                        />
                                        <p v-if="updateForm.errors.past_exco_office" class="text-xs text-destructive">
                                            {{ updateForm.errors.past_exco_office }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-6">
                                <Button type="submit" size="lg" :disabled="updateForm.processing" class="w-full md:w-auto"> Submit Updates </Button>
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

                            <div class="mb-6 rounded-lg bg-blue-50 p-4 text-sm text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                Please fill in as much information as possible to help us keep in touch with you.
                            </div>

                            <!-- Group 1: Personal Details -->
                            <div class="space-y-4">
                                <h3 class="border-b pb-2 text-lg font-semibold">Personal Details</h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Full Name *</Label>
                                        <Input v-model="createForm.name" required />
                                        <p v-if="createForm.errors.name" class="text-xs text-destructive">{{ createForm.errors.name }}</p>
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
                                        <p v-if="createForm.errors.gender" class="text-xs text-destructive">{{ createForm.errors.gender }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Birth Date *</Label>
                                        <Input type="date" v-model="createForm.birth_date" required />
                                        <p v-if="createForm.errors.birth_date" class="text-xs text-destructive">{{ createForm.errors.birth_date }}</p>
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
                                        <p v-if="createForm.errors.marital_status" class="text-xs text-destructive">
                                            {{ createForm.errors.marital_status }}
                                        </p>
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <Label>Recent Photo</Label>
                                        <div
                                            class="relative cursor-pointer rounded-lg border-2 border-dashed p-6 transition-colors"
                                            :class="[
                                                createDragActive
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-muted-foreground/25 hover:border-primary/50',
                                                createPhotoPreview ? 'bg-muted/30' : '',
                                            ]"
                                            @dragover.prevent="createDragActive = true"
                                            @dragleave.prevent="createDragActive = false"
                                            @drop="handleCreateDrop"
                                            @click="createPhotoInput?.click()"
                                        >
                                            <input
                                                ref="createPhotoInput"
                                                type="file"
                                                accept="image/jpeg,image/png,image/jpg,image/gif"
                                                class="hidden"
                                                @change="handleCreatePhotoChange"
                                            />
                                            <div v-if="createPhotoPreview" class="flex items-center gap-4">
                                                <img
                                                    :src="createPhotoPreview"
                                                    alt="Photo preview"
                                                    class="h-20 w-20 rounded-full border-2 border-background object-cover shadow-md"
                                                />
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium">Photo selected</p>
                                                    <p class="text-xs text-muted-foreground">Click to change or drag a new photo</p>
                                                </div>
                                                <Button type="button" variant="outline" size="sm" @click.stop="clearCreatePhoto"> Remove </Button>
                                            </div>
                                            <div v-else class="text-center">
                                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-6 w-6 text-muted-foreground"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                        />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-medium text-muted-foreground">
                                                    <span class="text-primary">Click to upload</span> or drag and drop
                                                </p>
                                                <p class="mt-1 text-xs text-muted-foreground">JPEG, PNG, GIF (max 2MB)</p>
                                            </div>
                                        </div>
                                        <p v-if="createForm.errors.photo" class="text-xs text-destructive">{{ createForm.errors.photo }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 2: Contact Information -->
                            <div class="space-y-4">
                                <h3 class="border-b pb-2 text-lg font-semibold">Contact Information</h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Email</Label>
                                        <Input v-model="createForm.email" type="email" />
                                        <p v-if="createForm.errors.email" class="text-xs text-destructive">{{ createForm.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <Label>Phone Numbers *</Label>
                                        <div v-for="(phone, index) in createForm.phones" :key="index" class="space-y-1">
                                            <div class="flex gap-2">
                                                <Input v-model="createForm.phones[index]" placeholder="e.g. 08012345678" />
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    @click="removePhone(createForm, index)"
                                                    v-if="createForm.phones.length > 1"
                                                >
                                                    <span class="sr-only">Delete</span>
                                                    <span aria-hidden="true">&times;</span>
                                                </Button>
                                            </div>
                                            <p v-if="createForm.errors[`phones.${index}`]" class="text-xs text-destructive">
                                                {{ createForm.errors[`phones.${index}`] }}
                                            </p>
                                        </div>
                                        <Button type="button" variant="outline" size="sm" class="mt-2" @click="addPhone(createForm)"
                                            >+ Add Phone</Button
                                        >
                                        <p v-if="createForm.errors.phones" class="text-xs text-destructive">{{ createForm.errors.phones }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Residential State</Label>
                                        <Combobox v-model="createForm.state" :options="states" placeholder="Select State" />
                                        <p v-if="createForm.errors.state" class="text-xs text-destructive">{{ createForm.errors.state }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Address</Label>
                                        <Input v-model="createForm.address" />
                                        <p v-if="createForm.errors.address" class="text-xs text-destructive">{{ createForm.errors.address }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 3: Academic & Professional -->
                            <div class="space-y-4">
                                <h3 class="border-b pb-2 text-lg font-semibold">Academic & Professional</h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Tenure Year *</Label>
                                        <Combobox v-model="createForm.tenure_id" :options="tenureOptions" required placeholder="Select Tenure" />
                                        <p v-if="createForm.errors.tenure_id" class="text-xs text-destructive">{{ createForm.errors.tenure_id }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Department</Label>
                                        <Combobox v-model="createForm.department_id" :options="departmentOptions" placeholder="Select Department" />
                                        <p v-if="createForm.errors.department_id" class="text-xs text-destructive">
                                            {{ createForm.errors.department_id }}
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Current Employer</Label>
                                        <Input v-model="createForm.current_employer" />
                                        <p v-if="createForm.errors.current_employer" class="text-xs text-destructive">
                                            {{ createForm.errors.current_employer }}
                                        </p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Occupation</Label>
                                        <Input v-model="createForm.occupation" />
                                        <p v-if="createForm.errors.occupation" class="text-xs text-destructive">{{ createForm.errors.occupation }}</p>
                                    </div>
                                    <div class="flex items-end pb-2 md:col-span-2">
                                        <div class="flex items-center space-x-2">
                                            <Checkbox
                                                id="create_is_futa_staff"
                                                :checked="createForm.is_futa_staff"
                                                @update:checked="(v: boolean) => (createForm.is_futa_staff = v)"
                                            />
                                            <Label for="create_is_futa_staff">I'm currently a FUTA Staff</Label>
                                        </div>
                                        <p v-if="createForm.errors.is_futa_staff" class="ml-2 text-xs text-destructive">
                                            {{ createForm.errors.is_futa_staff }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 4: Association Details -->
                            <div class="space-y-4">
                                <h3 class="border-b pb-2 text-lg font-semibold">Unit Details</h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label>Unit *</Label>
                                        <Combobox v-model="createForm.unit" :options="units" placeholder="Select Unit" />
                                        <p v-if="createForm.errors.unit" class="text-xs text-destructive">{{ createForm.errors.unit }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Past Exco Office</Label>
                                        <Combobox
                                            v-model="createForm.past_exco_office"
                                            :options="pastExcoOfficeOptions"
                                            placeholder="Select Office"
                                        />
                                        <p v-if="createForm.errors.past_exco_office" class="text-xs text-destructive">
                                            {{ createForm.errors.past_exco_office }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-6">
                                <Button type="submit" size="lg" :disabled="createForm.processing" class="w-full md:w-auto"> Join The Bridge </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </Transition>
        </div>
    </PublicLayout>
</template>
