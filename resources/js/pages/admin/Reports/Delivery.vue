<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { DeliveryMetrics } from '@/types/admin';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn } from '@/types';
import { Truck, Package, CheckCircle, Clock } from 'lucide-vue-next';
import reportsRoutes from '@/routes/admin/reports';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reportsRoutes.index().url,
    },
    {
        title: 'Delivery Report',
        href: reportsRoutes.delivery().url,
    },
];

interface Props {
    report: DeliveryMetrics;
}

const props = defineProps<Props>();

const statusColumns: DataTableColumn[] = [
    {
        label: 'Status',
        key: 'status',
        class: 'font-medium capitalize',
        render: (value) => value.replace('_', ' '),
    },
    {
        label: 'Count',
        key: 'count',
        align: 'right',
    },
];
</script>

<template>

    <Head title="Delivery Report" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Delivery Report</h1>
                    <p class="text-sm text-muted-foreground">Monitor delivery status and performance</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Deliveries</p>
                            <p class="text-2xl font-bold mt-1">{{ report.performance.total_deliveries }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <Truck class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Pending</p>
                            <p class="text-2xl font-bold mt-1">{{ report.performance.pending_deliveries }}</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <Clock class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">In Transit</p>
                            <p class="text-2xl font-bold mt-1">{{ report.performance.in_transit_deliveries }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <Package class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Delivered</p>
                            <p class="text-2xl font-bold mt-1">{{ report.performance.delivered_deliveries }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <CheckCircle class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Delivery Performance</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Deliveries with Proof</p>
                        <p class="text-2xl font-bold mt-1">{{ report.performance.with_proof }}</p>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ report.performance.total_deliveries > 0
                                ? Math.round((report.performance.with_proof / report.performance.total_deliveries) * 100)
                                : 0 }}% of total
                        </p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-muted-foreground">Completion Rate</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ report.performance.total_deliveries > 0
                                ? Math.round((report.performance.delivered_deliveries / report.performance.total_deliveries)
                                    * 100)
                                : 0 }}%
                        </p>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ report.performance.delivered_deliveries }} of {{ report.performance.total_deliveries }}
                            deliveries
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status Overview -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Delivery Status Overview</h2>
                <DataTable :data="report.status_overview" :columns="statusColumns"
                    empty-message="No delivery data found." />
            </div>
        </div>
    </AppLayout>
</template>
