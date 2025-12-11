<script setup lang="ts" generic="T extends Record<string, any>">
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { DataTableAction, DataTableColumn } from '@/types/admin';

interface Props {
    data: T[];
    columns: DataTableColumn<T>[];
    actions?: DataTableAction<T>[];
    caption?: string;
    emptyMessage?: string;
    loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    caption: '',
    emptyMessage: 'No data available',
    loading: false,
});

const getCellValue = (row: T, column: DataTableColumn<T>): any => {
    const value = row[column.key as keyof T];

    if (column.render) {
        return column.render(value, row)
    }

    return value
}

const shouldShowAction = (action: DataTableAction<T>, row: T): boolean => {
    return action.show ? action.show(row) : true
}
</script>

<template>
    <div class="rounded-lg border border-border bg-card shadow-sm">
        <Table>
            <!-- <TableCaption v-if="caption" class="text-muted-foreground">{{ caption }}</TableCaption> -->
            <TableHeader>
                <TableRow class="border-b border-border bg-muted/50 hover:bg-muted/50">
                    <TableHead v-for="column in columns" :key="String(column.key)" :class="[
                        'font-semibold text-foreground',
                        column.align ? `text-${column.align}` : ''
                    ]">
                        {{ column.label }}
                    </TableHead>
                    <TableHead v-if="actions && actions.length > 0" class="text-center font-semibold text-foreground">
                        Actions
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <!-- Empty State -->
                <TableEmpty v-if="data.length === 0 && !loading" :colspan="columns.length + (actions?.length ? 1 : 0)">
                    <div class="flex flex-col items-center justify-center py-12 px-4">
                        <div class="rounded-full bg-gradient-to-br from-primary/10 to-primary/5 p-4 mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary/60" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-foreground mb-1">{{ emptyMessage }}</h3>
                        <p class="text-sm text-muted-foreground">Get started by adding your first item</p>
                    </div>
                </TableEmpty>

                <!-- Loading State -->
                <TableEmpty v-if="loading" :colspan="columns.length + (actions?.length ? 1 : 0)">
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
                        <p class="text-sm text-muted-foreground">Loading data...</p>
                    </div>
                </TableEmpty>

                <!-- Data Rows -->
                <TableRow v-for="(row, index) in data" :key="index"
                    class="group border-b border-border transition-colors hover:bg-muted/50">
                    <TableCell v-for="column in columns" :key="String(column.key)" :class="[
                        'py-4',
                        column.align ? `text-${column.align}` : '',
                        column.class || ''
                    ]">
                        <slot :name="`cell-${String(column.key)}`" :row="row" :value="getCellValue(row, column)">
                            {{ getCellValue(row, column) }}
                        </slot>
                    </TableCell>

                    <!-- Actions Column -->
                    <TableCell v-if="actions && actions.length > 0" class="py-4">
                        <div class="flex items-center justify-center gap-1">
                            <template v-for="action in actions" :key="action.label">
                                <Button v-if="shouldShowAction(action, row)" :variant="action.variant || 'ghost'"
                                    size="icon" @click="action.onClick(row)" :title="action.label"
                                    :class="['h-8 w-8 transition-colors', action.class]">
                                    <component :is="action.icon" :size="16" />
                                </Button>
                            </template>
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>