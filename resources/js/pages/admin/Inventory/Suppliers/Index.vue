<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import Filters from '@/components/Filters.vue';
import LinkButton from '@/components/LinkButton.vue';
import Pagination from '@/components/Pagination.vue';
import { useFilters } from '@/composables/useFilters';
import AppLayout from '@/layouts/AppLayout.vue';
import suppliersRoutes from '@/routes/admin/suppliers';
import type { DataTableAction, DataTableColumn } from '@/types';
import { type BreadcrumbItem, type PaginationData } from '@/types';
import type { Supplier } from '@/types/admin';
import { Head, router } from '@inertiajs/vue3';
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Suppliers',
        href: suppliersRoutes.index().url,
    },
];

interface Props {
    suppliers: PaginationData & {
        data: Supplier[];
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

// Initialize filters
const { filters, updateFilter, resetFilters } = useFilters(
    suppliersRoutes.index().url,
    {
        search: props.filters.search || '',
    },
);

// Filter configurations
// const filterConfigs: FilterConfig[] = [];

const columns: DataTableColumn<Supplier>[] = [
    {
        label: 'ID',
        key: 'id',
        render: (value) => `#${value.toString().padStart(4, '0')}`,
        class: 'text-gray-700',
    },
    {
        label: 'Supplier Name',
        key: 'supplier_name',
        class: 'font-medium',
    },
    {
        label: 'Email',
        key: 'email',
    },
    {
        label: 'Phone',
        key: 'phone',
    },
    {
        label: 'Status',
        key: 'status',
    },
];

const actions: DataTableAction<Supplier>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (row) => {
            router.visit(suppliersRoutes.show(row.id).url);
        },
        class: 'hover:text-blue-600 hover:bg-blue-50',
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (row) => {
            router.visit(suppliersRoutes.edit(row.id).url);
        },
        class: 'hover:text-green-600 hover:bg-green-50',
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (row) => {
            router.delete(suppliersRoutes.destroy(row.id).url);
        },
        class: 'hover:text-red-600 hover:bg-red-50',
    },
];
</script>

<template>
    <Head title="Admin - Suppliers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Suppliers</h1>
                    <p class="text-sm text-muted-foreground">
                        Manage your suppliers information
                    </p>
                </div>
                <LinkButton
                    :href="suppliersRoutes.create().url"
                    label="Add a supplier"
                />
            </div>

            <!-- Filters -->
            <Filters
                :search-value="filters.search as string"
                search-placeholder="Search by name, contact person, email, or phone..."
                @update:search="(value) => updateFilter('search', value)"
                @update:filter="(key, value) => updateFilter(key, value, true)"
                @reset="resetFilters"
            />

            <!-- Suppliers Table -->
            <DataTable
                :data="suppliers.data"
                :columns="columns"
                :actions="actions"
                empty-message="No suppliers found."
            >
                <!-- Suppliers Name -->
                <template #cell-supplier_name="{ value }">
                    <span
                        class="block max-w-[150px] truncate font-medium"
                        :title="value"
                    >
                        {{ value }}
                    </span>
                </template>

                <!-- Contact Person -->
                <template #cell-contact_person="{ value }">
                    <span class="block max-w-[120px] truncate" :title="value">
                        {{ value || 'N/A' }}
                    </span>
                </template>

                <!-- Email -->
                <template #cell-email="{ value }">
                    <span class="block max-w-[150px] truncate" :title="value">
                        {{ value || 'N/A' }}
                    </span>
                </template>

                <!-- Phone -->
                <template #cell-phone="{ value }">
                    <span class="block max-w-[120px] truncate" :title="value">
                        {{ value || 'N/A' }}
                    </span>
                </template>

                <!-- Status Badge -->
                <template #cell-status="{ value }">
                    <span
                        v-if="value === 'active'"
                        class="inline-flex items-center rounded-full border border-green-500/10 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-600"
                    >
                        Active
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center rounded-full border border-red-500/10 bg-red-500/10 px-2 py-1 text-xs font-medium text-red-600"
                    >
                        Inactive
                    </span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div v-if="suppliers.total > 10">
                <Pagination :pagination="suppliers" />
            </div>
        </div>
    </AppLayout>
</template>
