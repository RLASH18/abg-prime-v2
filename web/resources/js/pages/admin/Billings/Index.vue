<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import Filters from '@/components/Filters.vue';
import Pagination from '@/components/Pagination.vue';
import { useFilters } from '@/composables/useFilters';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import billingsRoutes from '@/routes/admin/billings';
import type { DataTableAction, DataTableColumn } from '@/types';
import {
    type BreadcrumbItem,
    type FilterConfig,
    type PaginationData,
} from '@/types';
import type { Billing } from '@/types/admin';
import { Head, router } from '@inertiajs/vue3';
import { Eye } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billings',
        href: billingsRoutes.index().url,
    },
];

interface Props {
    billings: PaginationData & {
        data: Billing[];
    };
    filters: {
        search?: string;
        status?: string;
        payment_method?: string;
    };
}

const props = defineProps<Props>();
const { formatCurrency, formatDate } = useFormatters();

// Initialize filters
const { filters, updateFilter, resetFilters } = useFilters(
    billingsRoutes.index().url,
    {
        search: props.filters.search || '',
        status: props.filters.status || '',
        payment_method: props.filters.payment_method || '',
    },
);

// Filter configurations
const filterConfigs: FilterConfig[] = [
    {
        label: 'Status',
        key: 'status',
        options: [
            { label: 'Unpaid', value: 'unpaid' },
            { label: 'Paid', value: 'paid' },
            { label: 'Cancelled', value: 'cancelled' },
        ],
        placeholder: 'All Statuses',
    },
    {
        label: 'Payment Method',
        key: 'payment_method',
        options: [
            { label: 'Cash', value: 'cash' },
            { label: 'GCash', value: 'gcash' },
            { label: 'Bank Transfer', value: 'bank_transfer' },
        ],
        placeholder: 'All Payment Methods',
    },
];

const columns: DataTableColumn<Billing>[] = [
    {
        label: 'ID',
        key: 'id',
        render: (value) => `#${value.toString().padStart(4, '0')}`,
    },
    {
        label: 'Billing Number',
        key: 'billing_number',
        class: 'font-medium',
    },
    {
        label: 'Order ID',
        key: 'order_id',
    },
    {
        label: 'Customer',
        key: 'customer_name',
        render: (_, row) => row.order?.user?.name || '',
    },
    {
        label: 'Amount',
        key: 'amount',
        align: 'right',
        render: (value) => formatCurrency(parseFloat(value)),
    },
    {
        label: 'Payment Method',
        key: 'payment_method',
        render: (_, row) => row.order?.payment_method || '',
    },
    {
        label: 'Status',
        key: 'status',
    },
    {
        label: 'Paid At',
        key: 'paid_at',
        render: (value) => (value ? formatDate(value) : '-'),
    },
];

const actions: DataTableAction<Billing>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (row) => {
            router.visit(billingsRoutes.show(row.id));
        },
        class: 'hover:text-blue-600 hover:bg-blue-50',
    },
];
</script>

<template>
    <Head title="Admin - Billings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Billings</h1>
                    <p class="text-sm text-muted-foreground">
                        Manage billing records
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <Filters
                :search-value="filters.search as string"
                :filters="filterConfigs"
                search-placeholder="Search by billing number or customer name..."
                @update:search="(value) => updateFilter('search', value)"
                @update:filter="(key, value) => updateFilter(key, value, true)"
                @reset="resetFilters"
            />

            <!-- Billings Table -->
            <DataTable
                :data="billings.data"
                :columns="columns"
                :actions="actions"
                empty-message="No billings found."
            >
                <!-- Billing Number -->
                <template #cell-billing_number="{ value }">
                    <span class="font-mono font-medium text-primary">
                        {{ value }}
                    </span>
                </template>

                <!-- Customer Name -->
                <template #cell-customer_name="{ row }">
                    <span
                        class="block max-w-[150px] truncate"
                        :title="row.order?.user?.name"
                    >
                        {{ row.order?.user?.name || '-' }}
                    </span>
                </template>

                <!-- Custom slot for status with badge -->
                <template #cell-status="{ value }">
                    <span
                        v-if="value === 'paid'"
                        class="inline-flex items-center rounded-full border border-green-500/10 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-700"
                    >
                        Paid
                    </span>
                    <span
                        v-else-if="value === 'unpaid'"
                        class="inline-flex items-center rounded-full border border-yellow-500/10 bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-700"
                    >
                        Unpaid
                    </span>
                    <span
                        v-else-if="value === 'cancelled'"
                        class="inline-flex items-center rounded-full border border-red-500/10 bg-red-500/10 px-2 py-1 text-xs font-medium text-red-700"
                    >
                        Cancelled
                    </span>
                </template>

                <!-- Payment Method Badge -->
                <template #cell-payment_method="{ row }">
                    <span
                        v-if="row.order?.payment_method === 'cash'"
                        class="inline-flex items-center rounded-full border border-blue-500/10 bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-700"
                    >
                        Cash
                    </span>
                    <span
                        v-else-if="row.order?.payment_method === 'gcash'"
                        class="inline-flex items-center rounded-full border border-purple-500/10 bg-purple-500/10 px-2 py-1 text-xs font-medium text-purple-700"
                    >
                        GCash
                    </span>
                    <span
                        v-else-if="
                            row.order?.payment_method === 'bank_transfer'
                        "
                        class="inline-flex items-center rounded-full border border-green-500/10 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-700"
                    >
                        Bank Transfer
                    </span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div v-if="billings.total > 10">
                <Pagination :pagination="billings" />
            </div>
        </div>
    </AppLayout>
</template>
