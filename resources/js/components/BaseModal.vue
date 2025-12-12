<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from './ui/dialog';

interface Props {
    open: boolean;
    title?: string,
    description?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl';
}

withDefaults(defineProps<Props>(), {
    title: '',
    description: '',
    size: 'md',
});

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const sizeClasses = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent :class="sizeClasses[size]">
            <DialogHeader>
                <slot name="header">
                    <DialogTitle>{{ title }}</DialogTitle>
                    <DialogDescription v-if="description">{{ description }}</DialogDescription>
                </slot>
            </DialogHeader>

            <slot />

            <DialogFooter v-if="$slots.footer">
                <slot name="footer" />
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
