<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import LinkButton from '@/components/LinkButton.vue';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import reportsRoutes from '@/routes/admin/reports';
import type { DataTableColumn } from '@/types';
import { type BreadcrumbItem } from '@/types';
import type { BillingMetrics } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import {
    AlertCircle,
    CheckCircle,
    DollarSign,
    FileText,
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reportsRoutes.index().url,
    },
    {
        title: 'Billing Report',
        href: reportsRoutes.billing().url,
    },
];

interface Props {
    report: BillingMetrics;
}

defineProps<Props>();
const { formatCurrency, formatDate } = useFormatters();

const outstandingColumns: DataTableColumn[] = [
    {
        label: 'Billing Number',
        key: 'billing_number',
        class: 'font-medium',
    },
    {
        label: 'Customer',
        key: 'customer_name',
    },
    {
        label: 'Payment Method',
        key: 'payment_method',
        class: 'capitalize',
    },
    {
        label: 'Amount',
        key: 'amount',
        align: 'right',
        render: (value) => formatCurrency(value),
    },
    {
        label: 'Created At',
        key: 'created_at',
        render: (value) => formatDate(value),
    },
];
</script>

<template>
    <Head title="Admin - Billing Report" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Billing Report</h1>
                    <p class="text-sm text-muted-foreground">
                        Review billing and payment collection
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
                                Total Billings
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ report.summary.total_billings }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <FileText class="h-6 w-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Total Amount
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{
                                    formatCurrency(report.summary.total_amount)
                                }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-3">
                            <DollarSign class="h-6 w-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Paid Amount
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ formatCurrency(report.summary.paid_amount) }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <CheckCircle class="h-6 w-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Unpaid Amount
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{
                                    formatCurrency(report.summary.unpaid_amount)
                                }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-orange-50 p-3">
                            <AlertCircle class="h-6 w-6 text-orange-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Status Breakdown -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 text-lg font-semibold">
                    Payment Status Breakdown
                </h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-lg border p-4">
                        <div class="mb-2 flex items-center gap-2">
                            <CheckCircle class="h-5 w-5 text-green-600" />
                            <span class="font-medium">Paid Billings</span>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Count: {{ report.summary.paid_count }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-green-600">
                            {{ formatCurrency(report.summary.paid_amount) }}
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <div class="mb-2 flex items-center gap-2">
                            <AlertCircle class="h-5 w-5 text-orange-600" />
                            <span class="font-medium">Unpaid Billings</span>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Count: {{ report.summary.unpaid_count }}
                        </p>
                        <p class="mt-1 text-lg font-bold text-orange-600">
                            {{ formatCurrency(report.summary.unpaid_amount) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Outstanding Payments -->
            <div class="rounded-lg border bg-white p-6">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold">
                    <AlertCircle class="h-5 w-5 text-warning" />
                    Outstanding Payments
                </h2>
                <DataTable
                    :data="report.outstanding_payments"
                    :columns="outstandingColumns"
                    empty-message="No outstanding payments found."
                >
                    <!-- Customer Name -->
                    <template #cell-customer_name="{ row }">
                        <span>{{ row.order?.user?.name || 'N/A' }}</span>
                    </template>

                    <!-- Payment Method -->
                    <template #cell-payment_method="{ row }">
                        <span class="capitalize">{{
                            row.order?.payment_method?.replace('_', ' ') ||
                            'N/A'
                        }}</span>
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
