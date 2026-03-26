<?php

namespace App\Modules\Identity\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAdminController
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->input('search'), fn ($q, $s) =>
                $q->where('name', 'ilike', "%{$s}%")->orWhere('email', 'ilike', "%{$s}%")
            )
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($users);
    }
}
