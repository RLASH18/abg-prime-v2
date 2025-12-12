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
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BarChart3,
    Boxes,
    ClipboardList,
    LayoutGrid,
    ReceiptText,
    Truck
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import items from '@/routes/admin/items';
import suppliers from '@/routes/admin/suppliers';
import damagedItemsRoutes from '@/routes/admin/damaged-items';

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
                href: suppliers.index()
            },
            {
                title: 'Items',
                href: items.index()
            },
            {
                title: 'Damaged Items',
                href: damagedItemsRoutes.index(),
            },
        ]
    },
    {
        title: 'Orders',
        icon: ClipboardList,
        items: [
            {
                title: 'Online',
                href: dashboard()
            },
            {
                title: 'Walk-in',
                href: dashboard()
            }
        ]
    },
    {
        title: 'Billings',
        href: dashboard(),
        icon: ReceiptText,
    },
    {
        title: 'Delivery',
        href: dashboard(),
        icon: Truck,
    },
    {
        title: 'Reports',
        href: dashboard(),
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
