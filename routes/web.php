<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\BeneficiaryRequestController as AdminBeneficiaryRequestController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Agent\AssignedRequestController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\Public\BeneficiaryRequestController;
use App\Http\Controllers\Public\DonationController;
use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/request-gift', [BeneficiaryRequestController::class, 'create'])->name('public.request.create');
Route::post('/request-gift', [BeneficiaryRequestController::class, 'store'])->middleware('throttle:10,1')->name('public.request.store');
Route::get('/request-success/{code}', [BeneficiaryRequestController::class, 'success'])->name('public.request.success');

Route::get('/donate', [DonationController::class, 'create'])->name('public.donation.create');
Route::post('/donate', [DonationController::class, 'store'])->middleware('throttle:10,1')->name('public.donation.store');
Route::get('/donation-success/{code}', [DonationController::class, 'success'])->name('public.donation.success');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/pledge', [PledgeController::class, 'show'])->name('pledge.show');
    Route::post('/pledge/accept', [PledgeController::class, 'accept'])->name('pledge.accept');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin', 'pledge.accepted'])->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('agents', AgentController::class)->parameters(['agents' => 'agent']);

    // Super admin only routes
    Route::resource('areas', AreaController::class)->middleware('global.admin');
    Route::resource('admin-users', AdminUserController::class)->parameters(['admin-users' => 'adminUser'])->middleware('global.admin');

    // Beneficiary requests
    Route::get('/beneficiary-requests', [AdminBeneficiaryRequestController::class, 'index'])->name('beneficiary-requests.index');
    Route::get('/beneficiary-requests/{beneficiaryRequest}', [AdminBeneficiaryRequestController::class, 'show'])->name('beneficiary-requests.show');
    Route::post('/beneficiary-requests/{beneficiaryRequest}/approve', [AdminBeneficiaryRequestController::class, 'approve'])->name('beneficiary-requests.approve');
    Route::post('/beneficiary-requests/{beneficiaryRequest}/assign', [AdminBeneficiaryRequestController::class, 'assign'])->name('beneficiary-requests.assign');
    Route::post('/beneficiary-requests/{beneficiaryRequest}/status', [AdminBeneficiaryRequestController::class, 'status'])->name('beneficiary-requests.status');

    // Donations
    Route::get('/donations', [AdminDonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/{donation}', [AdminDonationController::class, 'show'])->name('donations.show');
    Route::post('/donations/{donation}/confirm', [AdminDonationController::class, 'confirm'])->name('donations.confirm');
    Route::post('/donations/{donation}/receive', [AdminDonationController::class, 'receive'])->name('donations.receive');
    Route::post('/donations/{donation}/allocate', [AdminDonationController::class, 'allocate'])->name('donations.allocate');
    Route::post('/donations/{donation}/status', [AdminDonationController::class, 'status'])->name('donations.status');

    // Delivery bonds/receipts - super admin only
    Route::post('/donations/{donation}/delivery-bond', [AdminDonationController::class, 'deliveryBond'])->middleware('global.admin')->name('donations.delivery-bond');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});

Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:agent', 'pledge.accepted'])->group(function () {
    Route::get('/dashboard', AgentDashboardController::class)->name('dashboard');
    Route::get('/requests', [AssignedRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{beneficiaryRequest}', [AssignedRequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{beneficiaryRequest}/status', [AssignedRequestController::class, 'status'])->name('requests.status');
});
