<?php

namespace App\Repositories;

use App\Models\IrAlert;
use App\Repositories\Interfaces\IrAlertRepositoryInterface;

class IrAlertRepository extends BaseRepository implements IrAlertRepositoryInterface
{
    /**
     * Inject the IrAlert model.
     *
     * @param IrAlert $irAlert
     */
    public function __construct(IrAlert $irAlert)
    {
        parent::__construct($irAlert);
    }
}
