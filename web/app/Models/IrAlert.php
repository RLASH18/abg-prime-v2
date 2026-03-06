<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IrAlert extends Model
{
    protected $fillable = [
        'item_code',
        'alert_type',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];
}
