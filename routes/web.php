<?php

use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Profile\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermission\PermissionController;
use App\Http\Controllers\RolePermission\RoleController;
use App\Http\Controllers\RolePermission\RolePermissionController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserPackageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Roles Routes
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Permissions Routes
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');


    Route::prefix('role-permissions')->name('role-permissions.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('index');
        Route::get('/create', [RolePermissionController::class, 'create'])->name('create');
        Route::post('/', [RolePermissionController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RolePermissionController::class, 'edit'])->name('edit'); // Add this line
        Route::put('/{role}', [RolePermissionController::class, 'update'])->name('update'); // Add this line
        Route::delete('/{role}', [RolePermissionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show'); // Add this line
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::post('/bank-details', [DocumentController::class, 'storeBankDetails'])->name('bank-details.store');
    Route::get('/documents/download/{id}/{type}', [DocumentController::class, 'download'])->name('documents.download');

});

// Route::middleware(['auth'])->group(function () {
//     Route::get('/admin/packages/create', [PackageController::class, 'create'])->name('admin.packages.create');
//     Route::post('/admin/packages', [PackageController::class, 'store'])->name('admin.packages.store');
//     Route::get('/admin/cities/{country}', [PackageController::class, 'getCities']);
//     Route::get('/admin/areas/{city}', [PackageController::class, 'getAreas']);
// });


Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('packages', PackageController::class);
    });
});

Route::prefix('user')->group(function () {
    // List of packages
    Route::get('/packages', [UserPackageController::class, 'index'])->name('user.packages.index');

    // Show a specific package
    Route::get('/packages/{package}', [UserPackageController::class, 'show'])->name('user.packages.show');
});

require __DIR__.'/auth.php';
