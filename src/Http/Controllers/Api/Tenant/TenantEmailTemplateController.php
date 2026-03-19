<?php

namespace Systha\tenantpanel\Http\Controllers\Api\Tenant;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Systha\Core\Http\Concerns\HandlesApiResources;
use Systha\Core\Models\EmailTemplate;


class TenantEmailTemplateController extends Controller
{
    use HandlesApiResources;

    public function index(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant');
        if (!$tenant) {
            return response()->json(['message' => 'Tenant context not found.'], 404);
        }

        $query = EmailTemplate::query()
            ->where(function ($builder) use ($tenant): void {
                $builder->whereNull('tenant_id')
                    ->orWhere('tenant_id', $tenant->id);
            })
            ->orderByDesc('id');

        $search = $this->search($request);
        if ($search !== null) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $paginator = $query->paginate($this->perPage($request))->withQueryString();
        $paginator->through(function (EmailTemplate $template): array {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'subject' => $template->subject,
                'active' => $template->is_active
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

        $template = EmailTemplate::query()
            ->where(function ($builder) use ($tenant): void {
                $builder->whereNull('tenant_id')
                    ->orWhere('tenant_id', $tenant->id);
            })
            ->where('id', $id)
            ->first();

        if (!$template) {
            return response()->json(['message' => 'Email template not found.'], 404);
        }

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'subject' => $template->subject,
            'body' => $template->body,
            'active' => (bool) $template->is_active,
            'code' => $template->code,
            'meta' => $template->meta,
            'updated_at' => optional($template->updated_at)->toDateTimeString(),
        ]);
    }

    public function update(Request $request, $tenantId, $id): JsonResponse
    {
        $tenant = $request->get('tenant');
        if (!$tenant) {
            return response()->json(['message' => 'Tenant context not found.'], 404);
        }

        $template = EmailTemplate::query()
            ->where(function ($builder) use ($tenant): void {
                $builder->whereNull('tenant_id')
                    ->orWhere('tenant_id', $tenant->id);
            })
            ->where('id', $id)
            ->first();

        if (!$template) {
            return response()->json(['message' => 'Email template not found.'], 404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'active' => ['required', 'boolean'],
        ]);

        $template->name = $data['name'];
        $template->subject = $data['subject'];
        $template->body = $data['body'] ?? '';
        $template->is_active = (bool) $data['active'];
        $template->save();

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'subject' => $template->subject,
            'body' => $template->body,
            'active' => (bool) $template->is_active,
            'code' => $template->code,
            'meta' => $template->meta,
            'updated_at' => optional($template->updated_at)->toDateTimeString(),
        ]);
    }
}
