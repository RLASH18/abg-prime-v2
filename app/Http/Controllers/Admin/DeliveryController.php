<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\UpdateDeliveryStatusRequest;
use App\Services\DeliveryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliveryController extends Controller
{
    /**
     * Inject Delivery Service
     *
     * @param DeliveryService $deliveryService
     */
    public function __construct(
        protected DeliveryService $deliveryService
    ) {}

    /**
     * Display a listing of deliveries
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $deliveries = $this->deliveryService->getAllPaginated(10, $filters);

        return Inertia::render('admin/Delivery/Index', [
            'deliveries' => $deliveries,
            'filters' => $filters
        ]);
    }

    /**
     * Display the specified delivery
     */
    public function show(int $id)
    {
        $delivery = $this->deliveryService->findById($id);

        if (! $delivery) {
            return $this->flashError('Delivery not found', 'admin.deliveries.index');
        }

        return Inertia::render('admin/Delivery/Show', [
            'delivery' => $delivery
        ]);
    }

    /**
     * Update delivery status
     */
    public function updateStatus(UpdateDeliveryStatusRequest $request, int $id)
    {
        $validated = $request->validated();
        $additionalData = [];

        if (isset($validated['driver_name'])) {
            $additionalData['driver_name'] = $validated['driver_name'];
        }

        if (isset($validated['scheduled_date'])) {
            $additionalData['scheduled_date'] = $validated['scheduled_date'];
        }

        if (isset($validated['remarks'])) {
            $additionalData['remarks'] = $validated['remarks'];
        }

        // Handle proof of delivery upload
        if ($request->hasFile('proof_of_delivery')) {
            $additionalData['proof_of_delivery'] = $this->storeFile(
                $request->file('proof_of_delivery'),
                'deliveries/proofs',
                'proof'
            );
        }

        $updated = $this->deliveryService->updateDeliveryStatus(
            $id,
            $validated['status'],
            $additionalData
        );

        if ($updated) {
            return $this->flashSuccess('Deliver updated successfully', 'admin.deliveries.index');
        }

        return $this->flashError('Delivery not found', 'admin.deliveries.index');
    }
}
