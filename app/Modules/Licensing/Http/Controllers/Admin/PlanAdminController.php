<?php

namespace App\Modules\Licensing\Http\Controllers\Admin;

use App\Modules\Licensing\Domain\Models\Plan;
use App\Modules\Licensing\Domain\Models\ProductModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanAdminController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(Plan::with('modules')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:plans,key',
            'name' => 'required|string|max:100',
            'billing_period' => 'required|in:monthly,yearly,one_time',
            'price_minor' => 'required|integer|min:0',
            'currency' => 'required|string|size:3',
            'module_keys' => 'nullable|array',
            'module_keys.*' => 'string|exists:product_modules,key',
        ]);

        $moduleKeys = $validated['module_keys'] ?? [];
        unset($validated['module_keys']);

        $plan = Plan::create(array_merge($validated, ['is_active' => true]));

        if (! empty($moduleKeys)) {
            $moduleIds = ProductModule::whereIn('key', $moduleKeys)->pluck('id');
            $plan->modules()->sync($moduleIds);
        }

        return response()->json($plan->load('modules'), 201);
    }

    public function update(Request $request, Plan $plan): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'price_minor' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'module_keys' => 'nullable|array',
        ]);

        if (isset($validated['module_keys'])) {
            $moduleIds = ProductModule::whereIn('key', $validated['module_keys'])->pluck('id');
            $plan->modules()->sync($moduleIds);
            unset($validated['module_keys']);
        }

        $plan->update($validated);

        return response()->json($plan->load('modules'));
    }
}
