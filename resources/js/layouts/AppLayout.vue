<script setup lang="ts">
import CustomerAppLayout from '@/layouts/app/AppHeaderLayout.vue';
import AdminAppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import flasher from '@flasher/flasher';
import NotyfPlugin from '@flasher/flasher-notyf';
import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

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

            // Manually clear messages from history state to prevent persistence
            if (
                window.history.state &&
                window.history.state.page &&
                window.history.state.page.props
            ) {
                const state = { ...window.history.state };
                state.page.props.messages = null;
                window.history.replaceState(state, '');
            }
        }
    },
    { immediate: true },
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
