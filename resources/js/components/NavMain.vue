<script setup lang="ts">
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible'
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar'
import { toUrl, urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

// Check if any sub-item is active
const isGroupActive = (item: NavItem): boolean => {
    if (!item.items || item.items.length === 0) return false;

    const currentUrlPath = page.url.split('?')[0];

    return item.items.some(subItem => {
        const subItemPath = toUrl(subItem.href!).split('?')[0];
        // Check if current URL starts with the sub-item URL
        return currentUrlPath.startsWith(subItemPath);
    });
};

// Check if a specific sub-item is active (including child routes)
const isSubItemActive = (href: NonNullable<NavItem['href']>): boolean => {
    const currentUrlPath = page.url.split('?')[0];
    const subItemPath = toUrl(href).split('?')[0];
    return currentUrlPath.startsWith(subItemPath);
};
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">

                <Collapsible v-if="item.items && item.items.length > 0" as-child :default-open="isGroupActive(item)"
                    class="group/collapsible">
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="item.title">
                                <component :is="item.icon" v-if="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronRight
                                    class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>

                        <CollapsibleContent>
                            <SidebarMenuSub class="mr-0 pr-0">
                                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                    <SidebarMenuSubButton as-child :is-active="isSubItemActive(subItem.href!)"
                                        class="w-full">
                                        <Link :href="subItem.href!">
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <SidebarMenuItem v-else>
                    <SidebarMenuButton as-child :is-active="item.href ? urlIsActive(item.href, page.url) : false"
                        :tooltip="item.title">
                        <Link :href="item.href || '#'">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>