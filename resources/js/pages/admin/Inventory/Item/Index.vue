<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type FilterConfig, type PaginationData, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import LinkButton from '@/components/LinkButton.vue';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn, DataTableAction, InventoryItem } from '@/types/admin';
import { AlertTriangle, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { useFilters } from '@/composables/useFilters';
import Filters from '@/components/Filters.vue';
import itemsRoutes from '@/routes/admin/items';
import { ref } from 'vue';
import MarkAsDamagedModal from '@/pages/admin/Inventory/DamagedItem/MarkAsDamagedModal.vue';
import { useFormatters } from '@/composables/useFormatters';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Items',
        href: itemsRoutes.index().url,
    },
];

interface Props {
    items: PaginationData & {
        data: InventoryItem[];
    };
    filters: {
        search?: string;
        category?: string;
        stock_status?: string;
    };
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();

// Modal state
const showDamageModal = ref(false);
const selectedItem = ref<InventoryItem | null>(null);

const openDamageModal = (item: InventoryItem) => {
    selectedItem.value = item;
    showDamageModal.value = true;
};

// Initialize filters
const { filters, updateFilter, resetFilters } = useFilters(
    itemsRoutes.index().url,
    {
        search: props.filters.search || '',
        category: props.filters.category || '',
        stock_status: props.filters.stock_status || '',
    }
);

// Filter configurations
const filterConfigs: FilterConfig[] = [
    {
        label: 'Category',
        key: 'category',
        options: [
            { label: 'Hand Tools', value: 'Hand Tools' },
            { label: 'Power Tools', value: 'Power Tools' },
            { label: 'Construction Materials', value: 'Construction Materials' },
            { label: 'Locks and Security', value: 'Locks and Security' },
            { label: 'Plumbing', value: 'Plumbing' },
            { label: 'Electrical', value: 'Electrical' },
            { label: 'Paint and Finishes', value: 'Paint and Finishes' },
            { label: 'Chemicals', value: 'Chemicals' },
        ],
        placeholder: 'All Categories',
    },
    {
        label: 'Stock Status',
        key: 'stock_status',
        options: [
            { label: 'Out of Stock', value: 'out_of_stock' },
            { label: 'Low Stock', value: 'low_stock' },
            { label: 'In Stock', value: 'in_stock' },
        ],
        placeholder: 'All Stock Levels',
    },
];

const columns: DataTableColumn<InventoryItem>[] = [
    {
        label: 'ID',
        key: 'id',
        render: (value) => `#${value.toString().padStart(4, '0')}`,
        class: 'text-gray-700'
    },
    {
        label: 'Item Code',
        key: 'item_code',
    },
    {
        label: 'Item Name',
        key: 'item_name',
        class: 'font-medium',
    },
    {
        label: 'Brand',
        key: 'brand_name',
    },
    {
        label: 'Category',
        key: 'category',
    },
    {
        label: 'Unit Price',
        key: 'unit_price',
        align: 'right',
        render: (value) => formatCurrency(value),
    },
    {
        label: 'Quantity',
        key: 'quantity',
        align: 'right',
    },
    {
        label: 'Restock Threshold',
        key: 'restock_threshold',
        align: 'right',
    },
];

const actions: DataTableAction<InventoryItem>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (row) => {
            router.visit(itemsRoutes.show(row.id).url);
        },
        class: 'hover:text-blue-600 hover:bg-blue-50'
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (row) => {
            router.visit(itemsRoutes.edit(row.id).url);
        },
        class: 'hover:text-green-600 hover:bg-green-50'
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (row) => {
            router.delete(itemsRoutes.destroy(row.id).url);
        },
        class: 'hover:text-red-600 hover:bg-red-50'
    },
    {
        label: 'Mark as Damaged',
        icon: AlertTriangle,
        onClick: (row) => openDamageModal(row),
        class: 'hover:text-orange-600 hover:bg-orange-50'
    }
];
</script>

<template>

    <Head title="Items" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Items</h1>
                    <p class="text-sm text-muted-foreground">Manage your inventory items</p>
                </div>
                <LinkButton :href="itemsRoutes.create().url" label="Add an Item" />
            </div>

            <!-- Filters -->
            <Filters :search-value="filters.search as string" :filters="filterConfigs"
                search-placeholder="Search by name, code, or brand..."
                @update:search="(value) => updateFilter('search', value)"
                @update:filter="(key, value) => updateFilter(key, value, true)" @reset="resetFilters" />

            <!-- items Table -->
            <DataTable :data="items.data" :columns="columns" :actions="actions" empty-message="No items items found.">

                <!-- Item name -->
                <template #cell-item_name="{ value }">
                    <span class="max-w-[100px] truncate block font-medium" :title="value">
                        {{ value }}
                    </span>
                </template>

                <!-- Brand Name -->
                <template #cell-brand_name="{ value }">
                    <span class="max-w-[100px] truncate block" :title="value">
                        {{ value }}
                    </span>
                </template>

                <!-- Custom slot for category with badge -->
                <template #cell-category="{ value }">
                    <span
                        class="inline-flex items-center rounded-full border border-primary/10 bg-primary/10 px-2 py-1 text-xs font-medium text-primary">
                        {{ value }}
                    </span>
                </template>

                <!-- Custom slot for quantity with low stock warning -->
                <template #cell-quantity="{ value, row }">
                    <span v-if="value <= 0" class="text-destructive font-semibold" title="Out of Stock">
                        {{ value }}
                    </span>
                    <span v-else-if="value <= row.restock_threshold" class="text-warning font-semibold"
                        title="Low Stock">
                        {{ value }}
                    </span>
                    <span v-else class="text-primary font-semibold" title="In Stock">
                        {{ value }}
                    </span>
                </template>
            </DataTable>

            <!-- Pagination -->
            <Pagination :pagination="items" />
        </div>
    </AppLayout>

    <MarkAsDamagedModal v-model:open="showDamageModal" :item="selectedItem" />
</template>
