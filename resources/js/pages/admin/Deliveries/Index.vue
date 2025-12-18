<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type FilterConfig, type PaginationData, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn, DataTableAction, Delivery } from '@/types/admin';
import { Eye, Pencil } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { useFilters } from '@/composables/useFilters';
import Filters from '@/components/Filters.vue';
import deliveriesRoutes from '@/routes/admin/deliveries';
import { useFormatters } from '@/composables/useFormatters';
import EditDeliveryModal from '@/components/admin/EditDeliveryModal.vue';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Deliveries',
        href: deliveriesRoutes.index().url,
    },
];

interface Props {
    deliveries: PaginationData & {
        data: Delivery[];
    };
    filters: {
        search?: string;
        status?: string;
    };
}

const props = defineProps<Props>();
const { formatDate } = useFormatters();

// Modal state
const isEditModalOpen = ref(false);
const selectedDelivery = ref<Delivery | null>(null);

const openEditModal = (delivery: Delivery) => {
    selectedDelivery.value = delivery;
    isEditModalOpen.value = true;
};

// Initialize filters
const { filters, updateFilter, resetFilters } = useFilters(
    deliveriesRoutes.index().url,
    {
        search: props.filters.search || '',
        status: props.filters.status || '',
    }
);

// Filter configurations
const filterConfigs: FilterConfig[] = [
    {
        label: 'Status',
        key: 'status',
        options: [
            { label: 'Scheduled', value: 'scheduled' },
            { label: 'Rescheduled', value: 'rescheduled' },
            { label: 'In Transit', value: 'in_transit' },
            { label: 'Delivered', value: 'delivered' },
            { label: 'Failed', value: 'failed' },
        ],
        placeholder: 'All Statuses',
    },
];

const columns: DataTableColumn<Delivery>[] = [
    {
        label: 'ID',
        key: 'id',
        render: (value) => `#${value.toString().padStart(4, '0')}`,
        class: 'text-gray-700'
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
        label: 'Driver',
        key: 'driver_name',
        render: (value) => value || '-',
    },
    {
        label: 'Scheduled Date',
        key: 'scheduled_date',
        render: (value) => formatDate(value),
    },
    {
        label: 'Actual Delivery',
        key: 'actual_delivery_date',
        render: (value) => value ? formatDate(value) : '-',
    },
    {
        label: 'Status',
        key: 'status',
    },
];

const actions: DataTableAction<Delivery>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (row) => {
            router.visit(deliveriesRoutes.show(row.id));
        },
        class: 'hover:text-green-600 hover:bg-green-50',
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (row) => {
            openEditModal(row);
        },
        class: 'hover:text-blue-600 hover:bg-blue-50'
    },
];

</script>

<template>

    <Head title="Deliveries" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Deliveries</h1>
                    <p class="text-sm text-muted-foreground">Manage delivery schedules and tracking</p>
                </div>
            </div>

            <!-- Filters -->
            <Filters :search-value="filters.search as string" :filters="filterConfigs"
                search-placeholder="Search by driver name or customer name..."
                @update:search="(value) => updateFilter('search', value)"
                @update:filter="(key, value) => updateFilter(key, value, true)" @reset="resetFilters" />

            <!-- Deliveries Table -->
            <DataTable :data="deliveries.data" :columns="columns" :actions="actions"
                empty-message="No deliveries found.">

                <!-- Customer Name -->
                <template #cell-customer_name="{ row }">
                    <span class="max-w-[150px] truncate block" :title="row.order?.user?.name">
                        {{ row.order?.user?.name || '-' }}
                    </span>
                </template>

                <!-- Driver Name -->
                <template #cell-driver_name="{ value }">
                    <span class="font-medium">
                        {{ value || 'Not Assigned' }}
                    </span>
                </template>

                <!-- Custom slot for status with badge -->
                <template #cell-status="{ value }">
                    <span v-if="value === 'scheduled'"
                        class="inline-flex items-center rounded-full border border-blue-500/10 bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-700">
                        Scheduled
                    </span>
                    <span v-else-if="value === 'rescheduled'"
                        class="inline-flex items-center rounded-full border border-orange-500/10 bg-orange-500/10 px-2 py-1 text-xs font-medium text-orange-700">
                        Rescheduled
                    </span>
                    <span v-else-if="value === 'in_transit'"
                        class="inline-flex items-center rounded-full border border-purple-500/10 bg-purple-500/10 px-2 py-1 text-xs font-medium text-purple-700">
                        In Transit
                    </span>
                    <span v-else-if="value === 'delivered'"
                        class="inline-flex items-center rounded-full border border-green-500/10 bg-green-500/10 px-2 py-1 text-xs font-medium text-green-700">
                        Delivered
                    </span>
                    <span v-else-if="value === 'failed'"
                        class="inline-flex items-center rounded-full border border-red-500/10 bg-red-500/10 px-2 py-1 text-xs font-medium text-red-700">
                        Failed
                    </span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <Pagination :pagination="deliveries" />
        </div>

        <!-- Edit Delivery Modal -->
        <EditDeliveryModal :open="isEditModalOpen" @update:open="isEditModalOpen = $event"
            :delivery="selectedDelivery" />
    </AppLayout>
</template>
