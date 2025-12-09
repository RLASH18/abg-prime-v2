<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Traits\Filterable;

class SupplierService
{
    use Filterable;

    /**
     * Inject the supplier repository
     * 
     * @param SupplierRepositoryInterface $supplierRepo
     */
    public function __construct(
        protected SupplierRepositoryInterface $supplierRepo
    ) {}

    /**
     * Get all suppliers
     * 
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $filters = [])
    {
        $query = $this->supplierRepo->query();

        $this->applySearchFilter($query, $filters['search'] ?? null, ['supplier_name', 'email', 'phone']);

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get a supplier by ID
     * 
     * @param int $id
     * @return Supplier|null
     */
    public function getById(int $id)
    {
        return $this->supplierRepo->find($id);
    }

    /**
     * Create a new supplier
     * 
     * @param array $data
     * @return Supplier
     */
    public function createSupplier(array $data): Supplier
    {
        return $this->supplierRepo->create($data);
    }

    /**
     * Update a supplier
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSupplier(int $id, array $data): bool
    {
        $supplier = $this->getById($id);

        if (! $supplier) {
            return false;
        }

        return $this->supplierRepo->update($id, $data);
    }

    /**
     * Delete a supplier
     * 
     * @param int $id
     * @return bool
     */
    public function deleteSupplier(int $id): bool
    {
        $supplier = $this->getById($id);

        if (! $supplier) {
            return false;
        }

        return $this->supplierRepo->delete($id);
    }
}
