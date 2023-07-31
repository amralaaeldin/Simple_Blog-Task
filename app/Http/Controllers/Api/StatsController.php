<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index()
    {
        if (Cache::has('stats')) {
            return Cache::get('stats');
        }

        $stats = [
            'total_posts' => Post::count(),
            'total_users' => User::count(),
            'total_users_with_no_posts' => User::doesntHave('posts')->count(),
        ];

        Cache::put('stats', $stats, now()->addMinutes(10));

        return response()->json([
            'data' => $stats
        ]);
    }
}
