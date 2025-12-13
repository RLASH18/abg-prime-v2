<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    /**
     * Inject Billing Service
     *
     * @param BillingService $billingService
     */
    public function __construct(
        protected BillingService $billingService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'payment_method']);
        $billings = $this->billingService->getAllPaginated(10, $filters);

        return Inertia::render('admin/Billing/Index', [
            'billings' => $billings,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $billing = $this->billingService->findById($id);

        if (! $billing) {
            return $this->flashError('Billing not found', 'admin.billings.index');
        }

        return Inertia::render('admin/Billing/Show', [
            'billing' => $billing
        ]);
    }
}
