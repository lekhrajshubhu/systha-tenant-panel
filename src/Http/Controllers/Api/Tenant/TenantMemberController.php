<?php

namespace Systha\tenantpanel\Http\Controllers\Api\Tenant;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\TenantMember;

class TenantMemberController extends Controller
{
    use HandlesApiResources;

    public function index(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant');
        if (!$tenant) {
            return response()->json(['message' => 'Tenant context not found.'], 404);
        }

        $query = TenantMember::query()
            ->where('tenant_id', $tenant->id)
            ->with('role')
            ->orderByDesc('id');

        $search = $this->search($request);
        if ($search !== null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhereHas('person', function ($q) use ($search): void {
                        $q->where('phone', 'like', '%' . $search . '%')
                            ->orWhere('mobile', 'like', '%' . $search . '%');
                    });
            });
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();
        $paginator->through(function (TenantMember $member): array {
            $name = trim($member->first_name . ' ' . $member->last_name);

            return [
                'id' => $member->id,
                'name' => $name !== '' ? $name : null,
                'email' => $member->email,
                'role' => $member->role?->name,
                'status' => $member->status,
                'last_login_at' => optional($member->last_login_at)->toDateTimeString(),
            ];
        });

        return $this->paginatedResponse($paginator);
    }

    public function show(Request $request, $tenantId, $id): JsonResponse
    {
        $tenant = $request->get('tenant');
        if (!$tenant) {
            return response()->json(['message' => 'Tenant context not found.'], 404);
        }

        $member = TenantMember::query()
            ->where('tenant_id', $tenant->id)
            ->where('id', $id)
            ->with('role')
            ->first();

        if (!$member) {
            return response()->json(['message' => 'Member not found.'], 404);
        }

        $name = trim($member->first_name . ' ' . $member->last_name);

        return response()->json([
            'data' => [
                'id' => $member->id,
                'name' => $name !== '' ? $name : null,
                'email' => $member->email,
                'role' => $member->role?->name,
                'status' => $member->status,
                'last_login_at' => optional($member->last_login_at)->toDateTimeString(),
            ],
        ]);
    }
}
