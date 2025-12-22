<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import reportsRoutes from '@/routes/admin/reports';
import { BarChart3, Package, ShoppingCart, FileText, Truck } from 'lucide-vue-next';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Sales',
        href: reportsRoutes.sales(),
        icon: BarChart3,
    },
    {
        title: 'Inventory',
        href: reportsRoutes.inventory(),
        icon: Package,
    },
    {
        title: 'Orders',
        href: reportsRoutes.orders(),
        icon: ShoppingCart,
    },
    {
        title: 'Billing',
        href: reportsRoutes.billing(),
        icon: FileText,
    },
    {
        title: 'Delivery',
        href: reportsRoutes.delivery(),
        icon: Truck,
    },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Reports" description="View comprehensive business analytics and insights" />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button v-for="item in sidebarNavItems" :key="toUrl(item.href!)" variant="ghost" :class="[
                        'w-full justify-start',
                        { 'bg-muted': urlIsActive(item.href!, currentPath) },
                    ]" as-child>
                        <Link :href="item.href">
                            <component v-if="item.icon" :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1">
                <section class="space-y-6">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
