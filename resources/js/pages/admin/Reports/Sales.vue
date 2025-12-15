<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { SalesMetrics } from '@/types/admin';
import { useFormatters } from '@/composables/useFormatters';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn } from '@/types/admin';
import { TrendingUp, DollarSign, ShoppingBag, CreditCard } from 'lucide-vue-next';
import reportsRoutes from '@/routes/admin/reports';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reportsRoutes.index().url,
    },
    {
        title: 'Sales Report',
        href: reportsRoutes.sales().url,
    },
];

interface Props {
    report: SalesMetrics;
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();

const topItemsColumns: DataTableColumn[] = [
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
        label: 'Quantity Sold',
        key: 'total_quantity',
        align: 'right',
    },
    {
        label: 'Revenue',
        key: 'total_revenue',
        align: 'right',
        render: (value) => formatCurrency(value),
    },
];
</script>

<template>

    <Head title="Sales Report" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Sales Report</h1>
                    <p class="text-sm text-muted-foreground">View sales performance and revenue analytics</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Orders</p>
                            <p class="text-2xl font-bold mt-1">{{ report.summary.total_orders }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <ShoppingBag class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Revenue</p>
                            <p class="text-2xl font-bold mt-1">{{ formatCurrency(report.summary.total_revenue) }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <DollarSign class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Average Order Value</p>
                            <p class="text-2xl font-bold mt-1">{{ formatCurrency(report.summary.average_order_value) }}
                            </p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <TrendingUp class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales by Payment Method -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Sales by Payment Method</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div v-for="method in report.by_payment_method" :key="method.payment_method"
                        class="border rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <CreditCard class="w-5 h-5 text-primary" />
                            <span class="font-medium capitalize">{{ method.payment_method.replace('_', ' ') }}</span>
                        </div>
                        <p class="text-sm text-muted-foreground">Orders: {{ method.order_count }}</p>
                        <p class="text-lg font-bold text-primary mt-1">{{ formatCurrency(method.revenue) }}</p>
                    </div>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Top Selling Items</h2>
                <DataTable :data="report.top_items" :columns="topItemsColumns" empty-message="No sales data found." />
            </div>

            <!-- Sales by Period -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Sales Trend</h2>
                <div class="space-y-2">
                    <div v-for="period in report.by_period" :key="period.period"
                        class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                        <div>
                            <p class="font-medium">{{ period.period }}</p>
                            <p class="text-sm text-muted-foreground">{{ period.order_count }} orders</p>
                        </div>
                        <p class="text-lg font-bold text-primary">{{ formatCurrency(period.revenue) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
