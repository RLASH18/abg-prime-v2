<?php

namespace App\Services;

use App\Models\Inventory;
use App\Repositories\Interfaces\InventoryRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class InventoryService
{
    /**
     * Inject the inventory repository
     * 
     * @param InventoryRepositoryInterface $inventoryRepo
     */
    public function __construct(
        protected InventoryRepositoryInterface $inventoryRepo
    ) {}

    /**
     * Get paginated inventory items with filters
     * 
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 10, array $filters = [])
    {
        $query = $this->inventoryRepo->query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%")
                    ->orWhere('brand_name', 'like', "%{$search}%");
            });
        }

        // Category filter
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        // Stock status filter
        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('quantity', '<=', 'restock_threshold')
                        ->where('quantity', '>', 0);
                    break;
                case 'in_stock':
                    $query->whereColumn('quantity', '>', 'restock_threshold');
                    break;
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Find an inventory item by ID
     * 
     * @param int $id
     * @return Inventory|null
     */
    public function find($id)
    {
        return $this->inventoryRepo->find($id);
    }

    /**
     * Create a new inventory item
     * 
     * @param array $data
     * @param array $files
     * @return Inventory
     */
    public function createInventory(array $data, array $files): Inventory
    {
        // Generate item code
        $data['item_code'] = $this->generateItemCode($data['category']);

        // Handle images
        $data = $this->handleImageUploads($data, $files);

        return $this->inventoryRepo->create($data);
    }

    /**
     * Update an existing inventory item
     * 
     * @param int $id
     * @param array $data
     * @param array $files
     * @return bool
     */
    public function updateInventory(int $id, array $data, array $files): bool
    {
        $inventory = $this->find($id);

        if (! $inventory) {
            return false;
        }

        // Regenerate item code if category changed
        if (isset($data['category']) && $data['category'] !== $inventory->category) {
            $data['item_code'] = $this->generateItemCode($data['category']);
        }

        // Handle images
        $data = $this->handleImageUploads($data, $files, $inventory);

        return $this->inventoryRepo->update($id, $data);
    }

    /**
     * Delete an inventory item
     * 
     * @param int $id
     * @return bool
     */
    public function deleteInventory(int $id): bool
    {
        $inventory = $this->find($id);

        if (! $inventory) {
            return false;
        }

        // Delete image
        $this->deleteImages($inventory);

        return $this->inventoryRepo->delete($id);
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

        $latestItem = $this->inventoryRepo->latestByCategory($category);

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
     * @param Inventory|null $existingInventory
     * @return array
     */
    protected function handleImageUploads(array $data, array $files, ?Inventory $existingInventory = null): array
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
     * @param Inventory $inventory
     * @return void
     */
    protected function deleteImages(Inventory $inventory)
    {
        $imageFields = ['item_image_1', 'item_image_2', 'item_image_3'];

        foreach ($imageFields as $field) {
            if ($inventory->$field) {
                Storage::disk('public')->delete($inventory->$field);
            }
        }
    }
}
