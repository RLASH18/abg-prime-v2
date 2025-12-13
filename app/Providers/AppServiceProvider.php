<?php

namespace App\Providers;

use App\Repositories\Interfaces\{
    BaseRepositoryInterface,
    SupplierRepositoryInterface,
    ItemRepositoryInterface,
    DamagedItemRepositoryInterface,
    OrderRepositoryInterface,
    BillingRepositoryInterface
};
use App\Repositories\{
    BaseRepository,
    SupplierRepository,
    ItemRepository,
    DamagedItemRepository,
    OrderRepository,
    BillingRepository
};
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
