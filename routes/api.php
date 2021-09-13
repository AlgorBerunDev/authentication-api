<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CategoryTranslationController;
use App\Http\Controllers\Api\AccountController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::get('/limits', [AuthController::class, 'limits']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/send_confirmation', [AuthController::class, 'sendConfirmation'])->middleware('apiauth');
    Route::post('/confirmation', [AuthController::class, 'confirmation'])->middleware('apiauth');
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function ($router) {
    Route::post('/refresh', [UserController::class, 'refresh']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/me', [UserController::class, 'profile'])->middleware('apiauth');
    Route::patch('/session_max_count', [UserController::class, 'changeSessionMaxCount'])->middleware('apiauth');
});

Route::group([
    'middleware' => 'apiauth',
    'prefix' => 'sessions'
], function ($router) {
    Route::post('/', [SessionController::class, 'getSessions']);
    Route::delete('/', [SessionController::class, 'removeSessions']);
    Route::patch('/', [SessionController::class, 'updateFcmToken']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'categories'
], function ($router) {
    Route::get('/', [CategoryController::class, 'getAll']);
    Route::get('/base_parents', [CategoryController::class, 'getSuperParent']);
    Route::get('/{id}', [CategoryController::class, 'getById']);
    Route::get('/childs/{parent_id}', [CategoryController::class, 'getChilds']);
    Route::post('/', [CategoryController::class, 'create']);
    Route::post('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'remove']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'category_translations'
], function ($router) {
    Route::get('/', [CategoryTranslationController::class, 'getAll']);
    Route::get('/{parent_id}', [CategoryTranslationController::class, 'getByCategoryId']);
    Route::post('/', [CategoryTranslationController::class, 'create']);
    Route::patch('/{id}', [CategoryTranslationController::class, 'update']);
    Route::delete('/{id}', [CategoryTranslationController::class, 'remove']);
});

Route::group([
    'middleware' => 'apiauth',
    'prefix' => 'accounts'
], function ($router) {

    Route::get('/phones', [AccountController::class, 'getPhones']);
    Route::get('/phones/{account_id}', [AccountController::class, 'getPhonesByAccountId']);
    Route::get('/phone/{id}', [AccountController::class, 'getPhoneById']);
    Route::post('/phones', [AccountController::class, 'addPhone']);
    Route::patch('/phones/{id}', [AccountController::class, 'updatePhone']);
    Route::delete('/phones/{id}', [AccountController::class, 'deletePhone']);

    Route::get('/addresses', [AccountController::class, 'getAddresses']);
    Route::get('/addresses/{account_id}', [AccountController::class, 'getAddressesByAccountId']);
    Route::get('/address/{id}', [AccountController::class, 'getAddressById']);
    Route::post('/addresses', [AccountController::class, 'addAddress']);
    Route::patch('/addresses/{id}', [AccountController::class, 'updateAddress']);
    Route::delete('/addresses/{id}', [AccountController::class, 'deleteAddress']);

    Route::get('/schedules/{account_id}', [AccountController::class, 'getScheduleById']);
    Route::post('/schedules', [AccountController::class, 'addSchedule']);
    Route::patch('/schedules/{id}', [AccountController::class, 'updateSchedule']);
    Route::delete('/schedules/{id}', [AccountController::class, 'deleteSchedule']);

    Route::get('/users/{account_id}', [AccountController::class, 'getAccountUsers']);
    Route::post('/user', [AccountController::class, 'addUser']);
    Route::patch('/user/{id}', [AccountController::class, 'updateUser']);
    Route::delete('/user/{id}', [AccountController::class, 'removeUser']);

    Route::get('/', [AccountController::class, 'getAccounts']);
    Route::get('/{id}', [AccountController::class, 'getAccountById']);
    Route::post('/', [AccountController::class, 'addAccount']);
    Route::post('/update/{id}', [AccountController::class, 'updateAccount']);
    Route::delete('/{id}', [AccountController::class, 'deleteAccount']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'search'
], function ($router) {
    Route::get('/users', [AccountController::class, 'searchUser']);
    Route::get('/products', [AccountController::class, 'searchProduct']);
    Route::get('/category', [AccountController::class, 'searchCategory']);
    Route::get('/accounts', [AccountController::class, 'searchAccounts']);
    Route::get('/smart', [AccountController::class, 'searchAccounts']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'product_properties'
], function ($router) {

});

Route::group([
    'middleware' => 'api',
    'prefix' => 'products'
], function ($router) {

});

Route::group([
    'middleware' => 'api',
    'prefix' => 'messages'
], function ($router) {

});

Route::group([
    'middleware' => 'api',
    'prefix' => 'subscribe'
], function ($router) {

});
