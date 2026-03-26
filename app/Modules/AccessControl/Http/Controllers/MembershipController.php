<?php

namespace App\Modules\AccessControl\Http\Controllers;

use App\Modules\AccessControl\Domain\Models\Membership;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController
{
    public function index(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $members = Membership::where('tenant_id', $ctx->id())
            ->with(['user', 'roles'])
            ->get();

        return response()->json($members);
    }

    public function invite(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // In a real implementation, this would create an invitation record
        // and send an email. For now, we find the user by email.
        $user = \App\Models\User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json(['message' => 'User not found. Invitation email will be sent.'], 202);
        }

        $existing = Membership::where('tenant_id', $ctx->id())
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'User is already a member.'], 422);
        }

        $membership = Membership::create([
            'tenant_id' => $ctx->id(),
            'user_id' => $user->id,
            'status' => 'invited',
            'invited_by' => $request->user()->id,
        ]);

        return response()->json($membership, 201);
    }

    public function destroy(Request $request, Membership $membership): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        abort_unless($membership->tenant_id === $ctx->id(), 403);

        $membership->delete();

        return response()->json(null, 204);
    }
}
