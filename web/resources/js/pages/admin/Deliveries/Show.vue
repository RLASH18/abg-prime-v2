<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import deliveriesRoutes from '@/routes/admin/deliveries';
import { type BreadcrumbItem } from '@/types';
import type { Delivery } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import {
    AlertCircle,
    Calendar,
    CheckCircle2,
    ClipboardList,
    MapPin,
    Package,
    Truck,
    User,
} from 'lucide-vue-next';

interface Props {
    delivery: Delivery;
}

const props = defineProps<Props>();
const { formatCurrency, formatDate } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Deliveries',
        href: deliveriesRoutes.index().url,
    },
    {
        title: `Delivery #${props.delivery.id.toString().padStart(4, '0')}`,
        href: deliveriesRoutes.show(props.delivery.id).url,
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'scheduled':
            return 'default' as const;
        case 'rescheduled':
            return 'secondary' as const;
        case 'in_transit':
            return 'default' as const;
        case 'delivered':
            return 'default' as const;
        case 'failed':
            return 'destructive' as const;
        default:
            return 'secondary' as const;
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'scheduled':
            return Calendar;
        case 'rescheduled':
            return Calendar;
        case 'in_transit':
            return Truck;
        case 'delivered':
            return CheckCircle2;
        case 'failed':
            return AlertCircle;
        default:
            return ClipboardList;
    }
};
</script>

<template>
    <Head
        :title="`Admin - Delivery #${delivery.id.toString().padStart(4, '0')}`"
    />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
        >
            <!-- Header -->
            <div class="mb-4 flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        Delivery #{{ delivery.id.toString().padStart(4, '0') }}
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Scheduled for {{ formatDate(delivery.scheduled_date) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <LinkButton
                        :href="deliveriesRoutes.index().url"
                        mode="back"
                        label="Back to list"
                    />
                </div>
            </div>

            <!-- Status Banner -->
            <div
                class="rounded-lg border border-border bg-gradient-to-r from-primary/5 to-primary/10 p-6"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-background p-3">
                            <component
                                :is="getStatusIcon(delivery.status)"
                                :size="24"
                                class="text-primary"
                            />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Current Status
                            </p>
                            <Badge
                                :variant="getStatusColor(delivery.status)"
                                class="mt-1 rounded-full capitalize"
                            >
                                {{ delivery.status.replace('_', ' ') }}
                            </Badge>
                        </div>
                    </div>
                    <div
                        v-if="delivery.actual_delivery_date"
                        class="text-right"
                    >
                        <p class="text-sm text-muted-foreground">
                            Delivered On
                        </p>
                        <p class="text-lg font-semibold text-green-700">
                            {{ formatDate(delivery.actual_delivery_date) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Order Information Card -->
                <div
                    class="rounded-lg border border-border bg-card p-6 shadow-sm"
                >
                    <h2
                        class="mb-4 flex items-center gap-2 text-lg font-semibold"
                    >
                        <Package :size="20" class="text-primary" />
                        Order Information
                    </h2>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between border-b pb-3"
                        >
                            <span class="text-sm text-muted-foreground"
                                >Order ID</span
                            >
                            <span class="font-semibold"
                                >#{{
                                    delivery.order_id
                                        .toString()
                                        .padStart(4, '0')
                                }}</span
                            >
                        </div>
                        <div
                            class="flex items-center justify-between border-b pb-3"
                        >
                            <span class="text-sm text-muted-foreground"
                                >Customer</span
                            >
                            <span class="font-medium">{{
                                delivery.order?.user?.name || 'Unknown'
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between border-b pb-3"
                        >
                            <span class="text-sm text-muted-foreground"
                                >Order Status</span
                            >
                            <Badge variant="outline" class="capitalize">
                                {{ delivery.order?.status || 'N/A' }}
                            </Badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted-foreground"
                                >Total Amount</span
                            >
                            <span class="text-lg font-bold text-primary">{{
                                formatCurrency(
                                    delivery.order?.total_amount || 0,
                                )
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Details Card -->
                <div
                    class="rounded-lg border border-border bg-card p-6 shadow-sm"
                >
                    <h2
                        class="mb-4 flex items-center gap-2 text-lg font-semibold"
                    >
                        <Truck :size="20" class="text-primary" />
                        Delivery Details
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 rounded-lg bg-primary/10 p-2">
                                <User :size="18" class="text-primary" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">
                                    Driver
                                </p>
                                <p class="font-medium">
                                    {{ delivery.driver_name || 'Not Assigned' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 rounded-lg bg-primary/10 p-2">
                                <Calendar :size="18" class="text-primary" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">
                                    Scheduled Date
                                </p>
                                <p class="font-medium">
                                    {{ formatDate(delivery.scheduled_date) }}
                                </p>
                            </div>
                        </div>
                        <div
                            v-if="delivery.order?.delivery_address"
                            class="flex items-start gap-3"
                        >
                            <div class="mt-0.5 rounded-lg bg-primary/10 p-2">
                                <MapPin :size="18" class="text-primary" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">
                                    Delivery Address
                                </p>
                                <p class="font-medium">
                                    {{ delivery.order.delivery_address }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks Card (Full Width) -->
                <div
                    v-if="delivery.remarks"
                    class="rounded-lg border border-border bg-card p-6 shadow-sm md:col-span-2"
                >
                    <h2
                        class="mb-4 flex items-center gap-2 text-lg font-semibold"
                    >
                        <ClipboardList :size="20" class="text-primary" />
                        Remarks
                    </h2>
                    <div class="rounded-lg bg-muted/50 p-4">
                        <p class="text-sm leading-relaxed">
                            {{ delivery.remarks }}
                        </p>
                    </div>
                </div>

                <!-- Delivery Timeline Card (Full Width) -->
                <div
                    class="rounded-lg border border-border bg-card p-6 shadow-sm md:col-span-2"
                >
                    <h2 class="mb-4 text-lg font-semibold">
                        Delivery Timeline
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="flex flex-col items-center">
                                <div
                                    class="h-3 w-3 rounded-full bg-primary"
                                ></div>
                                <div class="h-12 w-0.5 bg-border"></div>
                            </div>
                            <div class="flex-1 pb-8">
                                <p class="font-medium">Delivery Created</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ formatDate(delivery.created_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex flex-col items-center">
                                <div
                                    class="h-3 w-3 rounded-full"
                                    :class="
                                        delivery.status !== 'scheduled'
                                            ? 'bg-primary'
                                            : 'bg-border'
                                    "
                                ></div>
                                <div
                                    v-if="delivery.actual_delivery_date"
                                    class="h-12 w-0.5 bg-border"
                                ></div>
                            </div>
                            <div
                                class="flex-1"
                                :class="
                                    delivery.actual_delivery_date ? 'pb-8' : ''
                                "
                            >
                                <p class="font-medium">Last Updated</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ formatDate(delivery.updated_at) }}
                                </p>
                            </div>
                        </div>
                        <div
                            v-if="delivery.actual_delivery_date"
                            class="flex items-center gap-4"
                        >
                            <div class="flex flex-col items-center">
                                <div
                                    class="h-3 w-3 rounded-full bg-green-600"
                                ></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-green-700">
                                    Delivered
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{
                                        formatDate(
                                            delivery.actual_delivery_date,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
