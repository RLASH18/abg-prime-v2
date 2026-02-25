<?php

namespace App\Services\Customer;

use App\Models\DamagedItem;
use App\Models\Item;
use App\Repositories\DamagedItemRepository;
use App\Repositories\ItemRepository;
use App\Traits\Filterable;
use Illuminate\Pagination\LengthAwarePaginator;

class HomepageService
{
    use Filterable;

    /**
     * Inject item and damaged item repository
     *
     * @param ItemRepository $itemRepo
     * @param DamagedItemRepository $damagedItemRepo
     */
    public function __construct(
        protected ItemRepository $itemRepo,
        protected DamagedItemRepository $damagedItemRepo
    ) {}

    /**
     * Get all products paginated (including damaged items as separate entries)
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = [])
    {
        // Get regular items
        $itemsQuery = $this->itemRepo->query();

        $this->applySearchFilter($itemsQuery, $filters['search'] ?? null, [
            'item_name',
            'brand_name',
            'description',
            'unit_price'
        ]);
        $this->applyExactFilter($itemsQuery, 'category', $filters['category'] ?? null);

        $items = $itemsQuery->orderBy('created_at', 'desc')->get()->map(
            fn($item) => $this->normalizeItem($item)
        );

        // Get resellable damaged items with their parent item data
        $damagedItemsQuery = $this->damagedItemRepo->query()
            ->where('status', 'resellable')
            ->whereHas('item');

        // Apply filters for damaged items
        $this->applySearchFilter($damagedItemsQuery, $filters['search'] ?? null, [
            'item.item_name',
            'item.brand_name',
            'item.description'
        ]);
        $this->applyExactFilter($damagedItemsQuery, 'item.category', $filters['category'] ?? null);

        $damagedItems = $damagedItemsQuery->orderBy('created_at', 'desc')->get();

        // Transform damaged items to product format
        $transformedDamagedItems = $damagedItems->map(
            fn($damagedItem) => $this->transformDamagedItem($damagedItem)
        );

        // Merge regular items and damaged items
        $allProducts = $items->concat($transformedDamagedItems)
            ->sortByDesc('created_at')
            ->values();

        // Manually paginate the merged collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $allProducts->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentPageItems,
            $allProducts->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Find a product by ID (check both regular items and damaged items)
     *
     * @param int $id
     * @param int|null $damagedItemId
     * @return object|null
     */
    public function find(int $id, ?int $damagedItemId = null)
    {
        $item = $this->itemRepo->find($id);

        if (! $item) {
            return null;
        }

        // If damaged_item_id is provided, return damaged item version
        if ($damagedItemId) {
            $damagedItem = $this->damagedItemRepo->query()
                ->where('id', $damagedItemId)
                ->where('item_id', $id)
                ->where('status', 'resellable')
                ->first();

            if (! $damagedItem) {
                return null;
            }

            return $this->transformDamagedItem($damagedItem);
        }

        return $this->normalizeItem($item);
    }

    /**
     * Normalize a regular item with damage flags
     *
     * @param Item $item
     * @return Item
     */
    private function normalizeItem(Item $item): Item
    {
        $item->is_damaged = false;
        $item->damaged_item_id = null;
        $item->discount_percentage = null;
        $item->discounted_price = null;

        return $item;
    }

    /**
     * Transform a damaged item to product format
     *
     * @param DamagedItem $damagedItem
     * @return object
     */
    private function transformDamagedItem(DamagedItem $damagedItem): object
    {
        $item = $damagedItem->item;

        return (object) [
            'id' => $item->id,
            'item_code' => $item->item_code . '-D' . $damagedItem->id,
            'item_name' => $item->item_name,
            'brand_name' => $item->brand_name,
            'category' => $item->category,
            'unit_price' => $item->unit_price,
            'quantity' => $damagedItem->quantity,
            'restock_threshold' => $item->restock_threshold,
            'description' => $item->description,
            'item_image_1' => $item->item_image_1,
            'item_image_2' => $item->item_image_2,
            'item_image_3' => $item->item_image_3,
            'is_damaged' => true,
            'damaged_item_id' => $damagedItem->id,
            'discount_percentage' => $damagedItem->discount_percentage,
            'discounted_price' => $damagedItem->discounted_price,
            'remarks' => $damagedItem->remarks,
            'created_at' => $damagedItem->created_at,
            'updated_at' => $damagedItem->updated_at,
        ];
    }
}
