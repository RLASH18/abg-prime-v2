<?php

namespace App\Services;

use App\Models\IrAlert;
use App\Repositories\Interfaces\IrAlertRepositoryInterface;

class IrAlertService
{
    /**
     * Inject the IR alert repository.
     *
     * @param IrAlertRepositoryInterface $irAlertRepo
     */
    public function __construct(
        protected IrAlertRepositoryInterface $irAlertRepo
    ) {}

    /**
     * Log an IR sensor alert to the database.
     *
     * @param  string|null  $itemCode   RFID-scanned code, or null if unscanned
     * @param  string       $alertType  'unscanned' | 'scanned'
     * @param  string|null  $notes
     * @return IrAlert
     */
    public function logAlert(?string $itemCode, string $alertType = 'unscanned', ?string $notes = null): IrAlert
    {
        return $this->irAlertRepo->create([
            'item_code'  => $itemCode,
            'alert_type' => $alertType,
            'notes'      => $notes,
        ]);
    }
}
