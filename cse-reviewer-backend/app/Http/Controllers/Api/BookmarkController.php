<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $bookmarks = $request->user()
            ->bookmarks()
            ->get()
            ->pluck('id');

        return response()->json(['data' => $bookmarks]);
    }

    public function store(Request $request, string $questionId)
    {
        $request->user()->bookmarks()->syncWithoutDetaching([$questionId]);

        return response()->json(['message' => 'Bookmarked'], 201);
    }

    public function destroy(Request $request, string $questionId)
    {
        $request->user()->bookmarks()->detach($questionId);

        return response()->json(['message' => 'Bookmark removed']);
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['string'],
        ]);

        $request->user()->bookmarks()->sync($validated['question_ids']);

        return response()->json(['message' => 'Bookmarks synced']);
    }
}