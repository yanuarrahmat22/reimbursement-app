<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\UserMenuAuthorizationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Auth::routes();

Route::group(['middleware' => ['auth']], function () {
  // Internal Routes
  Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
  Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

  // Route Menu
  Route::resource("menu", MenuController::class);
  Route::post('/menu/sort', [MenuController::class, 'sort']);
  Route::get('/menu/delete/{id}', [MenuController::class, 'destroy'])->name('menu-delete');

  // Route Roles dan Authorization
  // Route::get('/role/', [RoleController::class, 'index']);
  Route::get('/role/delete/{id}', [RoleController::class, 'destroy']);
  Route::get('/role/usermenuauthorization/{id}', [UserMenuAuthorizationController::class, 'index'])->name('role.usermenuauthorization');
  Route::post('/role/usermenuauthorization/store', [UserMenuAuthorizationController::class, 'store'])->name('role.usermenuauthorization.store');
  Route::resource("role", RoleController::class);

  // Route User
  Route::resource("user", UserController::class);
  Route::get('/user-destroy/{id}', [UserController::class, 'destroy']);
  Route::get('/user-reset/{id}', [UserController::class, 'ResetPass']);
  Route::get('/user-reset-status/{id}/{val}', [UserController::class, 'ChangeStatus']);
  Route::post('/user/get-users-by-select2', [UserController::class, 'getUsersBySelect2'])->name('getUsersBySelect2');
  Route::post('/user/get-admin-hub-by-select2', [UserController::class, 'getAdminHubBySelect2'])->name('get-admin-hub-by-select2');

  // Route User Account
  Route::get('/user-profile-account', [UserAccountController::class, 'userProfileAccount']);
  Route::post('/user-profile-account/{id}', [UserAccountController::class, 'storeUserProfileAccount']);
  Route::get('/user-password-account', [UserAccountController::class, 'userPasswordAccount']);
  Route::post('/user-password-account/{id}', [UserAccountController::class, 'storeUserPasswordAccount']);

  // Route Data Reimbursement
  Route::get('/reimbursement/checking/{id}', [ReimbursementController::class, 'checkingApplication'])->name('reimbursement.checking');
  Route::post('/reimbursement/store-checking/{id}', [ReimbursementController::class, 'storeCheckingApplication'])->name('reimbursement.store-checking');

  Route::get('/reimbursement/payment-confirmation/{id}', [ReimbursementController::class, 'paymentConfirmation']);

  Route::get('/reimbursement/delete/{id}', [ReimbursementController::class, 'destroy']);

  Route::resource("reimbursement", ReimbursementController::class);
});
