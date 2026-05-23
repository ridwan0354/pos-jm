<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ── Customer Portal (Publik) ─────────────────────────────────────
Route::get('/portal',       fn () => redirect()->route('portal.login'))->name('portal');
Route::get('/portal/login', [CustomerPortalController::class, 'showLogin'])->name('portal.login');
Route::post('/portal/login',[CustomerPortalController::class, 'login'])->name('portal.login.post');
Route::post('/portal/logout',[CustomerPortalController::class, 'logout'])->name('portal.logout');

// ── Customer Portal (Butuh Login Portal) ────────────────────────
Route::middleware(\App\Http\Middleware\CustomerPortalAuth::class)->group(function () {
    Route::get('/portal/dashboard', [CustomerPortalController::class, 'dashboard'])->name('portal.dashboard');
    Route::post('/portal/topup',    [CustomerPortalController::class, 'requestTopup'])->name('portal.topup');
});

// ── Protected POS Routes ─────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::resource('orders', OrderController::class)->only(['index','create','store','show','destroy']);
    Route::patch('/orders/{order}/status',  [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/paid',    [OrderController::class, 'markPaid'])->name('orders.markPaid');

    // Customers
    Route::resource('customers', CustomerController::class)->only(['index','show','store','update','destroy']);
    Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::post('/customers/{customer}/topup', [CustomerController::class, 'topup'])->name('customers.topup');

    // Stocks
    Route::get('/stocks',                       [StockController::class, 'index'])->name('stocks.index');
    Route::post('/stocks',                      [StockController::class, 'store'])->name('stocks.store');
    Route::post('/stocks/{stock}/add',    [StockController::class, 'addStock'])->name('stocks.add');
    Route::post('/stocks/{stock}/reduce', [StockController::class, 'reduceStock'])->name('stocks.reduce');
    Route::delete('/stocks/{stock}',            [StockController::class, 'destroy'])->name('stocks.destroy');

    // Staff
    Route::resource('staff', StaffController::class)->only(['index','store','update','destroy']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Services (Detail Layanan)
    Route::get('/services',                          [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services',                         [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}',                [ServiceController::class, 'update'])->name('services.update');
    Route::patch('/services/{service}/toggle',       [ServiceController::class, 'toggleActive'])->name('services.toggle');
    Route::delete('/services/{service}',             [ServiceController::class, 'destroy'])->name('services.destroy');

    // Settings
    Route::get('/settings',  [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Profile (Breeze)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
