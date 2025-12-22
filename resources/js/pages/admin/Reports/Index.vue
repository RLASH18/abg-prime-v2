<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ReportsLayout from '@/layouts/reports/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useFormatters } from '@/composables/useFormatters';
import type { ReportOverview } from '@/types/admin';
import reportsRoutes from '@/routes/admin/reports';
import HeadingSmall from '@/components/HeadingSmall.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reportsRoutes.index().url,
    },
];

interface Props {
    overview: ReportOverview;
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Reports" />

        <ReportsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Overview" description="Quick summary of all reports" />

                <!-- Sales Summary -->
                <div class="rounded-lg border bg-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Sales Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Orders</p>
                            <p class="text-2xl font-bold mt-1">{{ overview.sales_summary.total_orders }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Total Revenue</p>
                            <p class="text-2xl font-bold mt-1">
                                {{ formatCurrency(overview.sales_summary.total_revenue) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Avg Order Value</p>
                            <p class="text-2xl font-bold mt-1">
                                {{ formatCurrency(overview.sales_summary.average_order_value) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Inventory Summary -->
                <div class="rounded-lg border bg-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Inventory Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Items</p>
                            <p class="text-2xl font-bold mt-1">
                                {{ overview.inventory_summary.total_items }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Stock Value</p>
                            <p class="text-2xl font-bold mt-1">
                                {{ formatCurrency(overview.inventory_summary.total_stock_value) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Low Stock Items</p>
                            <p class="text-2xl font-bold mt-1 text-warning">
                                {{ overview.inventory_summary.low_stock_count }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="rounded-lg border bg-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Total</p>
                            <p class="text-xl font-bold mt-1">
                                {{ overview.order_summary.total_orders }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Pending</p>
                            <p class="text-xl font-bold mt-1 text-orange-600">
                                {{ overview.order_summary.pending_orders }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Delivered</p>
                            <p class="text-xl font-bold mt-1 text-green-600">
                                {{ overview.order_summary.delivered_orders }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Cancelled</p>
                            <p class="text-xl font-bold mt-1 text-destructive">
                                {{ overview.order_summary.cancelled_orders }}</p>
                        </div>
                    </div>
                </div>

                <!-- Billing Summary -->
                <div class="rounded-lg border bg-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Billing Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Billings</p>
                            <p class="text-2xl font-bold mt-1">
                                {{ overview.billing_summary.total_billings }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Paid Amount</p>
                            <p class="text-2xl font-bold mt-1 text-green-600">
                                {{ formatCurrency(overview.billing_summary.paid_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Unpaid Amount</p>
                            <p class="text-2xl font-bold mt-1 text-orange-600">
                                {{ formatCurrency(overview.billing_summary.unpaid_amount) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Delivery Summary -->
                <div class="rounded-lg border bg-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Delivery Summary</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Total</p>
                            <p class="text-xl font-bold mt-1">
                                {{ overview.delivery_summary.total_deliveries }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Pending</p>
                            <p class="text-xl font-bold mt-1 text-orange-600">
                                {{ overview.delivery_summary.pending_deliveries }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">In Transit</p>
                            <p class="text-xl font-bold mt-1 text-blue-600">
                                {{ overview.delivery_summary.in_transit_deliveries }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Delivered</p>
                            <p class="text-xl font-bold mt-1 text-green-600">
                                {{ overview.delivery_summary.delivered_deliveries }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </ReportsLayout>
    </AppLayout>
</template>
