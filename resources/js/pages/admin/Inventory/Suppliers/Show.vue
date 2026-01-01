<script setup lang="ts">
import LinkButton from '@/components/LinkButton.vue';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import suppliersRoutes from '@/routes/admin/suppliers';
import { type BreadcrumbItem } from '@/types';
import type { Supplier } from '@/types/admin';
import { Head } from '@inertiajs/vue3';
import {
    Building2,
    Calendar,
    CheckCircle2,
    Mail,
    MapPin,
    Phone,
    XCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    supplier: Supplier;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Suppliers',
        href: suppliersRoutes.index().url,
    },
    {
        title: props.supplier.supplier_name,
        href: suppliersRoutes.show(props.supplier.id).url,
    },
];

// Compute status badge variant
const statusVariant = computed(() => {
    return props.supplier.status === 'active' ? 'default' : 'destructive';
});

const statusIcon = computed(() => {
    return props.supplier.status === 'active' ? CheckCircle2 : XCircle;
});

const { formatDate } = useFormatters();
</script>

<template>
    <Head :title="`Suppliers - ${props.supplier.supplier_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
        >
            <!-- Header -->
            <div class="mb-4 flex items-start justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold">
                            {{ props.supplier.supplier_name }}
                        </h1>
                        <Badge :variant="statusVariant" class="capitalize">
                            <component
                                :is="statusIcon"
                                :size="14"
                                class="mr-1"
                            />
                            {{ props.supplier.status }}
                        </Badge>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        Supplier ID: #{{
                            props.supplier.id.toString().padStart(4, '0')
                        }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <LinkButton
                        :href="suppliersRoutes.edit(props.supplier.id).url"
                        label="Edit Supplier"
                    />
                    <LinkButton
                        :href="suppliersRoutes.index().url"
                        mode="back"
                        label="Back to list"
                    />
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Contact Information Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Building2 :size="20" class="text-primary" />
                            Contact Information
                        </CardTitle>
                        <CardDescription
                            >Primary contact details for this
                            supplier</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Email -->
                        <div
                            v-if="props.supplier.email"
                            class="flex items-start gap-3 rounded-lg bg-muted/50 p-3"
                        >
                            <div class="rounded-md bg-blue-500/10 p-2">
                                <Mail :size="18" class="text-blue-600" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="mb-0.5 text-xs text-muted-foreground">
                                    Email Address
                                </p>
                                <a
                                    :href="`mailto:${props.supplier.email}`"
                                    class="font-medium break-all text-blue-600 hover:underline"
                                >
                                    {{ props.supplier.email }}
                                </a>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div
                            v-if="props.supplier.phone"
                            class="flex items-start gap-3 rounded-lg bg-muted/50 p-3"
                        >
                            <div class="rounded-md bg-green-500/10 p-2">
                                <Phone :size="18" class="text-green-600" />
                            </div>
                            <div class="flex-1">
                                <p class="mb-0.5 text-xs text-muted-foreground">
                                    Phone Number
                                </p>
                                <a
                                    :href="`tel:${props.supplier.phone}`"
                                    class="font-medium text-green-600 hover:underline"
                                >
                                    {{ props.supplier.phone }}
                                </a>
                            </div>
                        </div>

                        <!-- No contact info -->
                        <div
                            v-if="
                                !props.supplier.email && !props.supplier.phone
                            "
                            class="py-8 text-center text-muted-foreground"
                        >
                            <p class="text-sm">
                                No contact information available
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Address Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MapPin :size="20" class="text-primary" />
                            Address
                        </CardTitle>
                        <CardDescription
                            >Physical location of the supplier</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="props.supplier.address"
                            class="rounded-lg bg-muted/50 p-4"
                        >
                            <p
                                class="text-sm leading-relaxed whitespace-pre-wrap"
                            >
                                {{ props.supplier.address }}
                            </p>
                        </div>
                        <div
                            v-else
                            class="py-8 text-center text-muted-foreground"
                        >
                            <MapPin
                                :size="32"
                                class="mx-auto mb-2 opacity-50"
                            />
                            <p class="text-sm">No address provided</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Status & Metadata Card -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Calendar :size="20" class="text-primary" />
                            Additional Information
                        </CardTitle>
                        <CardDescription
                            >Supplier status and metadata</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <!-- Status -->
                            <div
                                class="rounded-lg border border-border bg-card p-4"
                            >
                                <p class="mb-2 text-xs text-muted-foreground">
                                    Status
                                </p>
                                <div class="flex items-center gap-2">
                                    <component
                                        :is="statusIcon"
                                        :size="20"
                                        :class="
                                            props.supplier.status === 'active'
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    />
                                    <span
                                        class="text-lg font-semibold capitalize"
                                        >{{ props.supplier.status }}</span
                                    >
                                </div>
                            </div>

                            <!-- Created Date -->
                            <div
                                class="rounded-lg border border-border bg-card p-4"
                            >
                                <p class="mb-2 text-xs text-muted-foreground">
                                    Created Date
                                </p>
                                <p class="text-lg font-semibold">
                                    {{ formatDate(props.supplier.created_at) }}
                                </p>
                            </div>

                            <!-- Last Updated -->
                            <div
                                class="rounded-lg border border-border bg-card p-4"
                            >
                                <p class="mb-2 text-xs text-muted-foreground">
                                    Last Updated
                                </p>
                                <p class="text-lg font-semibold">
                                    {{ formatDate(props.supplier.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
