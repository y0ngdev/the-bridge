<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
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
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { index, create, edit } from '@/actions/App/Http/Controllers/TenureController';

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

const props = defineProps<{
    tenures: PaginatedTenures;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tenures', href: index().url },
];

const goToPage = (page: number) => {
    router.get(index().url, { page }, { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <Head title="Tenures" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="flex items-center justify-between mb-6">
                <HeadingSmall title="Tenures" description="Manage tenure records" />
                <Link :href="create().url">
                    <Button>Add Tenure</Button>
                </Link>
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
                                <Link :href="edit(tenure.id).url">
                                    <Button variant="outline" size="sm">Edit</Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="tenures.data.length === 0">
                            <TableCell colspan="3" class="text-center text-muted-foreground py-8">
                                No tenures found. Create your first one!
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <Pagination
                v-if="tenures.last_page > 1"
                v-slot="{ page }"
                :total="tenures.last_page * 15"
                :items-per-page="15"
                :default-page="tenures.current_page"
                :sibling-count="1"
                show-edges
                class="mt-4 "
                @update:page="goToPage"
            >
                <PaginationContent v-slot="{ items }" class="gap-4 ">
                    <PaginationFirst  />
                    <PaginationPrevious />

                    <template v-for="(item, idx) in items" :key="idx">
                        <PaginationItem v-if="item.type === 'page'" :value="item.value" as-child>
                            <Button
                                :variant="item.value === tenures.current_page ? 'default' : 'outline'"
                                size="icon"

                            >
                                {{ item.value }}
                            </Button>
                        </PaginationItem>
                        <PaginationEllipsis v-else :index="idx" />
                    </template>

                    <PaginationNext />
                    <PaginationLast  />
                </PaginationContent>
            </Pagination>
        </div>
    </AppLayout>
</template>
