<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CategoryTranslationController;
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
