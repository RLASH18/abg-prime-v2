<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import ordersRoutes from '@/routes/admin/orders';
import { type BreadcrumbItem } from '@/types';
import type { Order } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import {
    Calendar,
    CreditCard,
    Hash,
    MapPin,
    Package,
    Truck,
    User,
} from 'lucide-vue-next';
import { computed } from 'vue';

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
        case 'pending':
            return 'secondary' as const;
        case 'confirmed':
            return 'default' as const;
        case 'assembled':
            return 'default' as const;
        case 'shipped':
            return 'default' as const;
        case 'delivered':
            return 'default' as const;
        case 'paid':
            return 'default' as const;
        case 'cancelled':
            return 'destructive' as const;
        default:
            return 'secondary' as const;
    }
};

const subtotal = computed(() => {
    return (
        props.order.order_items?.reduce((sum, item) => {
            return sum + item.unit_price * item.quantity;
        }, 0) || 0
    );
});
</script>

<template>
    <Head :title="`Admin - Order #${order.id.toString().padStart(4, '0')}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
        >
            <!-- Header -->
            <div class="mb-4 flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Order #{{ order.id.toString().padStart(4, '0') }}
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Placed on {{ formatDate(order.created_at) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <LinkButton
                        :href="ordersRoutes.index().url"
                        mode="back"
                        label="Back to list"
                    />
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column - Order Summary -->
                <div class="space-y-6 lg:col-span-1">
                    <!-- Status Card -->
                    <div
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-semibold">Order Status</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="mb-2 text-sm text-muted-foreground">
                                    Current Status
                                </p>
                                <Badge
                                    :variant="getStatusColor(order.status)"
                                    class="rounded-full capitalize"
                                >
                                    {{ order.status }}
                                </Badge>
                            </div>
                            <div>
                                <p class="mb-1 text-sm text-muted-foreground">
                                    Total Amount
                                </p>
                                <p class="text-2xl font-bold text-primary">
                                    {{ formatCurrency(order.total_amount) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info Card -->
                    <div
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-semibold">
                            Customer Information
                        </h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <User :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Customer Name
                                    </p>
                                    <p class="font-medium">
                                        {{ order.user?.name || 'Unknown User' }}
                                    </p>
                                </div>
                            </div>
                            <div
                                v-if="order.user?.email"
                                class="flex items-start gap-3"
                            >
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Hash :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Email
                                    </p>
                                    <p class="font-medium">
                                        {{ order.user.email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Info Card -->
                    <div
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-semibold">
                            Delivery Information
                        </h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Truck :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Delivery Method
                                    </p>
                                    <p class="font-medium capitalize">
                                        {{
                                            order.delivery_method.replace(
                                                '_',
                                                ' ',
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div
                                v-if="order.delivery_address"
                                class="flex items-start gap-3"
                            >
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <MapPin :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Delivery Address
                                    </p>
                                    <p class="font-medium">
                                        {{ order.delivery_address }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <CreditCard
                                        :size="20"
                                        class="text-primary"
                                    />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Payment Method
                                    </p>
                                    <p class="font-medium capitalize">
                                        {{
                                            order.payment_method.replace(
                                                '_',
                                                ' ',
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Items -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Order Items Card -->
                    <div
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-semibold">Order Items</h2>

                        <div
                            v-if="
                                order.order_items &&
                                order.order_items.length > 0
                            "
                            class="space-y-4"
                        >
                            <!-- Items Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th
                                                class="px-2 py-3 text-left text-sm font-medium text-muted-foreground"
                                            >
                                                Item
                                            </th>
                                            <th
                                                class="px-2 py-3 text-center text-sm font-medium text-muted-foreground"
                                            >
                                                Quantity
                                            </th>
                                            <th
                                                class="px-2 py-3 text-right text-sm font-medium text-muted-foreground"
                                            >
                                                Unit Price
                                            </th>
                                            <th
                                                class="px-2 py-3 text-right text-sm font-medium text-muted-foreground"
                                            >
                                                Subtotal
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="item in order.order_items"
                                            :key="item.id"
                                            class="border-b last:border-0"
                                        >
                                            <td class="px-2 py-4">
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <div
                                                        class="rounded-lg bg-muted p-2"
                                                    >
                                                        <Package
                                                            :size="20"
                                                            class="text-muted-foreground"
                                                        />
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">
                                                            {{
                                                                item.item
                                                                    ?.item_name ||
                                                                'Unknown Item'
                                                            }}
                                                        </p>
                                                        <p
                                                            v-if="
                                                                item.item
                                                                    ?.item_code
                                                            "
                                                            class="font-mono text-sm text-muted-foreground"
                                                        >
                                                            {{
                                                                item.item
                                                                    .item_code
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-2 py-4 text-center">
                                                <span class="font-medium">{{
                                                    item.quantity
                                                }}</span>
                                            </td>
                                            <td class="px-2 py-4 text-right">
                                                <span class="font-medium">{{
                                                    formatCurrency(
                                                        item.unit_price,
                                                    )
                                                }}</span>
                                            </td>
                                            <td class="px-2 py-4 text-right">
                                                <span class="font-medium">{{
                                                    formatCurrency(
                                                        item.unit_price *
                                                            item.quantity,
                                                    )
                                                }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Order Summary -->
                            <div class="space-y-2 border-t pt-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground"
                                        >Subtotal</span
                                    >
                                    <span class="font-medium">{{
                                        formatCurrency(subtotal)
                                    }}</span>
                                </div>
                                <div
                                    class="flex justify-between border-t pt-2 text-lg font-bold"
                                >
                                    <span>Total</span>
                                    <span class="text-primary">{{
                                        formatCurrency(order.total_amount)
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-else
                            class="flex flex-col items-center justify-center py-12 text-center"
                        >
                            <Package
                                class="mb-4 h-12 w-12 text-muted-foreground/50"
                            />
                            <h3 class="text-lg font-semibold">
                                No items in this order
                            </h3>
                            <p
                                class="mt-2 max-w-sm text-sm text-muted-foreground"
                            >
                                This order doesn't have any items yet.
                            </p>
                        </div>
                    </div>

                    <!-- Order Timeline Card -->
                    <div
                        class="rounded-lg border border-border bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-semibold">
                            Order Timeline
                        </h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Calendar :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Created
                                    </p>
                                    <p class="font-medium">
                                        {{ formatDate(order.created_at) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-primary/10 p-2">
                                    <Calendar :size="20" class="text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">
                                        Last Updated
                                    </p>
                                    <p class="font-medium">
                                        {{ formatDate(order.updated_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
