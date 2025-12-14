<?php

namespace App\Http\Controllers;

use App\Traits\InteractsWithFlash;
use App\Traits\HandlesFileUploads;

abstract class Controller
{
    use InteractsWithFlash, HandlesFileUploads;
}
