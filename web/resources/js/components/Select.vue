<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';
import { useVModel } from '@vueuse/core';
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';

interface SelectOption {
    value: string;
    label: string;
}

const props = defineProps<{
    modelValue?: string;
    options: SelectOption[] | string[];
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    class?: HTMLAttributes['class'];
}>();

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
});

// Normalize options to always have value and label
const normalizedOptions = computed(() => {
    return props.options.map((option) => {
        if (typeof option === 'string') {
            return { value: option, label: option };
        }
        return option;
    });
});
</script>

<template>
    <div class="relative">
        <!-- Hidden input for form submission -->
        <input type="hidden" v-bind="$attrs" :value="modelValue || ''" />

        <Select v-model="modelValue" :required="required" :disabled="disabled">
            <SelectTrigger
                data-slot="select"
                :class="
                    cn(
                        'flex h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30',
                        'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                        'aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40',
                        'data-[placeholder]:text-muted-foreground',
                        'items-center justify-between text-start [&>span]:truncate',
                        props.class,
                    )
                "
            >
                <SelectValue :placeholder="placeholder || 'Select an option'" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="option in normalizedOptions"
                    :key="option.value"
                    :value="option.value"
                >
                    {{ option.label }}
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
