<?php

namespace App\Services\Customer;

use App\Repositories\ItemRepository;
use App\Traits\Filterable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HomepageService
{
    use Filterable;

    /**
     * Inject item repository
     *
     * @param ItemRepository $itemRepo
     */
    public function __construct(
        protected ItemRepository $itemRepo
    ) {}

    /**
     * Get all products paginated
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllProductsPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->itemRepo->query();

        $this->applySearchFilter($query, $filters['search'] ?? null, ['item_name', 'brand_name', 'description', 'unit_price']);
        $this->applyExactFilter($query, 'category', $filters['category'] ?? null);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
