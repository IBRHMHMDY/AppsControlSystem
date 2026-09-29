<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function show(Application $application): ApplicationResource
    {
        return new ApplicationResource($application);
    }
}
