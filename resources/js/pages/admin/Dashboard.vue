<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import { type BreadcrumbItem } from '@/types';
import { type DashboardStats } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import { Package, PhilippinePeso, ShoppingCart, Users } from 'lucide-vue-next';

const props = defineProps<DashboardStats>();
const { formatCurrency } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Dashboard</h1>
                    <p class="text-sm text-muted-foreground">
                        Welcome back
                        <span class="font-bold text-[#815331]">@Admin</span>!
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <!-- Total Orders Card -->
                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Total Orders
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ countOrders }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3">
                            <ShoppingCart class="h-6 w-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Revenue Card -->
                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Total Revenue
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ formatCurrency(getTotalRevenue) }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3">
                            <PhilippinePeso class="h-6 w-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Items Card -->
                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Total Items
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ countItems }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-3">
                            <Package class="h-6 w-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Customers Card -->
                <div class="rounded-lg border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Total Customers
                            </p>
                            <p class="mt-1 text-2xl font-bold">
                                {{ countCustomers }}
                            </p>
                        </div>
                        <div class="rounded-lg bg-orange-50 p-3">
                            <Users class="h-6 w-6 text-orange-600" />
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
