<?php

namespace App\Modules\Helpdesk\Http\Controllers;

use App\Modules\Helpdesk\Domain\Events\TicketOpened;
use App\Modules\Helpdesk\Domain\Models\Ticket;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController
{
    public function index(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $tickets = Ticket::tenant($ctx->id())
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('priority'), fn ($q, $p) => $q->where('priority', $p))
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json($tickets);
    }

    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        $this->authorizeTenantAccess($ticket);

        return response()->json($ticket->load(['reporter', 'assignee', 'comments']));
    }

    public function store(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:low,medium,high,urgent',
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $ctx->id(),
            'subject' => $validated['subject'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'status' => 'open',
            'reporter_id' => $request->user()->id,
            'created_by' => $request->user()->id,
        ]);

        TicketOpened::dispatch($ctx->id(), $ticket->id);

        return response()->json($ticket, 201);
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $this->authorizeTenantAccess($ticket);

        $validated = $request->validate([
            'status' => 'in:open,in_progress,resolved,closed',
            'priority' => 'in:low,medium,high,urgent',
            'assignee_id' => 'nullable|integer|exists:users,id',
        ]);

        $ticket->update($validated);

        return response()->json($ticket);
    }

    private function authorizeTenantAccess(Ticket $ticket): void
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        abort_unless($ticket->tenant_id === $ctx->id(), 403);
    }
}
