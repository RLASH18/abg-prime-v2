<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import LinkButton from '@/components/LinkButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import reportsRoutes from '@/routes/admin/reports';
import type { DataTableColumn } from '@/types';
import { type BreadcrumbItem } from '@/types';
import type { DeliveryMetrics } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import { CheckCircle, Clock, Package, Truck } from 'lucide-vue-next';

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
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Delivery Report</h1>
                    <p class="text-sm text-muted-foreground">
                        Monitor delivery status and performance
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
                                Total Deliveries
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.performance.total_deliveries }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <Truck class="h-6 w-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Pending</p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.performance.pending_deliveries }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-orange-50 p-3">
                            <Clock class="h-6 w-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                In Transit
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.performance.in_transit_deliveries }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-3">
                            <Package class="h-6 w-6 text-purple-600" />
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
                                {{ report.performance.delivered_deliveries }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <CheckCircle class="h-6 w-6 text-green-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">Delivery Performance</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-lg border p-4">
                        <p class="text-sm text-muted-foreground">
                            Deliveries with Proof
                        </p>
                        <p class="mt-1 text-2xl font-bold">
                            {{ report.performance.with_proof }}
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{
                                report.performance.total_deliveries > 0
                                    ? Math.round(
                                          (report.performance.with_proof /
                                              report.performance
                                                  .total_deliveries) *
                                              100,
                                      )
                                    : 0
                            }}% of total
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <p class="text-sm text-muted-foreground">
                            Completion Rate
                        </p>
                        <p class="mt-1 text-2xl font-bold">
                            {{
                                report.performance.total_deliveries > 0
                                    ? Math.round(
                                          (report.performance
                                              .delivered_deliveries /
                                              report.performance
                                                  .total_deliveries) *
                                              100,
                                      )
                                    : 0
                            }}%
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{ report.performance.delivered_deliveries }} of
                            {{ report.performance.total_deliveries }}
                            deliveries
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status Overview -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">
                    Delivery Status Overview
                </h2>
                <DataTable
                    :data="report.status_overview"
                    :columns="statusColumns"
                    empty-message="No delivery data found."
                />
            </div>
        </div>
    </AppLayout>
</template>
