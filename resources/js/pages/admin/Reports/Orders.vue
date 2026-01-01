<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import LinkButton from '@/components/LinkButton.vue';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import reportsRoutes from '@/routes/admin/reports';
import type { DataTableColumn } from '@/types';
import { type BreadcrumbItem } from '@/types';
import type { OrderMetrics } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import { CheckCircle, Package, ShoppingCart, Truck } from 'lucide-vue-next';

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

defineProps<Props>();
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
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Order Report</h1>
                    <p class="text-sm text-muted-foreground">
                        Track order status and fulfillment metrics
                    </p>
                </div>
                <LinkButton
                    :href="reportsRoutes.index().url"
                    mode="back"
                    label="Go back"
                />
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Total Orders
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.fulfillment.total_orders }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <ShoppingCart class="h-6 w-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Pending Orders
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.fulfillment.pending_orders }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-orange-50 p-3">
                            <Package class="h-6 w-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Shipped</p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.fulfillment.shipped_orders }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-3">
                            <Truck class="h-6 w-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Delivered
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.fulfillment.delivered_orders }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <CheckCircle class="h-6 w-6 text-green-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fulfillment Breakdown -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">
                    Order Fulfillment Breakdown
                </h2>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div class="rounded-lg border p-4">
                        <p class="text-sm text-muted-foreground">Confirmed</p>
                        <p class="mt-1 text-xl font-bold">
                            {{ report.fulfillment.confirmed_orders }}
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <p class="text-sm text-muted-foreground">Paid</p>
                        <p class="mt-1 text-xl font-bold">
                            {{ report.fulfillment.paid_orders }}
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <p class="text-sm text-muted-foreground">Assembled</p>
                        <p class="mt-1 text-xl font-bold">
                            {{ report.fulfillment.assembled_orders }}
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <p class="text-sm text-muted-foreground">Cancelled</p>
                        <p class="mt-1 text-xl font-bold text-destructive">
                            {{ report.fulfillment.cancelled_orders }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Orders by Status -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">Orders by Status</h2>
                <DataTable
                    :data="report.by_status"
                    :columns="statusColumns"
                    empty-message="No order data found."
                />
            </div>

            <!-- Orders by Delivery Method -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">
                    Orders by Delivery Method
                </h2>
                <DataTable
                    :data="report.by_delivery_method"
                    :columns="deliveryMethodColumns"
                    empty-message="No delivery method data found."
                />
            </div>
        </div>
    </AppLayout>
</template>
