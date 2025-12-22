<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { OrderMetrics } from '@/types/admin';
import { useFormatters } from '@/composables/useFormatters';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn } from '@/types';
import { ShoppingCart, Package, Truck, CheckCircle } from 'lucide-vue-next';
import reportsRoutes from '@/routes/admin/reports';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reportsRoutes.index().url,
    },
    {
        title: 'Order Report',
        href: reportsRoutes.orders().url,
    },
];

interface Props {
    report: OrderMetrics;
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();

const statusColumns: DataTableColumn[] = [
    {
        label: 'Status',
        key: 'status',
        class: 'font-medium capitalize',
        render: (value) => value.replace('_', ' '),
    },
    {
        label: 'Order Count',
        key: 'count',
        align: 'right',
    },
    {
        label: 'Total Amount',
        key: 'total_amount',
        align: 'right',
        render: (value) => formatCurrency(value),
    },
];

const deliveryMethodColumns: DataTableColumn[] = [
    {
        label: 'Delivery Method',
        key: 'delivery_method',
        class: 'font-medium capitalize',
    },
    {
        label: 'Order Count',
        key: 'count',
        align: 'right',
    },
    {
        label: 'Total Amount',
        key: 'total_amount',
        align: 'right',
        render: (value) => formatCurrency(value),
    },
];
</script>

<template>

    <Head title="Order Report" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Order Report</h1>
                    <p class="text-sm text-muted-foreground">Track order status and fulfillment metrics</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Orders</p>
                            <p class="text-2xl font-bold mt-1">{{ report.fulfillment.total_orders }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <ShoppingCart class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Pending Orders</p>
                            <p class="text-2xl font-bold mt-1">{{ report.fulfillment.pending_orders }}</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <Package class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Shipped</p>
                            <p class="text-2xl font-bold mt-1">{{ report.fulfillment.shipped_orders }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <Truck class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Delivered</p>
                            <p class="text-2xl font-bold mt-1">{{ report.fulfillment.delivered_orders }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <CheckCircle class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fulfillment Breakdown -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Order Fulfillment Breakdown</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Confirmed</p>
                        <p class="text-xl font-bold mt-1">{{ report.fulfillment.confirmed_orders }}</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Paid</p>
                        <p class="text-xl font-bold mt-1">{{ report.fulfillment.paid_orders }}</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Assembled</p>
                        <p class="text-xl font-bold mt-1">{{ report.fulfillment.assembled_orders }}</p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Cancelled</p>
                        <p class="text-xl font-bold text-destructive mt-1">{{ report.fulfillment.cancelled_orders }}</p>
                    </div>
                </div>
            </div>

            <!-- Orders by Status -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Orders by Status</h2>
                <DataTable :data="report.by_status" :columns="statusColumns" empty-message="No order data found." />
            </div>

            <!-- Orders by Delivery Method -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Orders by Delivery Method</h2>
                <DataTable :data="report.by_delivery_method" :columns="deliveryMethodColumns"
                    empty-message="No delivery method data found." />
            </div>
        </div>
    </AppLayout>
</template>
