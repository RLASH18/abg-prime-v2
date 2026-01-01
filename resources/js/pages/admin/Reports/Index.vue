<script setup lang="ts">
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import reportsRoutes from '@/routes/admin/reports';
import { type BreadcrumbItem } from '@/types';
import type { ReportOverview } from '@/types/admin';
import { Head, Link } from '@inertiajs/vue3';
import {
    BarChart3,
    FileText,
    Package,
    ShoppingCart,
    Truck,
} from 'lucide-vue-next';

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

const reportCards = [
    {
        title: 'Sales Report',
        description: 'View sales performance and revenue analytics',
        icon: BarChart3,
        href: reportsRoutes.sales().url,
        stats: [
            {
                label: 'Total Orders',
                value: props.overview.sales_summary.total_orders,
            },
            {
                label: 'Total Revenue',
                value: formatCurrency(
                    props.overview.sales_summary.total_revenue,
                ),
            },
            {
                label: 'Avg Order Value',
                value: formatCurrency(
                    props.overview.sales_summary.average_order_value,
                ),
            },
        ],
        color: 'blue',
    },
    {
        title: 'Inventory Report',
        description: 'Monitor stock levels and inventory value',
        icon: Package,
        href: reportsRoutes.inventory().url,
        stats: [
            {
                label: 'Total Items',
                value: props.overview.inventory_summary.total_items,
            },
            {
                label: 'Stock Value',
                value: formatCurrency(
                    props.overview.inventory_summary.total_stock_value,
                ),
            },
            {
                label: 'Low Stock Items',
                value: props.overview.inventory_summary.low_stock_count,
            },
        ],
        color: 'green',
    },
    {
        title: 'Order Report',
        description: 'Track order status and fulfillment metrics',
        icon: ShoppingCart,
        href: reportsRoutes.orders().url,
        stats: [
            {
                label: 'Total Orders',
                value: props.overview.order_summary.total_orders,
            },
            {
                label: 'Delivered',
                value: props.overview.order_summary.delivered_orders,
            },
            {
                label: 'Pending',
                value: props.overview.order_summary.pending_orders,
            },
        ],
        color: 'purple',
    },
    {
        title: 'Billing Report',
        description: 'Review billing and payment collection',
        icon: FileText,
        href: reportsRoutes.billing().url,
        stats: [
            {
                label: 'Total Billings',
                value: props.overview.billing_summary.total_billings,
            },
            {
                label: 'Paid Amount',
                value: formatCurrency(
                    props.overview.billing_summary.paid_amount,
                ),
            },
            {
                label: 'Unpaid Amount',
                value: formatCurrency(
                    props.overview.billing_summary.unpaid_amount,
                ),
            },
        ],
        color: 'orange',
    },
    {
        title: 'Delivery Report',
        description: 'Monitor delivery status and performance',
        icon: Truck,
        href: reportsRoutes.delivery().url,
        stats: [
            {
                label: 'Total Deliveries',
                value: props.overview.delivery_summary.total_deliveries,
            },
            {
                label: 'In Transit',
                value: props.overview.delivery_summary.in_transit_deliveries,
            },
            {
                label: 'Delivered',
                value: props.overview.delivery_summary.delivered_deliveries,
            },
        ],
        color: 'indigo',
    },
];

const getColorClasses = (color: string) => {
    const colors: Record<string, { bg: string; text: string; border: string }> =
        {
            blue: {
                bg: 'bg-blue-50',
                text: 'text-blue-600',
                border: 'border-blue-200',
            },
            green: {
                bg: 'bg-green-50',
                text: 'text-green-600',
                border: 'border-green-200',
            },
            purple: {
                bg: 'bg-purple-50',
                text: 'text-purple-600',
                border: 'border-purple-200',
            },
            orange: {
                bg: 'bg-orange-50',
                text: 'text-orange-600',
                border: 'border-orange-200',
            },
            indigo: {
                bg: 'bg-indigo-50',
                text: 'text-indigo-600',
                border: 'border-indigo-200',
            },
        };
    return colors[color] || colors.blue;
};
</script>

<template>
    <Head title="Reports" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Reports</h1>
                    <p class="text-sm text-muted-foreground">
                        View comprehensive business analytics and insights
                    </p>
                </div>
            </div>

            <!-- Report Cards Grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="card in reportCards"
                    :key="card.title"
                    :href="card.href"
                    class="group block"
                >
                    <div
                        :class="[
                            'rounded-lg border p-6 transition-all duration-200',
                            'hover:scale-[1.02] hover:shadow-lg',
                            getColorClasses(card.color).border,
                            'bg-white',
                        ]"
                    >
                        <!-- Card Header -->
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <h3
                                    class="text-lg font-semibold text-gray-900 transition-colors group-hover:text-primary"
                                >
                                    {{ card.title }}
                                </h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ card.description }}
                                </p>
                            </div>
                            <div
                                :class="[
                                    'rounded-lg p-3',
                                    getColorClasses(card.color).bg,
                                ]"
                            >
                                <component
                                    :is="card.icon"
                                    :class="[
                                        'h-6 w-6',
                                        getColorClasses(card.color).text,
                                    ]"
                                />
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="space-y-2">
                            <div
                                v-for="stat in card.stats"
                                :key="stat.label"
                                class="flex items-center justify-between"
                            >
                                <span class="text-sm text-gray-600">{{
                                    stat.label
                                }}</span>
                                <span
                                    class="text-sm font-semibold text-gray-900"
                                    >{{ stat.value }}</span
                                >
                            </div>
                        </div>

                        <!-- View Details Link -->
                        <div class="mt-4 border-t pt-4">
                            <span
                                class="text-sm font-medium text-primary group-hover:underline"
                            >
                                View Detailed Report →
                            </span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
