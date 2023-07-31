<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\StoreTagsService;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        try {
            return response()->json(
                Post::where('user_id', request()->user()->id)
                    ->orderByDesc('is_pinned')
                    ->with('user', 'tags')
                    ->get()
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function show($id)
    {
        try {
            return response()->json(
                Post::where('user_id', request()->user()->id)
                    ->with('user', 'tags')
                    ->findOrfail($id)
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\NotFoundException(__('Not found.'));
        }
    }

    public function store(StorePostRequest $request, StoreTagsService $storeTagsService)
    {
        try {
            $post = Post::create([
                'title' => $request->title,
                'body' => $request->body,
                'is_pinned' => $request->is_pinned ?? false,
                'cover_image' => $request->file('cover_image')
                    ->storeAs('covers', now()->timestamp . '_' . $request->file('cover_image')->getClientOriginalName()),
                'user_id' => $request->user()->id,
            ]);

            if ($request->tagNames) {
                $tags = $storeTagsService->store($request->tagNames);
                $post->tags()->attach($tags);
            }
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json($post, 201);
    }

    public function update(UpdatePostRequest $request, $id, StoreTagsService $storeTagsService)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            if ($request->hasFile('cover_image')) {
                Storage::delete($post->cover_image);
            }

            $post->update([
                'title' => $request->title ?? $post->title,
                'body' => $request->body ?? $post->body,
                'is_pinned' => $request->is_pinned ?? $post->is_pinned,
                'cover_image' => $request->file('cover_image') ? $request->file('cover_image')
                    ->storeAs('covers', now()->timestamp . '_' . $request->file('cover_image')->getClientOriginalName())
                    : $post->cover_image,
            ]);

            $tags = $storeTagsService->store($request->tagNames);

            $post->tags()->sync($tags);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Updated successfully.'),
            ]
        );
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $post->delete();
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Deleted successfully.'),
            ]
        );
    }

    public function pin($id)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $post->update([
                'is_pinned' => true,
            ]);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Pinned successfully.'),
            ]
        );
    }

    public function unpin($id)
    {
        $post = Post::find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $post->update([
                'is_pinned' => false,
            ]);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Unpinned successfully.'),
            ]
        );
    }

    public function indexTrashed()
    {
        try {
            return response()->json(
                Post::onlyTrashed()
                    ->where('user_id', request()->user()->id)
                    ->orderByDesc('is_pinned')
                    ->with('user', 'tags')
                    ->get()
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $post->restore();
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Restored successfully.'),
            ]
        );
    }

    public function delete($id)
    {
        $post = Post::withTrashed()->find($id);
        if (!$post) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $post->forceDelete();
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }

        return response()->json(
            [
                'message' => __('Permanently deleted successfully.'),
            ]
        );
    }
}
