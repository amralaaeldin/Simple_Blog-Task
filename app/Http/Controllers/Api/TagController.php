<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        try {
            return response()->json(Tag::select('id', 'name')->get());
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while retrieving.'));
        }
    }

    public function show($id)
    {
        try {
            return response()->json(
                Tag::findOrfail($id)
            );
        } catch (\Exception $e) {
            throw new \App\Exceptions\NotFoundException(__('Not found.'));
        }
    }

    public function store(StoreTagRequest $request)
    {
        try {
            return response()->json(Tag::create([
                'name' => $request->name
            ]), 201);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while creating.'));
        }
    }

    public function update($id, UpdateTagRequest $request)
    {
        $tag = Tag::find($id);
        if (!$tag) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $tag->update([
                'name' => $request->name ?? $tag->name
            ]);
            return response()->json($tag);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while updating.'));
        }
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        if (!$tag) throw new \App\Exceptions\NotFoundException(__('Not found.'));

        try {
            $tag->delete();
            return response()->json($tag);
        } catch (\Exception $e) {
            throw new \App\Exceptions\QueryDBException(__('An error occurred while deleting.'));
        }
    }
}
