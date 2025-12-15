<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Inject Supplier Service
     *
     * @param SupplierService $supplierService
     */
    public function __construct(
        protected SupplierService $supplierService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only('search');
        $suppliers = $this->supplierService->getAllPaginated(10, $filters);

        return Inertia::render('admin/Inventory/Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/Inventory/Suppliers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request)
    {
        $this->supplierService->createSupplier($request->validated());
        return $this->flashSuccess('Supplier created successfully', 'admin.suppliers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $supplier = $this->supplierService->getById($id);

        if (! $supplier) {
            return $this->flashError('Supplier not found', 'admin.suppliers.index');
        }

        return Inertia::render('admin/Inventory/Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $supplier = $this->supplierService->getById($id);

        if (! $supplier) {
            return $this->flashError('Supplier not found', 'admin.suppliers.index');
        }

        return Inertia::render('admin/Inventory/Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, int $id)
    {
        $supplier = $this->supplierService->updateSupplier($id, $request->validated());

        if ($supplier) {
            return $this->flashSuccess('Supplier updated successfully', 'admin.suppliers.index');
        }

        return $this->flashError('Supplier not found', 'admin.suppliers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $supplier = $this->supplierService->deleteSupplier($id);

        if ($supplier) {
            return $this->flashSuccess('Supplier deleted successfully', 'admin.suppliers.index');
        }

        return $this->flashError('Supplier not found', 'admin.suppliers.index');
    }
}
