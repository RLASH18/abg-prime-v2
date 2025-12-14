<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import deliveriesRoutes from '@/routes/admin/deliveries';
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import {
    Package,
    User,
    Truck,
    MapPin,
    Calendar,
    ClipboardList,
    CheckCircle2,
    AlertCircle,
} from 'lucide-vue-next';
import type { Delivery } from '@/types/admin';
import { useFormatters } from '@/composables/useFormatters';

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
        title: `Delivery #${props.delivery.id}`,
        href: deliveriesRoutes.show(props.delivery.id).url,
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'scheduled': return 'default' as const;
        case 'rescheduled': return 'secondary' as const;
        case 'in_transit': return 'default' as const;
        case 'delivered': return 'default' as const;
        case 'failed': return 'destructive' as const;
        default: return 'secondary' as const;
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'scheduled': return Calendar;
        case 'rescheduled': return Calendar;
        case 'in_transit': return Truck;
        case 'delivered': return CheckCircle2;
        case 'failed': return AlertCircle;
        default: return ClipboardList;
    }
};
</script>

<template>

    <Head :title="`Delivery #${delivery.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Delivery #{{ delivery.id }}</h1>
                    <p class="text-sm text-muted-foreground mt-1">Scheduled for {{ formatDate(delivery.scheduled_date)
                    }}</p>
                </div>
                <div class="flex gap-2">
                    <LinkButton :href="deliveriesRoutes.index().url" mode="back" label="Back to list" />
                </div>
            </div>

            <!-- Status Banner -->
            <div class="rounded-lg border border-border bg-gradient-to-r from-primary/5 to-primary/10 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-full bg-background">
                            <component :is="getStatusIcon(delivery.status)" :size="24" class="text-primary" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Current Status</p>
                            <Badge :variant="getStatusColor(delivery.status)" class="rounded-full capitalize mt-1">
                                {{ delivery.status.replace('_', ' ') }}
                            </Badge>
                        </div>
                    </div>
                    <div v-if="delivery.actual_delivery_date" class="text-right">
                        <p class="text-sm text-muted-foreground">Delivered On</p>
                        <p class="text-lg font-semibold text-green-700">{{ formatDate(delivery.actual_delivery_date) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order Information Card -->
                <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <Package :size="20" class="text-primary" />
                        Order Information
                    </h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-sm text-muted-foreground">Order ID</span>
                            <span class="font-semibold">#{{ delivery.order_id }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-sm text-muted-foreground">Customer</span>
                            <span class="font-medium">{{ delivery.order?.user?.name || 'Unknown' }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-sm text-muted-foreground">Order Status</span>
                            <Badge variant="outline" class="capitalize">
                                {{ delivery.order?.status || 'N/A' }}
                            </Badge>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted-foreground">Total Amount</span>
                            <span class="text-lg font-bold text-primary">{{
                                formatCurrency(delivery.order?.total_amount || 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Details Card -->
                <div class="rounded-lg border border-border bg-card shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <Truck :size="20" class="text-primary" />
                        Delivery Details
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-primary/10 mt-0.5">
                                <User :size="18" class="text-primary" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">Driver</p>
                                <p class="font-medium">{{ delivery.driver_name || 'Not Assigned' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-primary/10 mt-0.5">
                                <Calendar :size="18" class="text-primary" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">Scheduled Date</p>
                                <p class="font-medium">{{ formatDate(delivery.scheduled_date) }}</p>
                            </div>
                        </div>
                        <div v-if="delivery.order?.delivery_address" class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-primary/10 mt-0.5">
                                <MapPin :size="18" class="text-primary" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-muted-foreground">Delivery Address</p>
                                <p class="font-medium">{{ delivery.order.delivery_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks Card (Full Width) -->
                <div v-if="delivery.remarks"
                    class="md:col-span-2 rounded-lg border border-border bg-card shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <ClipboardList :size="20" class="text-primary" />
                        Remarks
                    </h2>
                    <div class="bg-muted/50 rounded-lg p-4">
                        <p class="text-sm leading-relaxed">{{ delivery.remarks }}</p>
                    </div>
                </div>

                <!-- Delivery Timeline Card (Full Width) -->
                <div class="md:col-span-2 rounded-lg border border-border bg-card shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Delivery Timeline</h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-primary"></div>
                                <div class="w-0.5 h-12 bg-border"></div>
                            </div>
                            <div class="flex-1 pb-8">
                                <p class="font-medium">Delivery Created</p>
                                <p class="text-sm text-muted-foreground">{{ formatDate(delivery.created_at) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full"
                                    :class="delivery.status !== 'scheduled' ? 'bg-primary' : 'bg-border'"></div>
                                <div v-if="delivery.actual_delivery_date" class="w-0.5 h-12 bg-border"></div>
                            </div>
                            <div class="flex-1" :class="delivery.actual_delivery_date ? 'pb-8' : ''">
                                <p class="font-medium">Last Updated</p>
                                <p class="text-sm text-muted-foreground">{{ formatDate(delivery.updated_at) }}</p>
                            </div>
                        </div>
                        <div v-if="delivery.actual_delivery_date" class="flex items-center gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-green-600"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-green-700">Delivered</p>
                                <p class="text-sm text-muted-foreground">{{ formatDate(delivery.actual_delivery_date) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
