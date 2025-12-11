<?php

namespace App\Services;

use App\Models\Item;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Traits\Filterable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    use Filterable;

    /**
     * Inject the item repository
     *
     * @param ItemRepositoryInterface $itemRepo
     */
    public function __construct(
        protected ItemRepositoryInterface $itemRepo
    ) {}

    /**
     * Get paginated items with filters
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = [])
    {
        $query = $this->itemRepo->query();

        // Search filter
        $this->applySearchFilter($query, $filters['search'] ?? null, ['item_name', 'item_code', 'brand_name']);

        // Apply category filter
        $this->applyExactFilter($query, 'category', $filters['category'] ?? null);

        // Stock status filter
        $this->applyCustomFilter($query, $filters['stock_status'] ?? null, function ($q, $status) {
            return match ($status) {
                'out_of_stock' => $q->where('quantity', '<=', 0),
                'low_stock' => $q->whereColumn('quantity', '<=', 'restock_threshold')
                    ->where('quantity', '>', 0),
                'in_stock' => $q->whereColumn('quantity', '>', 'restock_threshold'),
                default => $q,
            };
        });

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find an item by ID
     *
     * @param int $id
     * @return Item|null
     */
    public function find($id)
    {
        return $this->itemRepo->find($id);
    }

    /**
     * Create a new item
     *
     * @param array $data
     * @param array $files
     * @return Item
     */
    public function createItem(array $data, array $files): Item
    {
        // Generate item code
        $data['item_code'] = $this->generateItemCode($data['category']);

        // Handle images
        $data = $this->handleImageUploads($data, $files);

        return $this->itemRepo->create($data);
    }

    /**
     * Update an existing item
     *
     * @param int $id
     * @param array $data
     * @param array $files
     * @return bool
     */
    public function updateItem(int $id, array $data, array $files): bool
    {
        $item = $this->find($id);

        if (! $item) {
            return false;
        }

        // Regenerate item code if category changed
        if (isset($data['category']) && $data['category'] !== $item->category) {
            $data['item_code'] = $this->generateItemCode($data['category']);
        }

        // Handle images
        $data = $this->handleImageUploads($data, $files, $item);

        return $this->itemRepo->update($id, $data);
    }

    /**
     * Delete an item
     *
     * @param int $id
     * @return bool
     */
    public function deleteItem(int $id): bool
    {
        $item = $this->find($id);

        if (! $item) {
            return false;
        }

        // Delete image
        $this->deleteImages($item);

        return $this->itemRepo->delete($id);
    }

    /**
     * Generate a unique item code
     *
     * @param string $category
     * @return string
     */
    protected function generateItemCode(string $category): string
    {
        $prefix = match ($category) {
            'Hand Tools' => 'HT',
            'Power Tools' => 'PT',
            'Construction Materials' => 'CM',
            'Locks and Security' => 'LS',
            'Plumbing' => 'PL',
            'Electrical' => 'EL',
            'Paint and Finishes' => 'PF',
            'Chemicals' => 'CH',
            default => 'OT', // Other
        };

        $latestItem = $this->itemRepo->latestByCategory($category);

        if (! $latestItem) {
            return $prefix . '001';
        }

        // Extract number from the last item code
        $lastCode = $latestItem->item_code;
        $lastNumber = intval(substr($lastCode, strlen($prefix)));
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Handle image uploads
     *
     * @param array $data
     * @param array $files
     * @param Item|null $existingInventory
     * @return array
     */
    protected function handleImageUploads(array $data, array $files, ?Item $existingInventory = null): array
    {
        $imageFields = ['item_image_1', 'item_image_2', 'item_image_3'];

        foreach ($imageFields as $field) {
            if (isset($files[$field]) && $files[$field] instanceof UploadedFile) {
                // Delete old image if updating
                if ($existingInventory && $existingInventory->$field) {
                    Storage::disk('public')->delete($existingInventory->$field);
                }

                // Generate custom filename
                $file = $files[$field];
                $extension = $file->getClientOriginalExtension();
                $timestamp = now()->format('YmdHis');
                $filename = "{$field}_{$timestamp}.{$extension}";

                // Store with custom name in items_img folder
                $data[$field] = $file->storeAs('items_img', $filename, 'public');
            } elseif ($existingInventory) {
                if (array_key_exists($field, $data) && empty($files[$field])) {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    /**
     * Delete all images
     *
     * @param Item $item
     * @return void
     */
    protected function deleteImages(Item $item)
    {
        $imageFields = ['item_image_1', 'item_image_2', 'item_image_3'];

        foreach ($imageFields as $field) {
            if ($item->$field) {
                Storage::disk('public')->delete($item->$field);
            }
        }
    }
}
