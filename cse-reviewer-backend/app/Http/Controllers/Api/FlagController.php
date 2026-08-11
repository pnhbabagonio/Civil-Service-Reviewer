<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FlagController extends Controller
{
    public function index(Request $request)
    {
        $flags = $request->user()
            ->flags()
            ->get()
            ->map(function ($question) {
                return [
                    'question_id' => $question->id,
                    'reason' => $question->pivot->reason,
                    'created_at' => $question->pivot->created_at,
                ];
            });

        return response()->json(['data' => $flags]);
    }

    public function store(Request $request, string $questionId)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:50'],
        ]);

        $request->user()->flags()->syncWithoutDetaching([
            $questionId => ['reason' => $validated['reason']],
        ]);

        return response()->json(['message' => 'Flagged'], 201);
    }

    public function destroy(Request $request, string $questionId)
    {
        $request->user()->flags()->detach($questionId);

        return response()->json(['message' => 'Flag removed']);
    }
}