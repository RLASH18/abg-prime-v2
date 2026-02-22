<?php

namespace App\Services;

use App\Models\Item;
use App\Repositories\Interfaces\OrderItemRepositoryInterface;
use Gemini\Data\Content;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\File;

class ItemForecastService
{
    /**
     * Inject the OrderItem repository.
     *
     * @param OrderItemRepositoryInterface $orderItemRepo
     */
    public function __construct(
        protected OrderItemRepositoryInterface $orderItemRepo
    ) {}

    /**
     * Generate an AI forecast report for a given item.
     *
     * @param  Item  $item
     * @return string
     */
    public function generate(Item $item): string
    {
        $context = $this->buildContext($item);
        $systemPrompt = $this->getSystemPrompt();

        $response = Gemini::generativeModel('gemini-2.5-flash')
            ->withSystemInstruction(Content::parse($systemPrompt))
            ->startChat([])
            ->sendMessage($context);

        return $response->text();
    }

    /**
     * Load the item forecast system prompt from the markdown file.
     *
     * @return string
     */
    private function getSystemPrompt(): string
    {
        $path = resource_path('views/prompts/item-forecast.md');

        return File::exists($path) ? File::get($path) : '';
    }

    /**
     * Build a structured context string from the item's data and sales history.
     *
     * @param Item $item
     * @return string
     */
    private function buildContext(Item $item): string
    {
        $days = 90;
        $salesData = $this->orderItemRepo->getSalesHistory($item->id, $days);
        $latestSales = $this->orderItemRepo->getLatestSale($item->id);

        $totalUnitsSold = $salesData->sum('quantity');
        $totalOrders = $salesData->count();
        $avgMonthlySales = round($totalUnitsSold / 3, 1);
        $avgDailySales = round($totalUnitsSold / $days, 2);
        $daysSinceLastSale = $latestSales
            ? (int) now()->diffInDays($latestSales->created_at)
            : null;

        $context = "Analyze the following inventory item and generate forecast report:\n\n";
        $context .= "**Item:** {$item->item_name}";

        if ($item->brand_name) {
            $context .= " ({$item->brand_name})";
        }

        $context .= "\n";
        $context .= "**Category:** {$item->category}\n";
        $context .= "**Item Code:** {$item->item_code}\n";
        $context .= "**Current Stock:** {$item->quantity} units\n";
        $context .= "**Restock Threshold:** {$item->restock_threshold} units\n";
        $context .= "**Unit Price:** ₱" . number_format($item->unit_price, 2) . "\n\n";
        $context .= "**Sales History (last {$days} days):**\n";

        if ($totalOrders === 0) {
            $context .= "- No sales recorded in the last {$days} days.\n";
        } else {
            $context .= "- Total units sold: {$totalUnitsSold}\n";
            $context .= "- Total orders: {$totalOrders}\n";
            $context .= "- Average monthly sales: ~{$avgMonthlySales} units/month\n";
            $context .= "- Average daily sales: ~{$avgDailySales} units/day\n";

            if ($daysSinceLastSale !== null) {
                $context .= "- Last sale: {$daysSinceLastSale} day(s) ago\n";
            }
        }

        return $context;
    }
}
