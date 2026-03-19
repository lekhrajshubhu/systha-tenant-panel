<?php

namespace Systha\tenantpanel\Http\Controllers\Api\Tenant;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TenantInquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant');
        if (!$tenant) {
            return response()->json(['message' => 'Tenant context not found.'], 404);
        }

        dd($request->all());
    }
}
