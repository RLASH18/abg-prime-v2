<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import cartsRoutes from '@/routes/customer/carts';
import homepageRoutes from '@/routes/customer/homepage';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import {
    AlertCircle,
    ArrowLeft,
    RefreshCw,
    ShoppingBag,
} from 'lucide-vue-next';

interface Props {
    message: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Homepage',
        href: homepageRoutes.index().url,
    },
    {
        title: 'Payment Failed',
        href: '#',
    },
];

const retryPayment = () => {
    router.visit(cartsRoutes.index().url);
};
</script>

<template>
    <Head title="Payment Failed" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 items-center justify-center p-4 md:p-6">
            <div class="w-full max-w-2xl space-y-8 text-center">
                <!-- Error Icon -->
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-destructive/10"
                >
                    <AlertCircle class="h-16 w-16 text-destructive" />
                </div>

                <!-- Error Title -->
                <div>
                    <h1 class="text-4xl font-bold text-destructive">
                        Payment Failed
                    </h1>
                    <p class="mt-3 text-lg text-muted-foreground">
                        {{ message }}
                    </p>
                </div>

                <!-- Error Details -->
                <div
                    class="rounded-lg border border-destructive/20 bg-destructive/5 p-6 text-left"
                >
                    <div class="space-y-3">
                        <h3 class="font-semibold text-destructive">
                            What happened?
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            Your payment could not be processed. This might be
                            due to:
                        </p>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li class="flex items-start gap-2">
                                <span
                                    class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-destructive"
                                ></span>
                                <span>Payment was cancelled</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span
                                    class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-destructive"
                                ></span>
                                <span
                                    >Insufficient funds or payment limit
                                    reached</span
                                >
                            </li>
                            <li class="flex items-start gap-2">
                                <span
                                    class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-destructive"
                                ></span>
                                <span>Network or connection issues</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span
                                    class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-destructive"
                                ></span>
                                <span>Payment method declined</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- What to do next -->
                <div class="space-y-4 text-left">
                    <h3 class="font-semibold">What can you do?</h3>
                    <div class="space-y-3 text-sm text-muted-foreground">
                        <div
                            class="flex items-start gap-3 rounded-lg border bg-muted/50 p-4"
                        >
                            <RefreshCw
                                class="mt-0.5 h-5 w-5 flex-shrink-0 text-primary"
                            />
                            <div>
                                <p class="font-medium text-foreground">
                                    Try Again
                                </p>
                                <p class="mt-1">
                                    Return to your cart and retry the payment
                                    with the same or different payment method
                                </p>
                            </div>
                        </div>
                        <div
                            class="flex items-start gap-3 rounded-lg border bg-muted/50 p-4"
                        >
                            <ShoppingBag
                                class="mt-0.5 h-5 w-5 flex-shrink-0 text-primary"
                            />
                            <div>
                                <p class="font-medium text-foreground">
                                    Continue Shopping
                                </p>
                                <p class="mt-1">
                                    Browse more products and add them to your
                                    cart
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Info -->
                <div class="rounded-lg border bg-muted/30 p-4 text-left">
                    <p class="text-sm text-muted-foreground">
                        <span class="font-medium text-foreground"
                            >Need help?</span
                        >
                        If you continue to experience issues, please contact our
                        support team.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3 pt-4 sm:flex-row">
                    <Button
                        @click="retryPayment"
                        size="lg"
                        class="w-full sm:flex-1"
                    >
                        <RefreshCw class="mr-2 h-5 w-5" />
                        Try Again
                    </Button>
                    <LinkButton
                        :href="homepageRoutes.index().url"
                        mode="back"
                        size="lg"
                        class="w-full sm:flex-1"
                    >
                        <ArrowLeft class="mr-2 h-5 w-5" />
                        Back to Shop
                    </LinkButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
