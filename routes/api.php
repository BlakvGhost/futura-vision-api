<?php

use App\Http\Controllers\Home;
use App\Http\Controllers\Setting;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AboutController;
use App\Http\Controllers\API\ForumController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\ExploitController;
use App\Http\Controllers\API\GalleryController;
use App\Http\Controllers\API\PartnerController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\ReactionController;
use App\Http\Controllers\API\TechnologyController;


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


Route::middleware('guest')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('setting', Setting::class)->only(['index', 'store']);
    Route::get('current-user', [AuthController::class, 'user']);
    Route::put('user/{user}', [UserController::class, 'update']);
    Route::apiResource('post', PostController::class);
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('exploit', ExploitController::class);
    Route::apiResource('project', ProjectController::class);
    Route::apiResource('service', ServiceController::class);
    Route::apiResource('technology', TechnologyController::class);
    Route::apiResource('customer', CustomerController::class);
    Route::apiResource('team', TeamController::class);
    Route::apiResource('partner', PartnerController::class);
    Route::apiResource('contact', ContactController::class);
    Route::apiResource('blog', BlogController::class);
    Route::apiResource('forum', ForumController::class);
    Route::post('comment', [ReactionController::class, 'comment']);
    Route::post('react', [ReactionController::class, 'react']);
    Route::post('seen', [ReactionController::class, 'seen']);
    Route::get('home', [Home::class, 'index']);
    Route::get('blog-detail/{blog}', [Home::class, 'showBlog']);
    Route::get('forum-detail/{forum}', [Home::class, 'showForum']);
    Route::get('blog-info', [Home::class, 'blog']);
    Route::get('forum-info', [Home::class, 'forum']);
    Route::get('blog-forum', [Home::class, 'blogForum']);
    Route::get('filter/category/{model}/{id}', [Home::class, 'filterBasedCat']);
    Route::get('filter/word/{model}/{q}', [Home::class, 'filterBasedWords']);
});
