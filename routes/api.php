<?php

use App\Http\Controllers\Api\AdminMessageController;
use App\Http\Controllers\Api\ArchivedFileController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\CompanyPaymentController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DynamicEntryController;
use App\Http\Controllers\Api\DynamicModuleController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\SuggestionController;
use App\Http\Controllers\Api\SuperAdminController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/contact', [ContactMessageController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth (always accessible even if org suspended — needed for logout & status check)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // All org-bound routes require active organization
    Route::middleware('org.active')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Employees
    Route::apiResource('employees', EmployeeController::class);
    Route::patch('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus']);
    Route::get('/employees-export', [EmployeeController::class, 'exportCsv']);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::patch('/projects/{project}/progress', [ProjectController::class, 'updateProgress']);

    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);

    // Finance
    Route::get('/finance/summary', [FinanceController::class, 'summary']);
    Route::apiResource('finance', FinanceController::class)->only(['index', 'store', 'destroy']);

    // Company Payments
    Route::apiResource('company-payments', CompanyPaymentController::class)->only(['index', 'store', 'destroy']);
    Route::get('/payment-methods', [PaymentMethodController::class, 'listAll']);

    // Attendance
    Route::get('/attendance/stats', [AttendanceController::class, 'stats']);
    Route::get('/attendance/export', [AttendanceController::class, 'exportCsv']);
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
    Route::get('/attendance', [AttendanceController::class, 'index']);

    // Communication — Channels & Messages
    Route::apiResource('channels', ChannelController::class)->only(['index', 'store', 'destroy']);
    Route::get('/channels/{channel}/messages', [MessageController::class, 'index']);
    Route::post('/channels/{channel}/messages', [MessageController::class, 'store']);

    // Suggestions
    Route::apiResource('suggestions', SuggestionController::class)->only(['index', 'store']);
    Route::post('/suggestions/{suggestion}/vote', [SuggestionController::class, 'vote']);
    Route::patch('/suggestions/{suggestion}/status', [SuggestionController::class, 'updateStatus']);

    // Archives — Folders & Files
    Route::apiResource('folders', FolderController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('files', ArchivedFileController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::get('/files/{file}/download', [ArchivedFileController::class, 'download']);

    // Dynamic Builder — Modules & Entries
    Route::apiResource('modules', DynamicModuleController::class);
    Route::get('/modules/{module}/entries', [DynamicEntryController::class, 'index']);
    Route::post('/modules/{module}/entries', [DynamicEntryController::class, 'store']);
    Route::delete('/entries/{entry}', [DynamicEntryController::class, 'destroy']);
    Route::get('/modules/{module}/entries/export', [DynamicEntryController::class, 'exportCsv']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);

    // Organization Settings
    Route::get('/settings/organization', [SettingsController::class, 'getOrgSettings']);
    Route::put('/settings/organization', [SettingsController::class, 'updateOrgSettings']);
    Route::post('/settings/logo', [SettingsController::class, 'updateLogo']);
    Route::delete('/settings/logo', [SettingsController::class, 'removeLogo']);
    Route::put('/settings/credentials', [SettingsController::class, 'updateCredentials']);
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications']);

    // Admin Messages (contact admin from within the app)
    Route::post('/admin-messages', [AdminMessageController::class, 'store']);

    // Newsletters received by org
    Route::get('/newsletters', [NewsletterController::class, 'received']);

    }); // end org.active middleware group

    /*
    |--------------------------------------------------------------------------
    | Super Admin Routes (no org.active check — super admin has no org)
    |--------------------------------------------------------------------------
    */

    Route::prefix('super-admin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
        Route::get('/organizations', [SuperAdminController::class, 'organizations']);
        Route::post('/organizations', [SuperAdminController::class, 'storeOrganization']);
        Route::put('/organizations/{organization}', [SuperAdminController::class, 'updateOrganization']);
        Route::patch('/organizations/{organization}/toggle-status', [SuperAdminController::class, 'toggleOrganizationStatus']);
        Route::delete('/organizations/{organization}', [SuperAdminController::class, 'deleteOrganization']);
        Route::patch('/organizations/{organization}/modules', [SuperAdminController::class, 'toggleModule']);

        Route::get('/payments', [SuperAdminController::class, 'payments']);
        Route::patch('/payments/{payment}/validate', [SuperAdminController::class, 'validatePayment']);
        Route::patch('/payments/{payment}/reject', [SuperAdminController::class, 'rejectPayment']);

        Route::apiResource('payment-methods', PaymentMethodController::class)->only(['index', 'store', 'destroy']);

        Route::get('/contact-messages', [ContactMessageController::class, 'index']);
        Route::patch('/contact-messages/{message}/read', [ContactMessageController::class, 'markRead']);
        Route::post('/contact-messages/{message}/reply', [ContactMessageController::class, 'reply']);

        Route::get('/admin-messages', [AdminMessageController::class, 'index']);
        Route::patch('/admin-messages/{message}/read', [AdminMessageController::class, 'markRead']);

        Route::get('/newsletters', [NewsletterController::class, 'index']);
        Route::post('/newsletters', [NewsletterController::class, 'store']);
        Route::post('/newsletters/{newsletter}/send', [NewsletterController::class, 'send']);
        Route::delete('/newsletters/{newsletter}', [NewsletterController::class, 'destroy']);
    });
});
