<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useFilters } from '@/composables/useFilters';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import cartsRoutes from '@/routes/customer/carts';
import homepageRoutes from '@/routes/customer/homepage';
import productsRoutes from '@/routes/customer/homepage/products';
import { type BreadcrumbItem, type PaginationData } from '@/types';
import { Product } from '@/types/customer';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Package,
    Percent,
    Search,
    ShoppingCart,
    Sparkles,
    TrendingUp,
} from 'lucide-vue-next';

interface Props {
    products: PaginationData & {
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
    },
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
    if (item.quantity <= 0)
        return {
            label: 'Out of Stock',
            class: 'bg-destructive/10 text-destructive border-destructive/20',
        };
    if (item.quantity <= item.restock_threshold)
        return {
            label: 'Low Stock',
            class: 'bg-warning/10 text-warning border-warning/20',
        };
    return {
        label: 'In Stock',
        class: 'bg-green-500/10 text-green-600 border-green-500/20',
    };
};

const addToCart = (product: Product, event: Event) => {
    event.preventDefault();
    event.stopPropagation();

    router.post(
        cartsRoutes.store().url,
        {
            item_id: product.id,
            quantity: 1,
            damaged_item_id: product.damaged_item_id || null,
        },
        {
            preserveScroll: true,
        },
    );
};

const getProductUrl = (product: Product) => {
    if (product.is_damaged === true && product.damaged_item_id != null) {
        return productsRoutes.show.damaged({
            product: product.id,
            damagedItemId: product.damaged_item_id,
        }).url;
    }
    return productsRoutes.show(product.id).url;
};
</script>

<template>
    <Head title="Homepage" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6"
        >
            <!-- Hero Section -->
            <div
                class="relative overflow-hidden rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/10 via-primary/5 to-background shadow-lg"
            >
                <div
                    class="bg-grid-white/5 absolute inset-0 [mask-image:linear-gradient(0deg,transparent,black)]"
                ></div>
                <div class="relative px-6 py-12 md:px-12 md:py-16">
                    <div class="max-w-3xl">
                        <div class="mb-4 flex items-center gap-2">
                            <Sparkles
                                class="h-5 w-5 animate-pulse text-primary"
                            />
                            <span
                                class="text-sm font-semibold tracking-wider text-primary uppercase"
                                >Premium Quality</span
                            >
                        </div>
                        <h1
                            class="mb-4 bg-gradient-to-r from-foreground to-foreground/70 bg-clip-text text-4xl font-bold text-transparent md:text-5xl lg:text-6xl"
                        >
                            Build Your Dreams
                        </h1>
                        <p
                            class="mb-6 max-w-2xl text-lg text-muted-foreground md:text-xl"
                        >
                            Discover premium construction materials, tools, and
                            supplies for your next project. Quality guaranteed.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <div
                                class="flex items-center gap-2 rounded-lg border border-border bg-background/50 px-4 py-2 backdrop-blur-sm"
                            >
                                <Package class="h-5 w-5 text-primary" />
                                <span class="text-sm font-medium"
                                    >{{ products.total }}+ Products</span
                                >
                            </div>
                            <div
                                class="flex items-center gap-2 rounded-lg border border-border bg-background/50 px-4 py-2 backdrop-blur-sm"
                            >
                                <TrendingUp class="h-5 w-5 text-green-500" />
                                <span class="text-sm font-medium"
                                    >Trusted Quality</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col gap-4">
                <div class="relative">
                    <Search
                        class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        :model-value="filters.search as string"
                        @update:model-value="
                            (value) => updateFilter('search', value)
                        "
                        placeholder="Search for tools, materials, or brands..."
                        class="h-12 border-2 pl-10 text-base focus-visible:ring-2"
                    />
                </div>

                <!-- Category Pills -->
                <div class="scrollbar-thin flex gap-2 overflow-x-auto pb-2">
                    <Button
                        v-for="cat in categories"
                        :key="cat.value"
                        @click="updateFilter('category', cat.value, true)"
                        :variant="
                            filters.category === cat.value
                                ? 'default'
                                : 'outline'
                        "
                        size="sm"
                        class="whitespace-nowrap transition-all duration-200 hover:scale-105"
                    >
                        {{ cat.label }}
                    </Button>
                </div>
            </div>

            <!-- Products Grid -->
            <div
                v-if="products.data.length > 0"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <Link
                    v-for="product in products.data"
                    :key="
                        product.is_damaged
                            ? `damaged-${product.damaged_item_id}`
                            : product.id
                    "
                    :href="getProductUrl(product)"
                    class="flex h-full"
                >
                    <Card
                        class="group flex w-full flex-col overflow-hidden border-2 transition-all duration-300 hover:-translate-y-1 hover:border-primary/50 hover:shadow-xl"
                    >
                        <CardContent class="flex-1 p-0">
                            <!-- Product Image -->
                            <div
                                class="relative aspect-square overflow-hidden p-4"
                            >
                                <img
                                    v-if="product.item_image_1"
                                    :src="`/storage/${product.item_image_1}`"
                                    :alt="product.item_name"
                                    class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-110"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center rounded-xl bg-gradient-to-br from-muted to-muted/50"
                                >
                                    <Package
                                        class="h-16 w-16 text-muted-foreground"
                                    />
                                </div>

                                <!-- Stock Badge -->
                                <div class="absolute top-3 right-3">
                                    <Badge
                                        :class="getStockStatus(product).class"
                                        class="border font-semibold shadow-lg backdrop-blur-sm"
                                    >
                                        {{ getStockStatus(product).label }}
                                    </Badge>
                                </div>

                                <!-- Category Badge -->
                                <div
                                    class="absolute top-3 left-3 flex flex-col gap-1"
                                >
                                    <Badge
                                        variant="secondary"
                                        class="border bg-background/80 shadow-lg backdrop-blur-sm"
                                    >
                                        {{ product.category }}
                                    </Badge>
                                    <!-- Discount Badge for Damaged Items -->
                                    <Badge
                                        v-if="
                                            product.is_damaged &&
                                            product.discount_percentage
                                        "
                                        class="border bg-red-500 text-white shadow-lg"
                                    >
                                        <Percent class="mr-1 h-3 w-3" />
                                        {{ product.discount_percentage }}% OFF
                                    </Badge>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="space-y-3 p-4">
                                <div>
                                    <p
                                        class="text-xs font-medium tracking-wider text-muted-foreground uppercase"
                                    >
                                        {{ product.brand_name }}
                                    </p>
                                    <h3
                                        class="mt-1 line-clamp-2 truncate text-lg font-bold transition-colors group-hover:text-primary"
                                        :title="product.item_name"
                                    >
                                        {{ product.item_name }}
                                    </h3>
                                </div>

                                <div class="flex items-baseline gap-2">
                                    <template
                                        v-if="
                                            product.is_damaged &&
                                            product.discounted_price !== null
                                        "
                                    >
                                        <span
                                            class="text-2xl font-bold text-red-500"
                                        >
                                            {{
                                                formatCurrency(
                                                    product.discounted_price!,
                                                )
                                            }}
                                        </span>
                                        <span
                                            class="text-sm text-muted-foreground line-through"
                                        >
                                            {{
                                                formatCurrency(
                                                    product.unit_price,
                                                )
                                            }}
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span
                                            class="text-2xl font-bold text-primary"
                                        >
                                            {{
                                                formatCurrency(
                                                    product.unit_price,
                                                )
                                            }}
                                        </span>
                                        <span
                                            class="text-xs text-muted-foreground"
                                            >per unit</span
                                        >
                                    </template>
                                </div>

                                <div
                                    class="flex items-center justify-between text-xs text-muted-foreground"
                                >
                                    <span>Code: {{ product.item_code }}</span>
                                    <span
                                        class="font-semibold"
                                        :class="{
                                            'text-destructive':
                                                product.quantity <= 0,
                                            'text-warning':
                                                product.quantity > 0 &&
                                                product.quantity <=
                                                    product.restock_threshold,
                                            'text-green-600':
                                                product.quantity >
                                                product.restock_threshold,
                                        }"
                                    >
                                        {{ product.quantity }} available
                                    </span>
                                </div>
                            </div>
                        </CardContent>

                        <CardFooter class="p-4 pt-0">
                            <Button
                                :disabled="product.quantity <= 0"
                                @click="(e: Event) => addToCart(product, e)"
                                class="w-full transition-all duration-200 group-hover:bg-primary group-hover:text-primary-foreground"
                                size="lg"
                            >
                                <ShoppingCart class="mr-2 h-4 w-4" />
                                {{
                                    product.quantity <= 0
                                        ? 'Out of Stock'
                                        : 'Add to Cart'
                                }}
                            </Button>
                        </CardFooter>
                    </Card>
                </Link>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center justify-center px-4 py-16"
            >
                <div class="mb-4 rounded-full bg-muted p-6">
                    <Package class="h-12 w-12 text-muted-foreground" />
                </div>
                <h3 class="mb-2 text-xl font-semibold">No products found</h3>
                <p class="mb-6 max-w-md text-center text-muted-foreground">
                    We couldn't find any products matching your search. Try
                    adjusting your filters or search terms.
                </p>
                <Button @click="resetFilters" variant="outline">
                    Clear Filters
                </Button>
            </div>

            <!-- Pagination -->
            <Pagination :pagination="products" />
        </div>
    </AppLayout>
</template>
