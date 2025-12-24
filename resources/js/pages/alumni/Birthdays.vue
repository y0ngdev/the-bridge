<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { type BreadcrumbItem, type BirthdayAlumnus } from '@/types';
import { Head } from '@inertiajs/vue3';
import { birthdays, index } from '@/actions/App/Http/Controllers/AlumnusController';
import { Cake, Mail, Phone, PartyPopper, ChevronDown, ChevronUp } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    today: BirthdayAlumnus[];
    thisWeek: BirthdayAlumnus[];
    thisMonth: BirthdayAlumnus[];
    allByMonth: Record<string, BirthdayAlumnus[]>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Alumni', href: index().url },
    { title: 'Birthdays', href: birthdays().url },
];

const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const isBirthdayToday = (dateString: string): boolean => {
    const date = new Date(dateString);
    const today = new Date();
    return date.getMonth() === today.getMonth() && date.getDate() === today.getDate();
};

const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

const sortedMonths = months.filter(month => props.allByMonth[month]);
const currentMonth = new Date().toLocaleDateString('en-US', { month: 'long' });

// Filter out today's birthdays from thisWeek/thisMonth to avoid duplication
const thisWeekFiltered = computed(() => props.thisWeek.filter(a => !isBirthdayToday(a.birth_date)));
const thisMonthFiltered = computed(() => props.thisMonth.filter(a => !isBirthdayToday(a.birth_date)));

// Show more/less functionality
const showAllToday = ref(false);
const showAllWeek = ref(false);
const showAllMonth = ref(false);
const INITIAL_DISPLAY_COUNT = 9;

const displayedToday = computed(() => 
    showAllToday.value ? props.today : props.today.slice(0, INITIAL_DISPLAY_COUNT)
);
const displayedWeek = computed(() => 
    showAllWeek.value ? thisWeekFiltered.value : thisWeekFiltered.value.slice(0, INITIAL_DISPLAY_COUNT)
);
const displayedMonth = computed(() => 
    showAllMonth.value ? thisMonthFiltered.value : thisMonthFiltered.value.slice(0, INITIAL_DISPLAY_COUNT)
);
</script>

<template>
    <Head title="Alumni Birthdays" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6 max-w-7xl mx-auto">
            <div class="mb-6">
                <HeadingSmall title="Alumni Birthdays" description="Birthday calendar for all alumni" />
            </div>

            <!-- Today's Birthdays -->
            <Card v-if="today.length > 0" class="mb-6 border-primary/30 bg-primary/5 dark:bg-primary/10">
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-2">
                        <PartyPopper class="h-6 w-6" />
                        Today's Birthdays!
                        <Badge variant="default" class="ml-2">{{ today.length }}</Badge>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <Dialog v-for="alumnus in displayedToday" :key="alumnus.id">
                            <DialogTrigger as-child>
                                <Card class="hover:shadow-lg hover:scale-[1.02] transition-all cursor-pointer border-primary/30">
                                    <CardContent class="p-6">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/20 dark:bg-primary/30">
                                                <Cake class="h-7 w-7 text-foreground" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                    <h3 class="font-semibold truncate">{{ alumnus.name }}</h3>
                                                <p class="text-sm text-muted-foreground">{{ alumnus.dept || 'Alumni' }}</p>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle class="flex items-center gap-2">
                                        <span class="text-2xl">🎂</span>
                                        {{ alumnus.name }}
                                    </DialogTitle>
                                    <DialogDescription>Happy Birthday! Contact details below.</DialogDescription>
                                </DialogHeader>
                                <div class="space-y-4 pt-4">
                                    <div class="flex items-center gap-3 p-3 rounded-lg bg-muted">
                                        <Cake class="h-5 w-5 text-primary" />
                                        <span class="font-medium">{{ formatDate(alumnus.birth_date) }}</span>
                                    </div>
                                    <div v-if="alumnus.email" class="flex items-center gap-3">
                                        <Mail class="h-5 w-5 text-muted-foreground" />
                                        <a :href="`mailto:${alumnus.email}`" class=" hover:underline">{{ alumnus.email }}</a>
                                    </div>
                                    <div v-if="alumnus.phones?.length" class="flex items-start gap-3">
                                        <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                                        <div class="space-y-1">
                                            <a v-for="phone in alumnus.phones" :key="phone" :href="`tel:${phone}`" class="block  hover:underline">{{ phone }}</a>
                                        </div>
                                    </div>
                                    <p v-if="!alumnus.email && !alumnus.phones?.length" class="text-muted-foreground italic">No contact information available.</p>
                                </div>
                            </DialogContent>
                        </Dialog>
                    </div>
                    <div v-if="today.length > INITIAL_DISPLAY_COUNT" class="text-center mt-4">
                        <Button variant="ghost" @click="showAllToday = !showAllToday">
                            <component :is="showAllToday ? ChevronUp : ChevronDown" class="h-4 w-4 mr-2" />
                            {{ showAllToday ? 'Show less' : `Show all ${today.length} birthdays` }}
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- No birthdays today -->
            <Card v-else class="mb-6 border-dashed">
                <CardContent class="py-12 text-center">
                    <Cake class="mx-auto h-16 w-16 text-muted-foreground/30 mb-4" />
                    <p class="text-muted-foreground">No birthdays today</p>
                </CardContent>
            </Card>

            <Tabs default-value="week" class="w-full">
                <TabsList class="mb-6 grid w-full grid-cols-3">
                    <TabsTrigger value="week">
                        This Week
                        <Badge v-if="thisWeekFiltered.length" variant="secondary" class="ml-2">{{ thisWeekFiltered.length }}</Badge>
                    </TabsTrigger>
                    <TabsTrigger value="month">
                        This Month
                        <Badge v-if="thisMonthFiltered.length" variant="secondary" class="ml-2">{{ thisMonthFiltered.length }}</Badge>
                    </TabsTrigger>
                    <TabsTrigger value="all">All Birthdays</TabsTrigger>
                </TabsList>

                <!-- This Week -->
                <TabsContent value="week">
                    <div v-if="thisWeekFiltered.length === 0" class="text-center py-12 text-muted-foreground">
                        <Cake class="mx-auto h-16 w-16 mb-4 opacity-30" />
                        <p>No upcoming birthdays this week</p>
                    </div>

                    <div v-else>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Dialog v-for="alumnus in displayedWeek" :key="alumnus.id">
                                <DialogTrigger as-child>
                                    <Card class="hover:shadow-md hover:border-primary/50 transition-all cursor-pointer">
                                        <CardContent class="p-6">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                                                    <Cake class="h-6 w-6 text-foreground" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="font-semibold truncate">{{ alumnus.name }}</h3>
                                                    <p class="text-sm text-muted-foreground">{{ formatDate(alumnus.birth_date) }}</p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>{{ alumnus.name }}</DialogTitle>
                                        <DialogDescription>Contact details</DialogDescription>
                                    </DialogHeader>
                                    <div class="space-y-4 pt-4">
                                        <div class="flex items-center gap-3 p-3 rounded-lg bg-muted">
                                            <Cake class="h-5 w-5 text-primary" />
                                            <span class="font-medium">{{ formatDate(alumnus.birth_date) }}</span>
                                        </div>
                                        <div v-if="alumnus.email" class="flex items-center gap-3">
                                            <Mail class="h-5 w-5 text-muted-foreground" />
                                            <a :href="`mailto:${alumnus.email}`" class=" hover:underline">{{ alumnus.email }}</a>
                                        </div>
                                        <div v-if="alumnus.phones?.length" class="flex items-start gap-3">
                                            <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                                            <div class="space-y-1">
                                                <a v-for="phone in alumnus.phones" :key="phone" :href="`tel:${phone}`" class="block hover:underline">{{ phone }}</a>
                                            </div>
                                        </div>
                                        <p v-if="!alumnus.email && !alumnus.phones?.length" class="text-muted-foreground italic">No contact information available.</p>
                                    </div>
                                </DialogContent>
                            </Dialog>
                        </div>
                        <div v-if="thisWeekFiltered.length > INITIAL_DISPLAY_COUNT" class="text-center mt-6">
                            <Button variant="ghost" @click="showAllWeek = !showAllWeek">
                                <component :is="showAllWeek ? ChevronUp : ChevronDown" class="h-4 w-4 mr-2" />
                                {{ showAllWeek ? 'Show less' : `Show all ${thisWeekFiltered.length}` }}
                            </Button>
                        </div>
                    </div>
                </TabsContent>

                <!-- This Month -->
                <TabsContent value="month">
                    <div v-if="thisMonthFiltered.length === 0" class="text-center py-12 text-muted-foreground">
                        <Cake class="mx-auto h-16 w-16 mb-4 opacity-30" />
                        <p>No upcoming birthdays this month</p>
                    </div>

                    <div v-else>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Dialog v-for="alumnus in displayedMonth" :key="alumnus.id">
                                <DialogTrigger as-child>
                                    <Card class="hover:shadow-md hover:border-primary/50 transition-all cursor-pointer">
                                        <CardContent class="p-6">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                                                    <Cake class="h-6 w-6 text-foreground" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h3 class="font-semibold truncate">{{ alumnus.name }}</h3>
                                                    <p class="text-sm text-muted-foreground">{{ formatDate(alumnus.birth_date) }}</p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>{{ alumnus.name }}</DialogTitle>
                                        <DialogDescription>Contact details</DialogDescription>
                                    </DialogHeader>
                                    <div class="space-y-4 pt-4">
                                        <div class="flex items-center gap-3 p-3 rounded-lg bg-muted">
                                            <Cake class="h-5 w-5 text-primary" />
                                            <span class="font-medium">{{ formatDate(alumnus.birth_date) }}</span>
                                        </div>
                                        <div v-if="alumnus.email" class="flex items-center gap-3">
                                            <Mail class="h-5 w-5 text-muted-foreground" />
                                            <a :href="`mailto:${alumnus.email}`" class="hover:underline">{{ alumnus.email }}</a>
                                        </div>
                                        <div v-if="alumnus.phones?.length" class="flex items-start gap-3">
                                            <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                                            <div class="space-y-1">
                                                <a v-for="phone in alumnus.phones" :key="phone" :href="`tel:${phone}`" class="block hover:underline">{{ phone }}</a>
                                            </div>
                                        </div>
                                        <p v-if="!alumnus.email && !alumnus.phones?.length" class="text-muted-foreground italic">No contact information available.</p>
                                    </div>
                                </DialogContent>
                            </Dialog>
                        </div>
                        <div v-if="thisMonthFiltered.length > INITIAL_DISPLAY_COUNT" class="text-center mt-6">
                            <Button variant="ghost" @click="showAllMonth = !showAllMonth">
                                <component :is="showAllMonth ? ChevronUp : ChevronDown" class="h-4 w-4 mr-2" />
                                {{ showAllMonth ? 'Show less' : `Show all ${thisMonthFiltered.length}` }}
                            </Button>
                        </div>
                    </div>
                </TabsContent>

                <!-- All Birthdays -->
                <TabsContent value="all">
                    <div v-if="sortedMonths.length === 0" class="text-center py-12 text-muted-foreground">
                        <Cake class="mx-auto h-16 w-16 mb-4 opacity-30" />
                        <p>No birthdays recorded yet</p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <Card
                            v-for="month in sortedMonths"
                            :key="month"
                            :class="[
                                'hover:shadow-md transition-shadow',
                                month === currentMonth ? 'ring-2 ring-primary' : ''
                            ]"
                        >
                            <CardHeader class="pb-3">
                                <CardTitle class="flex items-center justify-between text-base">
                                    <span class="flex items-center gap-2">
                                        <Cake class="h-5 w-5" />
                                        <span>{{ month }}</span>
                                    </span>
                                    <Badge :variant="month === currentMonth ? 'default' : 'secondary'">
                                        {{ allByMonth[month].length }}
                                    </Badge>
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="pt-0">
                                <ul class="space-y-2">
                                    <li
                                        v-for="alumnus in allByMonth[month]"
                                        :key="alumnus.id"
                                        class="flex items-center justify-between text-sm py-2 border-b border-border/50 last:border-0"
                                    >
                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <button 
                                                    class="font-medium text-left hover:text-primary transition-colors cursor-pointer truncate flex-1"
                                                    :class="isBirthdayToday(alumnus.birth_date) ? 'font-bold' : ''"
                                                >
                                                    {{ alumnus.name }}
                                                    <span v-if="isBirthdayToday(alumnus.birth_date)" class="ml-1">🎂</span>
                                                </button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <DialogHeader>
                                                    <DialogTitle>{{ alumnus.name }}</DialogTitle>
                                                    <DialogDescription>Contact details</DialogDescription>
                                                </DialogHeader>
                                                <div class="space-y-4 pt-4">
                                                    <div class="flex items-center gap-3 p-3 rounded-lg bg-muted">
                                                        <Cake class="h-5 w-5 text-primary" />
                                                        <span class="font-medium">{{ formatDate(alumnus.birth_date) }}</span>
                                                    </div>
                                                    <div v-if="alumnus.email" class="flex items-center gap-3">
                                                        <Mail class="h-5 w-5 text-muted-foreground" />
                                                        <a :href="`mailto:${alumnus.email}`" class=" hover:underline">{{ alumnus.email }}</a>
                                                    </div>
                                                    <div v-if="alumnus.phones?.length" class="flex items-start gap-3">
                                                        <Phone class="h-5 w-5 text-muted-foreground mt-0.5" />
                                                        <div class="space-y-1">
                                                            <a v-for="phone in alumnus.phones" :key="phone" :href="`tel:${phone}`" class="block  hover:underline">{{ phone }}</a>
                                                        </div>
                                                    </div>
                                                    <p v-if="!alumnus.email && !alumnus.phones?.length" class="text-muted-foreground italic">No contact information available.</p>
                                                </div>
                                            </DialogContent>
                                        </Dialog>
                                        <span class="text-muted-foreground text-xs ml-2">
                                            {{ formatDate(alumnus.birth_date) }}
                                        </span>
                                    </li>
                                </ul>
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
