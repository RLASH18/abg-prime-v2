<?php

namespace App\Services;

use App\Models\DamagedItem;
use App\Models\Item;
use App\Repositories\Interfaces\DamagedItemRepositoryInterface;
use App\Traits\Filterable;

class DamagedItemService
{
    use Filterable;

    /**
     * Inject the damaged item repository
     *
     * @param DamagedItemRepositoryInterface $damagedItemRepo
     */
    public function __construct(
        protected DamagedItemRepositoryInterface $damagedItemRepo
    ) {}

    /**
     * Get paginated damaged items with filters
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage, array $filters = [])
    {
        $query = $this->damagedItemRepo->query()->with('item');

        $this->applySearchFilter($query, $filters['search'] ?? null, ['remarks']);
        $this->applyExactFilter($query, 'status', $filters['status'] ?? null);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find a damaged item by ID
     *
     * @param int $id
     * @return DamagedItem|null
     */
    public function find(int $id)
    {
        return $this->damagedItemRepo->find($id);
    }

    /**
     * Mark an item as damaged
     *
     * @param array $data
     * @return DamagedItem
     */
    public function markAsDamaged(array $data): DamagedItem
    {
        $item = Item::findOrFail($data['item_id']);

        // Reduced item quantity
        $item->decrement('quantity', $data['quantity']);

        return $this->damagedItemRepo->create($data);
    }

    /**
     * Update a damaged item
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateDamagedItem(int $id, array $data): bool
    {
        return $this->damagedItemRepo->update($id, $data);
    }

    /**
     * Delete a damaged item
     *
     * @param int $id
     * @return bool
     */
    public function deleteDamagedItem(int $id): bool
    {
        $damagedItem = $this->find($id);

        if (! $damagedItem) {
            return false;
        }

        // Restore item quantity
        $damagedItem->item->increment('quantity', $damagedItem->quantity);

        return $this->damagedItemRepo->delete($id);
    }
}
