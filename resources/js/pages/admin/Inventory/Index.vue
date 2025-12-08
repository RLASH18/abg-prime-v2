<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import inventory from '@/routes/admin/inventory';
import LinkButton from '@/components/LinkButton.vue';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn, DataTableAction, InventoryItem } from '@/types/admin';
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: inventory.index().url,
    },
];

interface Props {
    items?: InventoryItem[];
}

const props = withDefaults(defineProps<Props>(), {
    items: () => [],
});

const columns: DataTableColumn<InventoryItem>[] = [
    {
        label: 'ID',
        key: 'id',
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
        render: (value) => `₱ ${parseFloat(value).toFixed(2)}`,
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
            router.visit(inventory.show(row.id).url);
        },
        class: 'hover:text-blue-600 hover:bg-blue-50'
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (row) => {
            router.visit(inventory.edit(row.id).url);
        },
        class: 'hover:text-green-600 hover:bg-green-50'
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (row) => {
            router.visit(inventory.destroy(row.id).url);
        },
        class: 'hover:text-red-600 hover:bg-red-50'
    },
];
</script>

<template>

    <Head title="Inventory" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Inventory</h1>
                    <p class="text-sm text-muted-foreground">Manage your inventory items</p>
                </div>
                <LinkButton :href="inventory.create().url" label="Add an Item" />
            </div>

            <DataTable :data="items" :columns="columns" :actions="actions"
                empty-message="No inventory items found. Click 'Add an Item' to get started.">

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
                        class="inline-flex items-center rounded-md bg-primary/10 px-2 py-1 text-xs font-medium text-primary">
                        {{ value }}
                    </span>
                </template>

                <!-- Custom slot for quantity with low stock warning -->
                <template #cell-quantity="{ value, row }">
                    <span v-if="value <= 0" class="text-destructive font-semibold">
                        {{ value }}
                    </span>
                    <span v-else-if="value <= row.restock_threshold" class="text-warning font-semibold">
                        {{ value }}
                    </span>
                    <span v-else class="text-primary font-semibold">
                        {{ value }}
                    </span>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
