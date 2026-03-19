<?php

namespace Systha\tenantpanel\Http\Controllers\Api\Tenant;



use App\Http\Controllers\Controller;
use Systha\Core\Models\TenantMember;
use Systha\Core\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class TenantAuthController extends Controller
{
    /**
     * Password login for a specific tenant.
     */
    public function passwordLogin(Request $request, string $tenantCode): JsonResponse
    {
        $request->validate([
            'login_identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. Resolve tenant
        $tenant = Tenant::where('code', $tenantCode)
            ->where('status', 'active')
            ->first();

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found or inactive'], 404);
        }

        // 2. Find member
        $member = TenantMember::where('tenant_id', $tenant->id)
            ->where('email', $request->login_identifier)
            ->where('status', 'active')
            ->where('is_active', true)
            ->with('role')
            ->first();

        if (!$member) {
            return response()->json(['error' => 'Invalid credentials or inactive membership'], 401);
        }

        // 3. Verify password
        if (!Hash::check($request->password, $member->password)) {
            return response()->json(['error' => 'Invalid password'], 401);
        }

        // 4. Generate JWT with custom claims
        $token = JWTAuth::claims([
            'tenant_id' => $tenant->id,
            'member_id' => $member->id,
            'tenant_code' => $tenant->code,
            'role' => $member->role?->name,
        ])->fromUser($member);

        // 5. Update last login
        $member->update([
            'last_login_at' => now(),
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => [
                'id' => $member->id,
                'role' => $member->role?->name,
                'name' => $member->first_name . ' ' . $member->last_name,
                'email' => $member->email,
                'tenant' => [
                    'name' => $tenant->name,
                    'code' => $tenant->code,
                ],
            ],
        ]);
    }

    /**
     * Log the account out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return response()->json([
            'access_token' => JWTAuth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => [
                'id' => $request->member->id,
                'role' => $request->role,
                'name' => $request->member->first_name . ' ' . $request->member->last_name,
                'email' => $request->member->email,
                'tenant' => [
                    'name' => $request->tenant->name,
                    'code' => $request->tenant->code,
                ],
            ],
        ]);
    }
}
