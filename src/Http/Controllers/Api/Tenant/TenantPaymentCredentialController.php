<?php

namespace Systha\tenantpanel\Http\Controllers\Api\Tenant;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\TenantPaymentCredential;

class TenantPaymentCredentialController extends Controller
{
    use HandlesApiResources;

    public function index(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant');
        if (!$tenant) {
            return response()->json(['message' => 'Tenant context not found.'], 404);
        }

        $query = TenantPaymentCredential::query()
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('id');

        $search = $this->search($request);
        if ($search !== null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();
        $paginator->through(function (TenantPaymentCredential $credential): array {
            return [
                'id' => $credential->id,
                'name' => $credential->name,
                'code' => $credential->code,
                'credentials' => $credential->credentials,
                'is_default' => (bool) $credential->is_default,
                'is_active' => (bool) $credential->is_active,
            ];
        });

        return $this->paginatedResponse($paginator);
    }
}
