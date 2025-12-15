<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { BillingMetrics } from '@/types/admin';
import { useFormatters } from '@/composables/useFormatters';
import DataTable from '@/components/DataTable.vue';
import type { DataTableColumn } from '@/types/admin';
import { FileText, DollarSign, CheckCircle, AlertCircle } from 'lucide-vue-next';
import reportsRoutes from '@/routes/admin/reports';

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

const props = defineProps<Props>();
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

    <Head title="Billing Report" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Billing Report</h1>
                    <p class="text-sm text-muted-foreground">Review billing and payment collection</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Billings</p>
                            <p class="text-2xl font-bold mt-1">{{ report.summary.total_billings }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <FileText class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Amount</p>
                            <p class="text-2xl font-bold mt-1">{{ formatCurrency(report.summary.total_amount) }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <DollarSign class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Paid Amount</p>
                            <p class="text-2xl font-bold mt-1">{{ formatCurrency(report.summary.paid_amount) }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <CheckCircle class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Unpaid Amount</p>
                            <p class="text-2xl font-bold mt-1">{{ formatCurrency(report.summary.unpaid_amount) }}</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <AlertCircle class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Status Breakdown -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Payment Status Breakdown</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <CheckCircle class="w-5 h-5 text-green-600" />
                            <span class="font-medium">Paid Billings</span>
                        </div>
                        <p class="text-sm text-muted-foreground">Count: {{ report.summary.paid_count }}</p>
                        <p class="text-lg font-bold text-green-600 mt-1">
                            {{ formatCurrency(report.summary.paid_amount) }}
                        </p>
                    </div>
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <AlertCircle class="w-5 h-5 text-orange-600" />
                            <span class="font-medium">Unpaid Billings</span>
                        </div>
                        <p class="text-sm text-muted-foreground">Count: {{ report.summary.unpaid_count }}</p>
                        <p class="text-lg font-bold text-orange-600 mt-1">
                            {{ formatCurrency(report.summary.unpaid_amount) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Outstanding Payments -->
            <div class="bg-white border rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <AlertCircle class="w-5 h-5 text-warning" />
                    Outstanding Payments
                </h2>
                <DataTable :data="report.outstanding_payments" :columns="outstandingColumns"
                    empty-message="No outstanding payments found.">
                    <!-- Customer Name -->
                    <template #cell-customer_name="{ row }">
                        <span>{{ row.order?.user?.name || 'N/A' }}</span>
                    </template>

                    <!-- Payment Method -->
                    <template #cell-payment_method="{ row }">
                        <span class="capitalize">{{ row.order?.payment_method?.replace('_', ' ') || 'N/A' }}</span>
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
