<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\DamagedItem\StoreDamagedItemRequest;
use App\Services\DamagedItemService;
use App\Traits\InteractsWithFlash;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DamagedItemController extends Controller
{
    use InteractsWithFlash;

    /**
     * Inject DamagedItem Service
     *
     * @param DamagedItemService $damagedItemService
     */
    public function __construct(
        protected DamagedItemService $damagedItemService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $damagedItems = $this->damagedItemService->getAllPaginated(10, $filters);

        return Inertia::render('admin/Inventory/DamagedItems/Index', [
            'damagedItems' => $damagedItems,
            'filters' => $filters
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDamagedItemRequest $request)
    {
        $this->damagedItemService->markAsDamaged($request->validated());
        return $this->flashSuccess('Item marked as damaged', 'admin.items.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->damagedItemService->deleteDamagedItem($id);

        if (! $result) {
            return $this->flashError('Damaged item not found', 'admin.damaged-items.index');
        }

        return $this->flashSuccess('Damaged item record deleted', 'admin.damaged-items.index');
    }
}
