<?php

namespace App\Repositories;

use App\Models\DamagedItem;
use App\Repositories\Interfaces\DamagedItemRepositoryInterface;

class DamagedItemRepository extends BaseRepository implements DamagedItemRepositoryInterface
{
    /**
     * Inject DamagedItem model
     *
     * @param DamagedItem $damagedItem
     */
    public function __construct(DamagedItem $damagedItem)
    {
        parent::__construct($damagedItem);
    }
}
