<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { InventoryMetrics } from '@/types/admin';
import { useFormatters } from '@/composables/useFormatters';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn } from '@/types/admin';
import { Package, AlertTriangle, DollarSign, Users } from 'lucide-vue-next';
import reportsRoutes from '@/routes/admin/reports';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reportsRoutes.index().url,
    },
    {
        title: 'Inventory Report',
        href: reportsRoutes.inventory().url,
    },
];

interface Props {
    report: InventoryMetrics;
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();

const lowStockColumns: DataTableColumn[] = [
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
        label: 'Quantity',
        key: 'quantity',
        align: 'right',
        class: 'text-warning font-semibold',
    },
    {
        label: 'Restock Threshold',
        key: 'restock_threshold',
        align: 'right',
    },
];

const supplierColumns: DataTableColumn[] = [
    {
        label: 'Supplier Name',
        key: 'supplier_name',
        class: 'font-medium',
    },
    {
        label: 'Item Count',
        key: 'item_count',
        align: 'right',
    },
    {
        label: 'Total Quantity',
        key: 'total_quantity',
        align: 'right',
    },
    {
        label: 'Total Value',
        key: 'total_value',
        align: 'right',
        render: (value) => formatCurrency(value),
    },
];
</script>

<template>

    <Head title="Inventory Report" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Inventory Report</h1>
                    <p class="text-sm text-muted-foreground">Monitor stock levels and inventory value</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Items</p>
                            <p class="text-2xl font-bold mt-1">{{ report.total_items }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <Package class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Stock Value</p>
                            <p class="text-2xl font-bold mt-1">{{ formatCurrency(report.total_stock_value) }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <DollarSign class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Low Stock Items</p>
                            <p class="text-2xl font-bold mt-1">{{ report.low_stock_items.length }}</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <AlertTriangle class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Damaged Items Loss</p>
                            <p class="text-2xl font-bold mt-1">{{ formatCurrency(report.damaged_summary.estimated_loss)
                            }}</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg">
                            <AlertTriangle class="w-6 h-6 text-red-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <AlertTriangle class="w-5 h-5 text-warning" />
                    Low Stock Items
                </h2>
                <DataTable :data="report.low_stock_items" :columns="lowStockColumns"
                    empty-message="No low stock items found." />
            </div>

            <!-- Inventory by Supplier -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <Users class="w-5 h-5 text-primary" />
                    Inventory by Supplier
                </h2>
                <DataTable :data="report.by_supplier" :columns="supplierColumns"
                    empty-message="No supplier data found." />
            </div>

            <!-- Damaged Items Summary -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Damaged Items Summary</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Total Records</p>
                        <p class="text-2xl font-bold mt-1">{{ report.damaged_summary.total_records }}</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Total Damaged Quantity</p>
                        <p class="text-2xl font-bold mt-1">{{ report.damaged_summary.total_damaged_quantity }}</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Estimated Loss</p>
                        <p class="text-2xl font-bold text-destructive mt-1">{{
                            formatCurrency(report.damaged_summary.estimated_loss) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
