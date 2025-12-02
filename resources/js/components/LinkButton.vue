<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Button from './ui/button/Button.vue';
import { Plus, ArrowLeft } from 'lucide-vue-next';
import { computed } from 'vue';

type ButtonMode = 'create' | 'back';

interface Props {
    href: string;
    label?: string;
    mode?: ButtonMode;
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const config = computed(() => {
    if (props.mode === 'back') {
        return {
            icon: ArrowLeft,
            variant: 'outline' as const,
            defaultLabel: 'Back'
        };
    }

    return {
        icon: Plus,
        variant: 'default' as const,
        defaultLabel: 'Create',
    };
});
</script>

<template>
    <Button
        :variant="config.variant"
        asChild
        size="lg"
    >
        <Link :href="href">
            <component :is="config.icon" class="h-4 w-4" />
            <span>{{ label || config.defaultLabel }}</span>
        </Link>
    </Button>
</template>
