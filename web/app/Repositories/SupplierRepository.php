<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Models\Supplier;

class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    /**
     * Inject the Supplier model
     * 
     * @param Supplier $supplier
     */
    public function __construct(Supplier $supplier)
    {
        parent::__construct($supplier);
    }
}
