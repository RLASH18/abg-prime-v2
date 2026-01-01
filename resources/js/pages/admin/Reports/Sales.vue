<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import LinkButton from '@/components/LinkButton.vue';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import reportsRoutes from '@/routes/admin/reports';
import type { DataTableColumn } from '@/types';
import { type BreadcrumbItem } from '@/types';
import type { SalesMetrics } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import {
    CreditCard,
    DollarSign,
    ShoppingBag,
    TrendingUp,
} from 'lucide-vue-next';

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
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Sales Report</h1>
                    <p class="text-sm text-muted-foreground">
                        View sales performance and revenue analytics
                    </p>
                </div>
                <LinkButton
                    :href="reportsRoutes.index().url"
                    mode="back"
                    label="Go back"
                />
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Total Orders
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.summary.total_orders }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <ShoppingBag class="h-6 w-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Total Revenue
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{
                                    formatCurrency(report.summary.total_revenue)
                                }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <DollarSign class="h-6 w-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Average Order Value
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{
                                    formatCurrency(
                                        report.summary.average_order_value,
                                    )
                                }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-3">
                            <TrendingUp class="h-6 w-6 text-purple-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales by Payment Method -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">
                    Sales by Payment Method
                </h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div
                        v-for="method in report.by_payment_method"
                        :key="method.payment_method"
                        class="rounded-lg border p-4"
                    >
                        <div class="mb-2 flex items-center gap-2">
                            <CreditCard class="h-5 w-5 text-primary" />
                            <span class="font-medium capitalize">{{
                                method.payment_method.replace('_', ' ')
                            }}</span>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Orders: {{ method.order_count }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-primary">
                            {{ formatCurrency(method.revenue) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">Top Selling Items</h2>
                <DataTable
                    :data="report.top_items"
                    :columns="topItemsColumns"
                    empty-message="No sales data found."
                />
            </div>

            <!-- Sales by Period -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">Sales Trend</h2>
                <div class="space-y-2">
                    <div
                        v-for="period in report.by_period"
                        :key="period.period"
                        class="flex items-center justify-between rounded-lg border p-3 hover:bg-gray-50"
                    >
                        <div>
                            <p class="font-medium">{{ period.period }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ period.order_count }} orders
                            </p>
                        </div>
                        <p class="text-lg font-bold text-primary">
                            {{ formatCurrency(period.revenue) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
