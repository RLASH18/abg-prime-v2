<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type FilterConfig, type PaginationData, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn, DataTableAction } from '@/types';
import type { DamagedItem } from '@/types/admin';
import { Trash2 } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { useFilters } from '@/composables/useFilters';
import Filters from '@/components/Filters.vue';
import damagedItemsRoutes from '@/routes/admin/damaged-items';
import { useFormatters } from '@/composables/useFormatters';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Damaged Items',
        href: damagedItemsRoutes.index().url
    },
];

interface Props {
    damagedItems: PaginationData & {
        data: DamagedItem[]
    };
    filters: {
        search?: string;
        status?: string
    };
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();

const { filters, updateFilter, resetFilters } = useFilters(
    damagedItemsRoutes.index().url,
    {
        search: props.filters.search || '',
        status: props.filters.status || '',
    }
);

const filterConfigs: FilterConfig[] = [
    {
        label: 'Status',
        key: 'status',
        options: [
            { label: 'Resellable', value: 'resellable' },
            { label: 'Disposed', value: 'disposed' },
        ],
        placeholder: 'All Statuses',
    },
];

const columns: DataTableColumn<DamagedItem>[] = [
    {
        label: 'ID',
        key: 'id',
        render: (value) => `#${value.toString().padStart(4, '0')}`,
        class: 'text-gray-700'
    },
    {
        label: 'Item Code',
        key: 'item.item_code',
        render: (_, row) => row.item.item_code
    },
    {
        label: 'Item Name',
        key: 'item.item_name',
        render: (_, row) => row.item.item_name
    },
    {
        label: 'Qty Damaged',
        key: 'quantity',
        align: 'right'
    },
    {
        label: 'Discount',
        key: 'discount',
        align: 'right',
        render: (value) => value ? formatCurrency(value) : '-'
    },
    {
        label: 'Status',
        key: 'status',
        render: (value) => value.charAt(0).toUpperCase() + value.slice(1)
    },
    {
        label: 'Remarks',
        key: 'remarks',
        render: (value) => value || '-'
    },
];

const actions: DataTableAction<DamagedItem>[] = [
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (row) => router.delete(damagedItemsRoutes.destroy(row.id)),
        class: 'hover:text-red-600 hover:bg-red-50',
    },
];
</script>

<template>

    <Head title="Damaged Items" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Damaged Items</h1>
                    <p class="text-sm text-muted-foreground">Track damaged inventory</p>
                </div>
            </div>

            <Filters :search-value="filters.search as string" :filters="filterConfigs"
                search-placeholder="Search by remarks..." @update:search="(value) => updateFilter('search', value)"
                @update:filter="(key, value) => updateFilter(key, value, true)" @reset="resetFilters" />

            <DataTable :data="damagedItems.data" :columns="columns" :actions="actions"
                empty-message="No damaged items found."
                empty-description="Mark items as damaged from the Items page to track them here.">

                <template #cell-status="{ value }">
                    <span :class="[
                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                        value === 'resellable' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                    ]">
                        {{ value }}
                    </span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div v-if="damagedItems.total > 10">
                <Pagination :pagination="damagedItems" />
            </div>
        </div>
    </AppLayout>
</template>
