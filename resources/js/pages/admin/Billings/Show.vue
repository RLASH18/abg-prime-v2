<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import billingsRoutes from '@/routes/admin/billings';
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import {
    Receipt,
    User,
    PhilippinePeso,
    Calendar,
    CreditCard,
    Package,
    Hash,
    CheckCircle2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { Billing } from '@/types/admin';
import { useFormatters } from '@/composables/useFormatters';

interface Props {
    billing: Billing;
}

const props = defineProps<Props>();
const { formatDate, formatCurrency } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Billings',
        href: billingsRoutes.index().url,
    },
    {
        title: 'Billing Details',
        href: billingsRoutes.show(props.billing.id).url,
    },
];

// Compute billing status badge
const billingStatus = computed(() => {
    if (props.billing.status === 'paid') {
        return { label: 'Paid', variant: 'default' as const, class: 'bg-green-500/10 text-green-700 border-green-500/20' };
    } else if (props.billing.status === 'unpaid') {
        return { label: 'Unpaid', variant: 'secondary' as const, class: 'bg-yellow-500/10 text-yellow-700 border-yellow-500/20' };
    }
    return { label: 'Cancelled', variant: 'destructive' as const, class: 'bg-red-500/10 text-red-700 border-red-500/20' };
});

// Format payment method
const formatPaymentMethod = (method: string) => {
    const methods: Record<string, string> = {
        'cash': 'Cash',
        'gcash': 'GCash',
        'bank_transfer': 'Bank Transfer'
    };
    return methods[method] || method;
};

// Calculate subtotal for order items
const subtotal = computed(() => {
    if (!props.billing.order?.order_items) return 0;
    return props.billing.order.order_items.reduce((sum, item) => {
        return sum + (item.quantity * item.unit_price);
    }, 0);
});
</script>

<template>

    <Head :title="`Billing - ${billing.billing_number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Billing Details</h1>
                    <p class="text-sm text-muted-foreground mt-1">Billing Number: {{ billing.billing_number }}</p>
                </div>
                <div class="flex gap-2">
                    <LinkButton :href="billingsRoutes.index().url" mode="back" label="Back to list" />
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Billing Summary -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Billing Status Card -->
                    <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 rounded-lg bg-primary/10">
                                <Receipt :size="24" class="text-primary" />
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Billing Status</p>
                                <Badge :class="billingStatus.class" class="mt-1 rounded-full border">
                                    {{ billingStatus.label }}
                                </Badge>
                            </div>
                        </div>

                        <div class="space-y-4 mt-6">
                            <!-- Amount -->
                            <div class="flex items-center justify-between p-3 rounded-lg bg-primary/5">
                                <span class="text-sm text-muted-foreground">Total Amount</span>
                                <span class="text-xl font-bold text-primary">
                                    {{ formatCurrency(billing.amount) }}
                                </span>
                            </div>

                            <!-- Created Date -->
                            <div class="flex items-start gap-3">
                                <Calendar :size="18" class="text-muted-foreground mt-0.5" />
                                <div>
                                    <p class="text-sm text-muted-foreground">Created At</p>
                                    <p class="font-medium text-sm">{{ formatDate(billing.created_at) }}</p>
                                </div>
                            </div>

                            <!-- Paid Date -->
                            <div v-if="billing.paid_at" class="flex items-start gap-3">
                                <CheckCircle2 :size="18" class="text-green-600 mt-0.5" />
                                <div>
                                    <p class="text-sm text-muted-foreground">Paid At</p>
                                    <p class="font-medium text-sm">{{ formatDate(billing.paid_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Information Card -->
                    <div v-if="billing.order" class="rounded-lg border border-border bg-card shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <Package :size="20" />
                            Order Information
                        </h2>

                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <Hash :size="18" class="text-muted-foreground mt-0.5" />
                                <div>
                                    <p class="text-sm text-muted-foreground">Order ID</p>
                                    <p class="font-medium font-mono">#{{ billing.order.id.toString().padStart(4, '0') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <CreditCard :size="18" class="text-muted-foreground mt-0.5" />
                                <div>
                                    <p class="text-sm text-muted-foreground">Payment Method</p>
                                    <p class="font-medium">{{ formatPaymentMethod(billing.order.payment_method) }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <User :size="18" class="text-muted-foreground mt-0.5" />
                                <div>
                                    <p class="text-sm text-muted-foreground">Customer</p>
                                    <p class="font-medium">{{ billing.order.user?.name || '-' }}</p>
                                    <p v-if="billing.order.user?.email" class="text-xs text-muted-foreground">{{
                                        billing.order.user.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Items -->
                <div class="lg:col-span-2">
                    <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">Order Items</h2>

                        <div v-if="billing.order?.order_items && billing.order.order_items.length > 0"
                            class="space-y-4">
                            <!-- Items Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-border">
                                            <th class="text-left py-3 px-2 text-sm font-semibold text-muted-foreground">
                                                Item</th>
                                            <th
                                                class="text-right py-3 px-2 text-sm font-semibold text-muted-foreground">
                                                Unit Price</th>
                                            <th
                                                class="text-center py-3 px-2 text-sm font-semibold text-muted-foreground">
                                                Quantity</th>
                                            <th
                                                class="text-right py-3 px-2 text-sm font-semibold text-muted-foreground">
                                                Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in billing.order.order_items" :key="item.id"
                                            class="border-b border-border/50 hover:bg-muted/50 transition-colors">
                                            <td class="py-4 px-2">
                                                <div>
                                                    <p class="font-medium">{{ item.item?.item_name || 'Unknown Item' }}
                                                    </p>
                                                    <p v-if="item.item?.item_code"
                                                        class="text-xs text-muted-foreground font-mono">
                                                        {{ item.item.item_code }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="py-4 px-2 text-right font-medium">
                                                {{ formatCurrency(item.unit_price) }}</td>
                                            <td class="py-4 px-2 text-center">
                                                <span
                                                    class="inline-flex items-center justify-center rounded-full bg-primary/10 px-3 py-1 text-sm font-medium">
                                                    {{ item.quantity }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-2 text-right font-semibold text-primary">
                                                {{ formatCurrency(item.quantity * item.unit_price) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Total Summary -->
                            <div class="border-t border-border pt-4 mt-4">
                                <div class="flex justify-end">
                                    <div class="w-full max-w-sm space-y-2">
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-muted-foreground">Subtotal</span>
                                            <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-t border-border">
                                            <span class="text-lg font-semibold">Total</span>
                                            <span class="text-2xl font-bold text-primary">
                                                {{ formatCurrency(billing.amount) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No Items -->
                        <div v-else class="text-center py-12">
                            <Package :size="48" class="text-muted-foreground mx-auto mb-3" />
                            <p class="text-muted-foreground">No order items found</p>
                        </div>
                    </div>

                    <!-- Delivery Information (if applicable) -->
                    <div v-if="billing.order?.delivery_address"
                        class="rounded-lg border border-border bg-card shadow-sm p-6 mt-6">
                        <h2 class="text-lg font-semibold mb-3">Delivery Information</h2>
                        <div class="space-y-2">
                            <div>
                                <p class="text-sm text-muted-foreground">Delivery Method</p>
                                <p class="font-medium capitalize">{{ billing.order.delivery_method }}</p>
                            </div>
                            <div v-if="billing.order.delivery_address">
                                <p class="text-sm text-muted-foreground">Delivery Address</p>
                                <p class="font-medium">{{ billing.order.delivery_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
