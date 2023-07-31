<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\PostTagsController;
use App\Http\Controllers\Api\StatsController;

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

Route::group(
    ['prefix' => 'v1'],
    function () {
        Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/stats', [StatsController::class, 'index']);

        // tags routes
        Route::group([
            'middleware' => ['auth:sanctum', 'verified'],
        ], function () {
            Route::get('/tags', [TagController::class, 'index']);
            Route::get('/tags/{id}', [TagController::class, 'show']);
            Route::post('/tags', [TagController::class, 'store']);
            Route::patch('/tags/{id}', [TagController::class, 'update']);
            Route::delete('/tags/{id}', [TagController::class, 'destroy']);
        });

        // post-tags routes
        Route::group([
            'middleware' => ['auth:sanctum', 'verified', 'is_owner:post'],
        ], function () {
            Route::get('posts/{id}/tags', [PostTagsController::class, 'index']);
            Route::post('posts/{id}/tags', [PostTagsController::class, 'store']);
            Route::patch('posts/{id}/tags/{tag_id}', [PostTagsController::class, 'update']);
            Route::delete('posts/{id}/tags/{tag_id}', [PostTagsController::class, 'destroy']);
        });

        // posts routes
        Route::group([
            'middleware' => ['auth:sanctum', 'verified'],
            'prefix' => 'u'
        ], function () {
            Route::get('posts', [PostController::class, 'index']);
            Route::get('posts/trashed', [PostController::class, 'indexTrashed']);
            Route::middleware(['is_owner:post'])
                ->get('posts/{id}', [PostController::class, 'show']);
            Route::post('posts', [PostController::class, 'store']);
            Route::middleware(['is_owner:post'])
                ->patch('posts/{id}', [PostController::class, 'update']);
            Route::middleware(['is_owner:post'])
                ->patch('posts/{id}/pin', [PostController::class, 'pin']);
            Route::middleware(['is_owner:post'])
                ->patch('posts/{id}/unpin', [PostController::class, 'unpin']);
            Route::middleware(['is_owner:post'])
                ->delete('posts/{id}', [PostController::class, 'destroy']);
            Route::middleware(['is_owner:post'])
                ->patch('posts/{id}/restore', [PostController::class, 'restore']);
            Route::middleware(['is_owner:post'])
                ->delete('posts/{id}/force-delete', [PostController::class, 'delete']);
        });

        require __DIR__ . '/auth.php';
    }
);
