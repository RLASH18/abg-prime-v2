<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Models\Supplier;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Inject Item Service
     *
     * @param ItemService $itemService
     */
    public function __construct(
        protected ItemService $itemService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category', 'stock_status']);
        $items = $this->itemService->getAllPaginated(10, $filters);

        return Inertia::render('admin/Inventory/Items/Index', [
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

        return Inertia::render('admin/Inventory/Items/Create', [
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        $this->itemService->createItem($request->validated(), $request->allFiles());
        return $this->flashSuccess('Item created successfully', 'admin.items.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $item = $this->itemService->find($id);

        if (! $item) {
            return $this->flashError('Item not found', 'admin.items.index');
        }

        if (method_exists($item, 'supplier')) {
            $item->load('supplier');
        }

        return Inertia::render('admin/Inventory/Items/Show', [
            'item' => $item,
            'supplier' => $item->supplier ?? null
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $item = $this->itemService->find($id);

        if (! $item) {
            return $this->flashError('Item not found', 'admin.items.index');
        }

        $suppliers = Supplier::all();

        return Inertia::render('admin/Inventory/Items/Edit', [
            'item' => $item,
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, int $id)
    {
        $item = $this->itemService->updateItem($id, $request->validated(), $request->allFiles());

        if (! $item) {
            return $this->flashError('Item not found', 'admin.items.index');
        }

        return $this->flashSuccess('Item updated successfully', 'admin.items.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $item = $this->itemService->deleteItem($id);

        if (! $item) {
            return $this->flashError('Item not found', 'admin.items.index');
        }

        return $this->flashSuccess('Item deleted successfully', 'admin.items.index');
    }
}
