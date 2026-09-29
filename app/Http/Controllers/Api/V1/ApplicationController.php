<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    use AuthorizesRequests;

    public function show(
        Request $request,
        Application $application,
    ): ApplicationResource {
        $this->authorize('view', $application);

        return new ApplicationResource($application);
    }
}
