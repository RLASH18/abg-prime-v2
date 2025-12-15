<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ordersRoutes from '@/routes/admin/orders';
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import {
    Package,
    User,
    CreditCard,
    Truck,
    MapPin,
    Calendar,
    Hash,
    PhilippinePeso,
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { Order } from '@/types/admin';
import { useFormatters } from '@/composables/useFormatters';

interface Props {
    order: Order;
}

const props = defineProps<Props>();
const { formatCurrency, formatDate } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: ordersRoutes.index().url,
    },
    {
        title: `Order #${props.order.id.toString().padStart(4, '0')}`,
        href: ordersRoutes.show(props.order.id).url,
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending': return 'secondary' as const;
        case 'confirmed': return 'default' as const;
        case 'assembled': return 'default' as const;
        case 'shipped': return 'default' as const;
        case 'delivered': return 'default' as const;
        case 'paid': return 'default' as const;
        case 'cancelled': return 'destructive' as const;
        default: return 'secondary' as const;
    }
};

const subtotal = computed(() => {
    return props.order.order_items?.reduce((sum, item) => {
        return sum + (item.unit_price * item.quantity);
    }, 0) || 0;
});
</script>

<template>

    <Head :title="`Order #${order.id.toString().padStart(4, '0')}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Order #{{ order.id.toString().padStart(4, '0') }}</h1>
                    <p class="text-sm text-muted-foreground mt-1">Placed on {{ formatDate(order.created_at) }}</p>
                </div>
                <div class="flex gap-2">
                    <LinkButton :href="ordersRoutes.index().url" mode="back" label="Back to list" />
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Order Summary -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Status Card -->
                    <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">Order Status</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-muted-foreground mb-2">Current Status</p>
                                <Badge :variant="getStatusColor(order.status)" class="rounded-full capitalize">
                                    {{ order.status }}
                                </Badge>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground mb-1">Total Amount</p>
                                <p class="text-2xl font-bold text-primary">{{ formatCurrency(order.total_amount) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info Card -->
                    <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">Customer Information</h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-primary/10">
                                    <User :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Customer Name</p>
                                    <p class="font-medium">{{ order.user?.name || 'Unknown User' }}</p>
                                </div>
                            </div>
                            <div v-if="order.user?.email" class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-primary/10">
                                    <Hash :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Email</p>
                                    <p class="font-medium">{{ order.user.email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Info Card -->
                    <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">Delivery Information</h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-primary/10">
                                    <Truck :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Delivery Method</p>
                                    <p class="font-medium capitalize">{{ order.delivery_method.replace('_', ' ') }}</p>
                                </div>
                            </div>
                            <div v-if="order.delivery_address" class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-primary/10">
                                    <MapPin :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Delivery Address</p>
                                    <p class="font-medium">{{ order.delivery_address }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-primary/10">
                                    <CreditCard :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Payment Method</p>
                                    <p class="font-medium capitalize">{{ order.payment_method.replace('_', ' ') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Items -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items Card -->
                    <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">Order Items</h2>

                        <div v-if="order.order_items && order.order_items.length > 0" class="space-y-4">
                            <!-- Items Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-3 px-2 text-sm font-medium text-muted-foreground">
                                                Item</th>
                                            <th class="text-center py-3 px-2 text-sm font-medium text-muted-foreground">
                                                Quantity</th>
                                            <th class="text-right py-3 px-2 text-sm font-medium text-muted-foreground">
                                                Unit Price</th>
                                            <th class="text-right py-3 px-2 text-sm font-medium text-muted-foreground">
                                                Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in order.order_items" :key="item.id"
                                            class="border-b last:border-0">
                                            <td class="py-4 px-2">
                                                <div class="flex items-center gap-3">
                                                    <div class="p-2 rounded-lg bg-muted">
                                                        <Package :size="20" class="text-muted-foreground" />
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">{{ item.item?.item_name || 'Unknown Item'
                                                            }}</p>
                                                        <p v-if="item.item?.item_code"
                                                            class="text-sm text-muted-foreground font-mono">{{
                                                                item.item.item_code }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-2 text-center">
                                                <span class="font-medium">{{ item.quantity }}</span>
                                            </td>
                                            <td class="py-4 px-2 text-right">
                                                <span class="font-medium">{{ formatCurrency(item.unit_price) }}</span>
                                            </td>
                                            <td class="py-4 px-2 text-right">
                                                <span class="font-medium">{{ formatCurrency(item.unit_price *
                                                    item.quantity) }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Order Summary -->
                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Subtotal</span>
                                    <span class="font-medium">{{ formatCurrency(subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold pt-2 border-t">
                                    <span>Total</span>
                                    <span class="text-primary">{{ formatCurrency(order.total_amount) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                            <Package class="w-12 h-12 text-muted-foreground/50 mb-4" />
                            <h3 class="text-lg font-semibold">No items in this order</h3>
                            <p class="text-muted-foreground text-sm max-w-sm mt-2">
                                This order doesn't have any items yet.
                            </p>
                        </div>
                    </div>

                    <!-- Order Timeline Card -->
                    <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                        <h2 class="text-lg font-semibold mb-4">Order Timeline</h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-primary/10">
                                    <Calendar :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Created</p>
                                    <p class="font-medium">{{ formatDate(order.created_at) }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg bg-primary/10">
                                    <Calendar :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Last Updated</p>
                                    <p class="font-medium">{{ formatDate(order.updated_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
