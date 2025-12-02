<script setup lang="ts">
import AdminAppLayout from '@/layouts/app/AppSidebarLayout.vue';
import CustomerAppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import flasher from '@flasher/flasher';
import NotyfPlugin from '@flasher/flasher-notyf';

flasher.addPlugin('notyf', NotyfPlugin);

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const user = computed(() => page.props.auth.user);

watch(
    () => page.props.messages,
    (messages) => {
        if (messages) {
            messages.scripts = [];
            messages.styles = [];
            flasher.render(messages);
        }
    },
    { immediate: true }
);
</script>

<template>
    <AdminAppLayout v-if="user.role === 'admin'" :breadcrumbs="breadcrumbs">
        <slot />
    </AdminAppLayout>
    <CustomerAppLayout v-else :breadcrumbs="breadcrumbs">
        <slot />
    </CustomerAppLayout>
</template>
