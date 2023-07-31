<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Services\StoreTagsService;
use App\Models\Tag;
use App\Models\Post;

class PostTagsController extends Controller
{
    public function index($id)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            return response()->json(
                $post->tags
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while creating.'));
        }
    }

    public function store($id, StoreTagRequest $request, StoreTagsService $storeTagsService)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $tag = Tag::create([
                'name' => $request->name
            ]);

            $post->tags()->attach($tag);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while creating.'));
        }

        return response()->json($tag, 201);
    }

    public function update($id, $tag_id, UpdateTagRequest $request, StoreTagsService $storeTagsService)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        $tag = Tag::find($tag_id);
        if (!$tag) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $tag = Tag::create([
                'name' => $request->name
            ]);

            $post->tags()->detach($tag_id);
            $post->tags()->attach($tag);

            return response()->json($tag);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while updating.'));
        }
    }

    public function destroy($id, $tag_id)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        $tag = Tag::find($tag_id);
        if (!$tag) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $post->tags()->detach($tag_id);
            return response()->json($tag);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while deleting.'));
        }
    }
}
