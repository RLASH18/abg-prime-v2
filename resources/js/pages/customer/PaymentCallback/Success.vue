<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import homepageRoutes from '@/routes/customer/homepage';
import ordersRoutes from '@/routes/customer/orders';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { CheckCircle2, Package, ShoppingBag } from 'lucide-vue-next';

interface Props {
    session_id?: string;
    message: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Homepage',
        href: homepageRoutes.index().url,
    },
    {
        title: 'Payment Success',
        href: '#',
    },
];
</script>

<template>
    <Head title="Payment Successful" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 items-center justify-center p-4 md:p-6">
            <div class="w-full max-w-2xl space-y-8 text-center">
                <!-- Success Icon -->
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20"
                >
                    <CheckCircle2
                        class="h-16 w-16 text-green-600 dark:text-green-400"
                    />
                </div>

                <!-- Success Title -->
                <div>
                    <h1
                        class="text-4xl font-bold text-green-600 dark:text-green-400"
                    >
                        Payment Successful!
                    </h1>
                    <p class="mt-3 text-lg text-muted-foreground">
                        {{ message }}
                    </p>
                </div>

                <!-- Payment Details -->
                <div class="rounded-lg border bg-muted/50 p-6 text-left">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-primary/10 p-2">
                                <Package class="h-5 w-5 text-primary" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Order Status</p>
                                <p class="text-xs text-muted-foreground">
                                    Your order has been confirmed and is being
                                    processed
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="session_id"
                            class="rounded-md bg-background p-3"
                        >
                            <p
                                class="text-xs font-medium text-muted-foreground"
                            >
                                Transaction ID
                            </p>
                            <p class="mt-1 font-mono text-sm break-all">
                                {{ session_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- What's Next -->
                <div class="space-y-4 text-left">
                    <h3 class="font-semibold">What happens next?</h3>
                    <ul class="space-y-3 text-sm text-muted-foreground">
                        <li class="flex items-start gap-2">
                            <CheckCircle2
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600"
                            />
                            <span
                                >You'll receive an order confirmation email
                                shortly</span
                            >
                        </li>
                        <li class="flex items-start gap-2">
                            <CheckCircle2
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600"
                            />
                            <span
                                >Your order will be prepared for
                                delivery/pickup</span
                            >
                        </li>
                        <li class="flex items-start gap-2">
                            <CheckCircle2
                                class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600"
                            />
                            <span
                                >Track your order status in your orders
                                page</span
                            >
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3 pt-4 sm:flex-row">
                    <LinkButton
                        :href="ordersRoutes.index().url"
                        label="View My Orders"
                        size="lg"
                        class="w-full sm:flex-1"
                    />
                    <LinkButton
                        :href="homepageRoutes.index().url"
                        mode="back"
                        size="lg"
                        class="w-full sm:flex-1"
                    >
                        <ShoppingBag class="mr-2 h-5 w-5" />
                        Continue Shopping
                    </LinkButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
