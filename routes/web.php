<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Password Reset Routes
Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::group(['middleware' => 'auth'], function () {
    Route::get('password/change', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'show'])->name('password_change');
    Route::post('password/change', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])->name('password_update');

    Route::get('/', [\App\Http\Controllers\UsersController::class, 'search']);
    Route::get('home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('profile', [\App\Http\Controllers\HomeController::class, 'index'])->name('profile');
    Route::post('family-actions/{user}/set-father', [\App\Http\Controllers\FamilyActionsController::class, 'setFather'])->name('family-actions.set-father');
    Route::post('family-actions/{user}/set-mother', [\App\Http\Controllers\FamilyActionsController::class, 'setMother'])->name('family-actions.set-mother');
    Route::post('family-actions/{user}/add-child', [\App\Http\Controllers\FamilyActionsController::class, 'addChild'])->name('family-actions.add-child');
    Route::post('family-actions/{user}/add-wife', [\App\Http\Controllers\FamilyActionsController::class, 'addWife'])->name('family-actions.add-wife');
    Route::post('family-actions/{user}/add-husband', [\App\Http\Controllers\FamilyActionsController::class, 'addHusband'])->name('family-actions.add-husband');
    Route::post('family-actions/{user}/set-parent', [\App\Http\Controllers\FamilyActionsController::class, 'setParent'])->name('family-actions.set-parent');

    Route::get('profile-search', [\App\Http\Controllers\UsersController::class, 'search'])->name('users.search');
    Route::get('users/{user}', [\App\Http\Controllers\UsersController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [\App\Http\Controllers\UsersController::class, 'edit'])->name('users.edit');
    Route::patch('users/{user}', [\App\Http\Controllers\UsersController::class, 'update'])->name('users.update');
    Route::get('users/{user}/chart', [\App\Http\Controllers\UsersController::class, 'chart'])->name('users.chart');
    Route::get('users/{user}/tree', [\App\Http\Controllers\UsersController::class, 'tree'])->name('users.tree');
    Route::get('users/{user}/death', [\App\Http\Controllers\UsersController::class, 'death'])->name('users.death');
    Route::patch('users/{user}/photo-upload', [\App\Http\Controllers\UsersController::class, 'photoUpload'])->name('users.photo-upload');
    Route::delete('users/{user}', [\App\Http\Controllers\UsersController::class, 'destroy'])->name('users.destroy');

    Route::get('users/{user}/marriages', [\App\Http\Controllers\UserMarriagesController::class, 'index'])->name('users.marriages');

    Route::get('birthdays', [\App\Http\Controllers\BirthdayController::class, 'index'])->name('birthdays.index');

    /**
     * Couple/Marriages Routes
     */
    Route::get('couples/{couple}', [\App\Http\Controllers\CouplesController::class, 'show'])->name('couples.show');
    Route::get('couples/{couple}/edit', [\App\Http\Controllers\CouplesController::class, 'edit'])->name('couples.edit');
    Route::patch('couples/{couple}', [\App\Http\Controllers\CouplesController::class, 'update'])->name('couples.update');
});

