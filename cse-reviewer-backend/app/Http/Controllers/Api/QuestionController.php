<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\PassageGroup;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('categories')) {
            $query->whereIn('category', explode(',', $request->categories));
        }

        if ($request->has('subcategory')) {
            $query->where('subcategory', $request->subcategory);
        }

        if ($request->has('difficulty') && $request->difficulty !== 'all') {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->has('ids')) {
            $query->whereIn('id', explode(',', $request->ids));
        }

        $questions = $query->get();

        return response()->json(['data' => $questions]);
    }

    public function show(string $id)
    {
        $question = Question::findOrFail($id);

        return response()->json(['data' => $question]);
    }

    public function passageGroups(Request $request)
    {
        $query = PassageGroup::with('questions');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('categories')) {
            $query->whereIn('category', explode(',', $request->categories));
        }

        if ($request->has('difficulty') && $request->difficulty !== 'all') {
            $query->where('difficulty', $request->difficulty);
        }

        $groups = $query->get();

        return response()->json(['data' => $groups]);
    }

    // Admin methods
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'string', 'max:20', 'unique:questions'],
            'category' => ['required', 'string', 'max:50'],
            'subcategory' => ['required', 'string', 'max:100'],
            'difficulty' => ['required', 'in:easy,average,difficult'],
            'question_text' => ['required', 'string'],
            'choices' => ['required', 'array'],
            'answer' => ['required', 'string', 'size:1'],
            'explanation' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'has_image' => ['boolean'],
            'passage_group_id' => ['nullable', 'string', 'exists:passage_groups,id'],
        ]);

        $question = Question::create($validated);

        return response()->json(['data' => $question], 201);
    }

    public function update(Request $request, string $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'category' => ['sometimes', 'string', 'max:50'],
            'subcategory' => ['sometimes', 'string', 'max:100'],
            'difficulty' => ['sometimes', 'in:easy,average,difficult'],
            'question_text' => ['sometimes', 'string'],
            'choices' => ['sometimes', 'array'],
            'answer' => ['sometimes', 'string', 'size:1'],
            'explanation' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'has_image' => ['boolean'],
            'passage_group_id' => ['nullable', 'string', 'exists:passage_groups,id'],
        ]);

        $validated['version'] = $question->version + 1;
        $question->update($validated);

        return response()->json(['data' => $question]);
    }

    public function destroy(string $id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Question deleted']);
    }
}