<script setup lang="ts">
import { birthdays, index, show } from '@/actions/App/Http/Controllers/AlumnusController';
import { index as dashboardIndex } from '@/actions/App/Http/Controllers/DashboardController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPhoneNumber } from '@/lib/utils';
import { type BirthdayAlumnus, type BreadcrumbItem } from '@/types';
import { Deferred, Head, Link } from '@inertiajs/vue3';
import { Cake, ChevronDown, ChevronUp, ExternalLink, Mail, PartyPopper, Phone } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    today?: BirthdayAlumnus[];
    thisWeek?: BirthdayAlumnus[];
    thisMonth?: BirthdayAlumnus[];
    allByMonth?: Record<string, BirthdayAlumnus[]>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboardIndex().url },
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

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

const sortedMonths = computed(() => months.filter((month) => props.allByMonth?.[month]));
const currentMonth = new Date().toLocaleDateString('en-US', { month: 'long' });

// Filter out today's birthdays from thisWeek/thisMonth to avoid duplication
const thisWeekFiltered = computed(() => (props.thisWeek ?? []).filter((a) => !isBirthdayToday(a.birth_date)));
const thisMonthFiltered = computed(() => (props.thisMonth ?? []).filter((a) => !isBirthdayToday(a.birth_date)));

// Show more/less functionality
const showAllToday = ref(false);
const showAllWeek = ref(false);
const showAllMonth = ref(false);
const INITIAL_DISPLAY_COUNT = 9;

const displayedToday = computed(() => {
    const todayList = props.today ?? [];
    return showAllToday.value ? todayList : todayList.slice(0, INITIAL_DISPLAY_COUNT);
});
const displayedWeek = computed(() => (showAllWeek.value ? thisWeekFiltered.value : thisWeekFiltered.value.slice(0, INITIAL_DISPLAY_COUNT)));
const displayedMonth = computed(() => (showAllMonth.value ? thisMonthFiltered.value : thisMonthFiltered.value.slice(0, INITIAL_DISPLAY_COUNT)));
</script>

<template>
    <Head title="Alumni Birthdays" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl px-4 py-6">
            <div class="mb-6">
                <HeadingSmall title="Alumni Birthdays" description="Birthday calendar for all alumni" />
            </div>

            <!-- Today's Birthdays -->
            <Deferred data="today">
                <template #fallback>
                    <Card class="mb-6 border-primary/30 bg-primary/5 dark:bg-primary/10">
                        <CardHeader class="pb-4">
                            <CardTitle class="flex items-center gap-2">
                                <PartyPopper class="h-6 w-6" />
                                Today's Birthdays!
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <Skeleton v-for="i in 3" :key="i" class="h-24 rounded-lg" />
                            </div>
                        </CardContent>
                    </Card>
                </template>

                <Card v-if="(today ?? []).length > 0" class="mb-6 border-primary/30 bg-primary/5 dark:bg-primary/10">
                    <CardHeader class="pb-4">
                        <CardTitle class="flex items-center gap-2">
                            <PartyPopper class="h-6 w-6" />
                            Today's Birthdays!
                            <Badge variant="default" class="ml-2">{{ (today ?? []).length }}</Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Dialog v-for="alumnus in displayedToday" :key="alumnus.id">
                                <DialogTrigger as-child>
                                    <Card class="cursor-pointer border-primary/30 transition-all hover:scale-[1.02] hover:shadow-lg">
                                        <CardContent class="p-6">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary/20 dark:bg-primary/30"
                                                >
                                                    <img
                                                        v-if="alumnus.photo_url"
                                                        :src="alumnus.photo_url"
                                                        :alt="`${alumnus.name}'s photo`"
                                                        class="h-full w-full object-cover"
                                                    />
                                                    <span v-else class="text-lg font-semibold text-foreground/70">{{ alumnus.initials }}</span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <h3 class="truncate font-semibold">{{ alumnus.name }}</h3>
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
                                        <div class="flex items-center gap-3 rounded-lg bg-muted p-3">
                                            <Cake class="h-5 w-5 text-primary" />
                                            <span class="font-medium">{{ formatDate(alumnus.birth_date) }}</span>
                                        </div>
                                        <div v-if="alumnus.email" class="flex items-center gap-3">
                                            <Mail class="h-5 w-5 text-muted-foreground" />
                                            <a :href="`mailto:${alumnus.email}`" class="hover:underline">{{ alumnus.email }}</a>
                                        </div>
                                        <div v-if="alumnus.phones?.length" class="flex items-start gap-3">
                                            <Phone class="mt-0.5 h-5 w-5 text-muted-foreground" />
                                            <div class="space-y-1">
                                                <a
                                                    v-for="phone in alumnus.phones"
                                                    :key="phone"
                                                    :href="`tel:${phone}`"
                                                    class="block hover:underline"
                                                    >{{ formatPhoneNumber(phone) }}</a
                                                >
                                            </div>
                                        </div>
                                        <p v-if="!alumnus.email && !alumnus.phones?.length" class="text-muted-foreground italic">
                                            No contact information available.
                                        </p>
                                    </div>
                                    <DialogFooter>
                                        <Link :href="show(alumnus.id).url" class="w-full">
                                            <Button variant="outline" class="w-full">
                                                <ExternalLink class="mr-2 h-4 w-4" />
                                                View Full Details
                                            </Button>
                                        </Link>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        </div>
                        <div v-if="(today ?? []).length > INITIAL_DISPLAY_COUNT" class="mt-4 text-center">
                            <Button variant="ghost" @click="showAllToday = !showAllToday">
                                <component :is="showAllToday ? ChevronUp : ChevronDown" class="mr-2 h-4 w-4" />
                                {{ showAllToday ? 'Show less' : `Show all ${(today ?? []).length} birthdays` }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- No birthdays today -->
                <Card v-else class="mb-6 border-dashed">
                    <CardContent class="py-12 text-center">
                        <Cake class="mx-auto mb-4 h-16 w-16 text-muted-foreground/30" />
                        <p class="text-muted-foreground">No birthdays today</p>
                    </CardContent>
                </Card>
            </Deferred>

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
                    <Deferred data="thisWeek">
                        <template #fallback>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <Card v-for="i in 6" :key="i">
                                    <CardContent class="p-6">
                                        <div class="flex items-center gap-4">
                                            <Skeleton class="h-12 w-12 rounded-full" />
                                            <div class="flex-1 space-y-2">
                                                <Skeleton class="h-4 w-3/4" />
                                                <Skeleton class="h-3 w-1/2" />
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </template>

                        <div v-if="thisWeekFiltered.length === 0" class="py-12 text-center text-muted-foreground">
                            <Cake class="mx-auto mb-4 h-16 w-16 opacity-30" />
                            <p>No upcoming birthdays this week</p>
                        </div>

                        <div v-else>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <Dialog v-for="alumnus in displayedWeek" :key="alumnus.id">
                                    <DialogTrigger as-child>
                                        <Card class="cursor-pointer transition-all hover:border-primary/50 hover:shadow-md">
                                            <CardContent class="p-6">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-muted"
                                                    >
                                                        <img
                                                            v-if="alumnus.photo_url"
                                                            :src="alumnus.photo_url"
                                                            :alt="`${alumnus.name}'s photo`"
                                                            class="h-full w-full object-cover"
                                                        />
                                                        <span v-else class="text-sm font-semibold text-muted-foreground">{{ alumnus.initials }}</span>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <h3 class="truncate font-semibold">{{ alumnus.name }}</h3>
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
                                            <div class="flex items-center gap-3 rounded-lg bg-muted p-3">
                                                <Cake class="h-5 w-5 text-primary" />
                                                <span class="font-medium">{{ formatDate(alumnus.birth_date) }}</span>
                                            </div>
                                            <div v-if="alumnus.email" class="flex items-center gap-3">
                                                <Mail class="h-5 w-5 text-muted-foreground" />
                                                <a :href="`mailto:${alumnus.email}`" class="hover:underline">{{ alumnus.email }}</a>
                                            </div>
                                            <div v-if="alumnus.phones?.length" class="flex items-start gap-3">
                                                <Phone class="mt-0.5 h-5 w-5 text-muted-foreground" />
                                                <div class="space-y-1">
                                                    <a
                                                        v-for="phone in alumnus.phones"
                                                        :key="phone"
                                                        :href="`tel:${phone}`"
                                                        class="block hover:underline"
                                                        >{{ formatPhoneNumber(phone) }}</a
                                                    >
                                                </div>
                                            </div>
                                            <p v-if="!alumnus.email && !alumnus.phones?.length" class="text-muted-foreground italic">
                                                No contact information available.
                                            </p>
                                        </div>
                                        <DialogFooter>
                                            <Link :href="show(alumnus.id).url" class="w-full">
                                                <Button variant="outline" class="w-full">
                                                    <ExternalLink class="mr-2 h-4 w-4" />
                                                    View Full Details
                                                </Button>
                                            </Link>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>
                            <div v-if="thisWeekFiltered.length > INITIAL_DISPLAY_COUNT" class="mt-6 text-center">
                                <Button variant="ghost" @click="showAllWeek = !showAllWeek">
                                    <component :is="showAllWeek ? ChevronUp : ChevronDown" class="mr-2 h-4 w-4" />
                                    {{ showAllWeek ? 'Show less' : `Show all ${thisWeekFiltered.length}` }}
                                </Button>
                            </div>
                        </div>
                    </Deferred>
                </TabsContent>

                <!-- This Month -->
                <TabsContent value="month">
                    <Deferred data="thisMonth">
                        <template #fallback>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <Card v-for="i in 6" :key="i">
                                    <CardContent class="p-6">
                                        <div class="flex items-center gap-4">
                                            <Skeleton class="h-12 w-12 rounded-full" />
                                            <div class="flex-1 space-y-2">
                                                <Skeleton class="h-4 w-3/4" />
                                                <Skeleton class="h-3 w-1/2" />
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </template>

                        <div v-if="thisMonthFiltered.length === 0" class="py-12 text-center text-muted-foreground">
                            <Cake class="mx-auto mb-4 h-16 w-16 opacity-30" />
                            <p>No upcoming birthdays this month</p>
                        </div>

                        <div v-else>
                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <Dialog v-for="alumnus in displayedMonth" :key="alumnus.id">
                                    <DialogTrigger as-child>
                                        <Card class="cursor-pointer transition-all hover:border-primary/50 hover:shadow-md">
                                            <CardContent class="p-6">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-muted"
                                                    >
                                                        <img
                                                            v-if="alumnus.photo_url"
                                                            :src="alumnus.photo_url"
                                                            :alt="`${alumnus.name}'s photo`"
                                                            class="h-full w-full object-cover"
                                                        />
                                                        <span v-else class="text-sm font-semibold text-muted-foreground">{{ alumnus.initials }}</span>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <h3 class="truncate font-semibold">{{ alumnus.name }}</h3>
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
                                            <div class="flex items-center gap-3 rounded-lg bg-muted p-3">
                                                <Cake class="h-5 w-5 text-primary" />
                                                <span class="font-medium">{{ formatDate(alumnus.birth_date) }}</span>
                                            </div>
                                            <div v-if="alumnus.email" class="flex items-center gap-3">
                                                <Mail class="h-5 w-5 text-muted-foreground" />
                                                <a :href="`mailto:${alumnus.email}`" class="hover:underline">{{ alumnus.email }}</a>
                                            </div>
                                            <div v-if="alumnus.phones?.length" class="flex items-start gap-3">
                                                <Phone class="mt-0.5 h-5 w-5 text-muted-foreground" />
                                                <div class="space-y-1">
                                                    <a
                                                        v-for="phone in alumnus.phones"
                                                        :key="phone"
                                                        :href="`tel:${phone}`"
                                                        class="block hover:underline"
                                                        >{{ formatPhoneNumber(phone) }}</a
                                                    >
                                                </div>
                                            </div>
                                            <p v-if="!alumnus.email && !alumnus.phones?.length" class="text-muted-foreground italic">
                                                No contact information available.
                                            </p>
                                        </div>
                                        <DialogFooter>
                                            <Link :href="show(alumnus.id).url" class="w-full">
                                                <Button variant="outline" class="w-full">
                                                    <ExternalLink class="mr-2 h-4 w-4" />
                                                    View Full Details
                                                </Button>
                                            </Link>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>
                            <div v-if="thisMonthFiltered.length > INITIAL_DISPLAY_COUNT" class="mt-6 text-center">
                                <Button variant="ghost" @click="showAllMonth = !showAllMonth">
                                    <component :is="showAllMonth ? ChevronUp : ChevronDown" class="mr-2 h-4 w-4" />
                                    {{ showAllMonth ? 'Show less' : `Show all ${thisMonthFiltered.length}` }}
                                </Button>
                            </div>
                        </div>
                    </Deferred>
                </TabsContent>

                <!-- All Birthdays -->
                <TabsContent value="all">
                    <Deferred data="allByMonth">
                        <template #fallback>
                            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                <Card v-for="i in 8" :key="i">
                                    <CardHeader class="pb-3">
                                        <Skeleton class="h-6 w-32" />
                                    </CardHeader>
                                    <CardContent class="pt-0">
                                        <div class="space-y-4">
                                            <div v-for="j in 3" :key="j" class="flex items-center justify-between">
                                                <Skeleton class="h-4 w-24" />
                                                <Skeleton class="h-3 w-16" />
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </template>

                        <div v-if="sortedMonths.length === 0" class="py-12 text-center text-muted-foreground">
                            <Cake class="mx-auto mb-4 h-16 w-16 opacity-30" />
                            <p>No birthdays recorded yet</p>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            <Card
                                v-for="month in sortedMonths"
                                :key="month"
                                :class="['transition-shadow hover:shadow-md', month === currentMonth ? 'ring-2 ring-primary' : '']"
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
                                            class="flex items-center justify-between border-b border-border/50 py-2 text-sm last:border-0"
                                        >
                                            <Dialog>
                                                <DialogTrigger as-child>
                                                    <button
                                                        class="flex-1 cursor-pointer truncate text-left font-medium transition-colors hover:text-primary"
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
                                                        <div class="flex items-center gap-3 rounded-lg bg-muted p-3">
                                                            <Cake class="h-5 w-5 text-primary" />
                                                            <span class="font-medium">{{ formatDate(alumnus.birth_date) }}</span>
                                                        </div>
                                                        <div v-if="alumnus.email" class="flex items-center gap-3">
                                                            <Mail class="h-5 w-5 text-muted-foreground" />
                                                            <a :href="`mailto:${alumnus.email}`" class="hover:underline">{{ alumnus.email }}</a>
                                                        </div>
                                                        <div v-if="alumnus.phones?.length" class="flex items-start gap-3">
                                                            <Phone class="mt-0.5 h-5 w-5 text-muted-foreground" />
                                                            <div class="space-y-1">
                                                                <a
                                                                    v-for="phone in alumnus.phones"
                                                                    :key="phone"
                                                                    :href="`tel:${phone}`"
                                                                    class="block hover:underline"
                                                                    >{{ formatPhoneNumber(phone) }}</a
                                                                >
                                                            </div>
                                                        </div>
                                                        <p v-if="!alumnus.email && !alumnus.phones?.length" class="text-muted-foreground italic">
                                                            No contact information available.
                                                        </p>
                                                    </div>
                                                    <DialogFooter>
                                                        <Link :href="show(alumnus.id).url" class="w-full">
                                                            <Button variant="outline" class="w-full">
                                                                <ExternalLink class="mr-2 h-4 w-4" />
                                                                View Full Details
                                                            </Button>
                                                        </Link>
                                                    </DialogFooter>
                                                </DialogContent>
                                            </Dialog>
                                            <span class="ml-2 text-xs text-muted-foreground">
                                                {{ formatDate(alumnus.birth_date) }}
                                            </span>
                                        </li>
                                    </ul>
                                </CardContent>
                            </Card>
                        </div>
                    </Deferred>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
