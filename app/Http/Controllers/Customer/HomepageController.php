<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\HomepageService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomepageController extends Controller
{
    /**
     * Inject HomepageService
     *
     * @param HomepageService $homepageService
     */
    public function __construct(
        protected HomepageService $homepageService
    ) {}

    /**
     * Display all products on the customer homepage.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category']);
        $items = $this->homepageService->getAllProductsPaginated(24, $filters);

        return Inertia::render('customer/Homepage/Index', [
            'items' => $items,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(int $id)
    {
        return Inertia::render('customer/Homepage/Show');
    }
}
