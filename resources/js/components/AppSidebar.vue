<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes/admin';
import billingsRoutes from '@/routes/admin/billings';
import damagedItemsRoutes from '@/routes/admin/damaged-items';
import deliveriesRoutes from '@/routes/admin/deliveries';
import itemsRoutes from '@/routes/admin/items';
import ordersRoutes from '@/routes/admin/orders';
import reportsRoutes from '@/routes/admin/reports';
import suppliersRoutes from '@/routes/admin/suppliers';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BarChart3,
    Boxes,
    ClipboardList,
    LayoutGrid,
    ReceiptText,
    Truck,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Inventory',
        icon: Boxes,
        items: [
            {
                title: 'Suppliers',
                href: suppliersRoutes.index(),
            },
            {
                title: 'Items',
                href: itemsRoutes.index(),
            },
            {
                title: 'Damaged Items',
                href: damagedItemsRoutes.index(),
            },
        ],
    },
    {
        title: 'Orders',
        href: ordersRoutes.index(),
        icon: ClipboardList,
    },
    {
        title: 'Billings',
        href: billingsRoutes.index().url,
        icon: ReceiptText,
    },
    {
        title: 'Delivery',
        href: deliveriesRoutes.index().url,
        icon: Truck,
    },
    {
        title: 'Reports',
        href: reportsRoutes.index().url,
        icon: BarChart3,
    },
];

const footerNavItems: NavItem[] = [
    //
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
