<?php

namespace App\Modules\Licensing\Http\Controllers\Admin;

use App\Modules\Licensing\Domain\Models\ProductModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleAdminController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(ProductModule::orderBy('key')->get());
    }
}
