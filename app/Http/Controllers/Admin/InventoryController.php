<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryRequest;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    /**
     * Inject InventoryService
     * 
     * @param InventoryService $inventoryService
     */
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'category',
            'stock_status',
        ]);

        $items = $this->inventoryService->getPaginated(10, $filters);

        return Inertia::render('admin/Inventory/Index', [
            'items' => $items,
            'filters' => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();

        return Inertia::render('admin/Inventory/Create', [
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request)
    {
        $this->inventoryService->createInventory($request->validated(), $request->allFiles());
        return $this->flashSuccess('Item created successfully', 'admin.inventory.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $inventory = $this->inventoryService->find($id);

        if (! $inventory) {
            return $this->flashError('Item not found', 'admin.inventory.index');
        }

        if (method_exists($inventory, 'supplier')) {
            $inventory->load('supplier');
        }

        return Inertia::render('admin/Inventory/Show', [
            'inventory' => $inventory,
            'supplier' => $inventory->supplier ?? null
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $inventory = $this->inventoryService->find($id);

        if (! $inventory) {
            return $this->flashError('Item not found', 'admin.inventory.index');
        }

        $suppliers = Supplier::all();

        return Inertia::render('admin/Inventory/Edit', [
            'inventory' => $inventory,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreInventoryRequest $request, int $id)
    {
        $inventory = $this->inventoryService->updateInventory($id, $request->validated(), $request->allFiles());

        if ($inventory) {
            return $this->flashSuccess('Item updated successfully', 'admin.inventory.index');
        }

        return $this->flashError('Item not found', 'admin.inventory.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $inventory = $this->inventoryService->deleteInventory($id);

        if ($inventory) {
            return $this->flashSuccess('Item deleted successfully', 'admin.inventory.index');
        }

        return $this->flashError('Item not found', 'admin.inventory.index');
    }
}
