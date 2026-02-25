<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useItemForecast } from '@/composables/useItemForecast';
import { BrainCircuit, RefreshCw } from 'lucide-vue-next';
import { marked } from 'marked';
import { ref } from 'vue';

marked.setOptions({ breaks: true });

const props = defineProps<{
    itemId: number;
}>();

const { loading, generateForecast } = useItemForecast();

const report = ref<string | null>(null);
const hasError = ref(false);

// Parse markdown report to safe HTML
const parsedReport = () =>
    report.value ? (marked.parse(report.value) as string) : '';

const handleGenerate = async () => {
    hasError.value = false;
    try {
        report.value = await generateForecast(props.itemId);
    } catch {
        hasError.value = true;
    }
};
</script>

<template>
    <div class="rounded-lg border border-border bg-card p-6 shadow-sm">
        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="rounded-lg bg-primary/10 p-2">
                    <BrainCircuit :size="20" class="text-primary" />
                </div>
                <div>
                    <h2 class="text-lg font-semibold">AI Forecast</h2>
                    <p class="text-sm text-muted-foreground">
                        AI-generated inventory analysis based on sales data
                    </p>
                </div>
            </div>
            <Button
                @click="handleGenerate"
                :disabled="loading"
                size="sm"
                class="gap-2"
            >
                <RefreshCw :size="14" :class="{ 'animate-spin': loading }" />
                {{ report ? 'Regenerate' : 'Analyze with AI' }}
            </Button>
        </div>

        <!-- Not yet generated -->
        <div
            v-if="!report && !loading && !hasError"
            class="flex flex-col items-center justify-center rounded-lg border border-dashed border-border bg-muted/40 py-10 text-center"
        >
            <BrainCircuit :size="40" class="mb-3 text-muted-foreground" />
            <p class="text-sm font-medium">No forecast generated yet</p>
            <p class="mt-1 text-xs text-muted-foreground">
                Click "Analyze with AI" to generate an inventory forecast report
                for this item.
            </p>
        </div>

        <!-- Loading state -->
        <div
            v-if="loading"
            class="flex flex-col items-center justify-center rounded-lg bg-muted/40 py-10 text-center"
        >
            <RefreshCw :size="32" class="mb-3 animate-spin text-primary" />
            <p class="text-sm text-muted-foreground">
                AI is analyzing this item...
            </p>
        </div>

        <!-- Error state -->
        <div
            v-if="hasError && !loading"
            class="rounded-lg bg-destructive/10 p-4 text-sm text-destructive"
        >
            Failed to generate forecast. Please try again.
        </div>

        <!-- Report result -->
        <div
            v-if="report && !loading"
            class="ai-message-content prose prose-sm max-w-none rounded-lg bg-muted/40 p-4 text-sm"
            v-html="parsedReport()"
        />
    </div>
</template>
