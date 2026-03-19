<?php

use Illuminate\Support\Facades\Route;
use Systha\Core\Http\Middleware\TenantContextFromToken;
use Systha\Core\Http\Middleware\TenantJwtAuth;
use Systha\tenantpanel\Http\Controllers\Api\Tenant\TenantAuthController;
use Systha\tenantpanel\Http\Controllers\Api\Tenant\TenantCustomerController;
use Systha\tenantpanel\Http\Controllers\Api\Tenant\TenantEmailTemplateController;
use Systha\tenantpanel\Http\Controllers\Api\Tenant\TenantInquiryController;
use Systha\tenantpanel\Http\Controllers\Api\Tenant\TenantMemberController;
use Systha\tenantpanel\Http\Controllers\Api\Tenant\TenantPaymentCredentialController;
use Systha\tenantpanel\Http\Controllers\Api\Tenant\TenantServiceController;
use Systha\tenantpanel\Http\Controllers\Api\Tenant\TenantServiceQuestionController;

/*
|--------------------------------------------------------------------------
| Tenant API Routes (Managed by Tenantpanel Package)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function (): void {
    Route::post('tenant/{tenant}/login/password', [TenantAuthController::class, 'passwordLogin']);

    Route::middleware(TenantJwtAuth::class)->group(function (): void {
        Route::post('tenant/logout', [TenantAuthController::class, 'logout']);
        Route::post('tenant/refresh', [TenantAuthController::class, 'refresh']);

        Route::middleware(TenantContextFromToken::class)->prefix('tenant/{tenant}')->group(function (): void {
            Route::get('me', [TenantAuthController::class, 'me']);
            Route::get('customers/all', [TenantCustomerController::class, 'listAll']);
            Route::get('customers', [TenantCustomerController::class, 'index']);
            Route::get('customers/{id}', [TenantCustomerController::class, 'show']);
            Route::get('email-templates', [TenantEmailTemplateController::class, 'index']);
            Route::get('email-templates/{id}', [TenantEmailTemplateController::class, 'show']);
            Route::put('email-templates/{id}', [TenantEmailTemplateController::class, 'update']);
            Route::post('inquiries', [TenantInquiryController::class, 'store']);
            Route::get('members', [TenantMemberController::class, 'index']);
            Route::get('members/{id}', [TenantMemberController::class, 'show']);
            Route::get('payment-credentials', [TenantPaymentCredentialController::class, 'index']);
            Route::get('services', [TenantServiceController::class, 'index']);
            Route::get('services/{id}', [TenantServiceController::class, 'show']);
            Route::get('services/{id}/questions', [TenantServiceQuestionController::class, 'serviceItemQuestions']);
            // Route::get('service-groups/{id}/questions', [TenantServiceQuestionController::class, 'serviceGroupItemQuestions']);
        });
    });
});
