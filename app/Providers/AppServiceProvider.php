<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\BillingRepository;
use App\Repositories\DamagedItemRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\BillingRepositoryInterface;
use App\Repositories\Interfaces\DamagedItemRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\ItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Repository bindings
     *
     * @var array<class-string, class-string>
     */
    protected $repositories = [
        BaseRepositoryInterface::class => BaseRepository::class,
        ItemRepositoryInterface::class => ItemRepository::class,
        SupplierRepositoryInterface::class => SupplierRepository::class,
        DamagedItemRepositoryInterface::class => DamagedItemRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        BillingRepositoryInterface::class => BillingRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
