<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/admin';
import { type BreadcrumbItem } from '@/types';
import { type DashboardStats } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { useFormatters } from '@/composables/useFormatters';
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
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Dashboard</h1>
                    <p class="text-sm text-muted-foreground">Welcome back <span
                            class="text-[#815331] font-bold">@Admin</span>!</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Total Orders Card -->
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Orders</p>
                            <p class="text-2xl font-bold mt-1">{{ countOrders }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <ShoppingCart class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Revenue Card -->
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Revenue</p>
                            <p class="text-2xl font-bold mt-1">{{ formatCurrency(getTotalRevenue) }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <PhilippinePeso class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Items Card -->
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Items</p>
                            <p class="text-2xl font-bold mt-1">{{ countItems }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <Package class="w-6 h-6 text-purple-600" />
                        </div>
                    </div>
                </div>

                <!-- Total Customers Card -->
                <div class="bg-white border rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Customers</p>
                            <p class="text-2xl font-bold mt-1">{{ countCustomers }}</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <Users class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
