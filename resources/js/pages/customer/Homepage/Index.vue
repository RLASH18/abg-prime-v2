<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type PaginationData } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Product } from '@/types/customer';
import { Search, ShoppingCart, Package, TrendingUp, Sparkles } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import Pagination from '@/components/Pagination.vue';
import { useFormatters } from '@/composables/useFormatters';
import { useFilters } from '@/composables/useFilters';
import homepageRoutes from '@/routes/customer/homepage';
import itemsRoutes from '@/routes/customer/homepage/items';

interface Props {
    items: PaginationData & {
        data: Product[];
    };
    filters: {
        search?: string;
        category?: string;
    };
}

const props = defineProps<Props>();
const { formatCurrency } = useFormatters();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Homepage',
        href: homepageRoutes.index().url,
    },
];

const { filters, updateFilter, resetFilters } = useFilters(
    homepageRoutes.index().url,
    {
        search: props.filters.search || '',
        category: props.filters.category || '',
    }
);

const categories = [
    { label: 'All Products', value: '' },
    { label: 'Hand Tools', value: 'Hand Tools' },
    { label: 'Power Tools', value: 'Power Tools' },
    { label: 'Construction Materials', value: 'Construction Materials' },
    { label: 'Locks and Security', value: 'Locks and Security' },
    { label: 'Plumbing', value: 'Plumbing' },
    { label: 'Electrical', value: 'Electrical' },
    { label: 'Paint and Finishes', value: 'Paint and Finishes' },
    { label: 'Chemicals', value: 'Chemicals' },
];

const getStockStatus = (item: Product) => {
    if (item.quantity <= 0) return { label: 'Out of Stock', class: 'bg-destructive/10 text-destructive border-destructive/20' };
    if (item.quantity <= item.restock_threshold) return { label: 'Low Stock', class: 'bg-warning/10 text-warning border-warning/20' };
    return { label: 'In Stock', class: 'bg-green-500/10 text-green-600 border-green-500/20' };
};

</script>

<template>

    <Head title="Homepage" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">

            <!-- Hero Section -->
            <div
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary/10 via-primary/5 to-background border border-primary/20 shadow-lg">
                <div class="absolute inset-0 bg-grid-white/5 [mask-image:linear-gradient(0deg,transparent,black)]">
                </div>
                <div class="relative px-6 py-12 md:px-12 md:py-16">
                    <div class="max-w-3xl">
                        <div class="flex items-center gap-2 mb-4">
                            <Sparkles class="h-5 w-5 text-primary animate-pulse" />
                            <span class="text-sm font-semibold text-primary uppercase tracking-wider">Premium
                                Quality</span>
                        </div>
                        <h1
                            class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 bg-gradient-to-r from-foreground to-foreground/70 bg-clip-text text-transparent">
                            Build Your Dreams
                        </h1>
                        <p class="text-lg md:text-xl text-muted-foreground mb-6 max-w-2xl">
                            Discover premium construction materials, tools, and supplies for your next project. Quality
                            guaranteed.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <div
                                class="flex items-center gap-2 px-4 py-2 rounded-lg bg-background/50 backdrop-blur-sm border border-border">
                                <Package class="h-5 w-5 text-primary" />
                                <span class="text-sm font-medium">{{ items.total }}+ Products</span>
                            </div>
                            <div
                                class="flex items-center gap-2 px-4 py-2 rounded-lg bg-background/50 backdrop-blur-sm border border-border">
                                <TrendingUp class="h-5 w-5 text-green-500" />
                                <span class="text-sm font-medium">Trusted Quality</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col gap-4">
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                    <Input :model-value="filters.search as string"
                        @update:model-value="(value) => updateFilter('search', value)"
                        placeholder="Search for tools, materials, or brands..."
                        class="pl-10 h-12 text-base border-2 focus-visible:ring-2" />
                </div>

                <!-- Category Pills -->
                <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin">
                    <Button v-for="cat in categories" :key="cat.value"
                        @click="updateFilter('category', cat.value, true)"
                        :variant="filters.category === cat.value ? 'default' : 'outline'" size="sm"
                        class="whitespace-nowrap transition-all duration-200 hover:scale-105">
                        {{ cat.label }}
                    </Button>
                </div>
            </div>

            <!-- Products Grid -->
            <div v-if="items.data.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                <Link
                    v-for="item in items.data"
                    :key="item.id"
                    :href="itemsRoutes.show(item.id)"
                    class="flex h-full"
                >

                    <Card
                        class="group flex flex-col w-full overflow-hidden border-2 hover:border-primary/50 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">

                        <CardContent class="flex-1 p-0">
                            <!-- Product Image -->
                            <div class="relative aspect-square overflow-hidden bg-muted">
                                <img v-if="item.image_path" :src="item.image_path" :alt="item.item_name"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                <div v-else
                                    class="w-full h-full flex items-center justify-center bg-gradient-to-br from-muted to-muted/50">
                                    <Package class="h-16 w-16 text-muted-foreground" />
                                </div>

                                <!-- Stock Badge -->
                                <div class="absolute top-3 right-3">
                                    <Badge :class="getStockStatus(item).class"
                                        class="border font-semibold shadow-lg backdrop-blur-sm">
                                        {{ getStockStatus(item).label }}
                                    </Badge>
                                </div>

                                <!-- Category Badge -->
                                <div class="absolute top-3 left-3">
                                    <Badge variant="secondary"
                                        class="bg-background/80 backdrop-blur-sm border shadow-lg">
                                        {{ item.category }}
                                    </Badge>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="p-4 space-y-3">
                                <div>
                                    <p class="text-xs text-muted-foreground font-medium uppercase tracking-wider">{{
                                        item.brand_name }}</p>
                                    <h3 class="font-bold text-lg line-clamp-2 mt-1 group-hover:text-primary transition-colors"
                                        :title="item.item_name">
                                        {{ item.item_name }}
                                    </h3>
                                </div>

                                <div class="flex items-baseline gap-2">
                                    <span class="text-2xl font-bold text-primary">{{ formatCurrency(item.unit_price)
                                        }}</span>
                                    <span class="text-xs text-muted-foreground">per unit</span>
                                </div>

                                <div class="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>Code: {{ item.item_code }}</span>
                                    <span class="font-semibold" :class="{
                                        'text-destructive': item.quantity <= 0,
                                        'text-warning': item.quantity > 0 && item.quantity <= item.restock_threshold,
                                        'text-green-600': item.quantity > item.restock_threshold
                                    }">
                                        {{ item.quantity }} available
                                    </span>
                                </div>
                            </div>
                        </CardContent>

                        <CardFooter class="p-4 pt-0">
                            <Button :disabled="item.quantity <= 0"
                                class="w-full group-hover:bg-primary group-hover:text-primary-foreground transition-all duration-200"
                                size="lg">
                                <ShoppingCart class="h-4 w-4 mr-2" />
                                {{ item.quantity <= 0 ? 'Out of Stock' : 'Add to Cart' }} </Button>
                        </CardFooter>
                    </Card>
                </Link>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-16 px-4">
                <div class="rounded-full bg-muted p-6 mb-4">
                    <Package class="h-12 w-12 text-muted-foreground" />
                </div>
                <h3 class="text-xl font-semibold mb-2">No products found</h3>
                <p class="text-muted-foreground text-center mb-6 max-w-md">
                    We couldn't find any products matching your search. Try adjusting your filters or search terms.
                </p>
                <Button @click="resetFilters" variant="outline">
                    Clear Filters
                </Button>
            </div>

            <!-- Pagination -->
            <Pagination :pagination="items" />
        </div>
    </AppLayout>
</template>
