<?php

namespace App\Services;

use App\Models\IrAlert;

class IrAlertService
{
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
        return IrAlert::create([
            'item_code'  => $itemCode,
            'alert_type' => $alertType,
            'notes'      => $notes,
        ]);
    }

    /**
     * Get paginated IR alert history, newest first.
     *
     * @param  int  $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15)
    {
        return IrAlert::latest()->paginate($perPage);
    }
}
