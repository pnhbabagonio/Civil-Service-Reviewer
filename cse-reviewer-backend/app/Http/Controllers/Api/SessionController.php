<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\SessionAnswer;
use App\Models\UserStat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamSession::query();

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } elseif ($request->has('guest_token')) {
            $query->where('guest_token', $request->guest_token);
        } else {
            return response()->json(['data' => []]);
        }

        $sessions = $query->with('answers')
                          ->orderBy('completed_at', 'desc')
                          ->paginate(20);

        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:timed,practice,simulator'],
            'categories' => ['required', 'array'],
            'subcategory_keys' => ['nullable', 'array'],
            'difficulty' => ['required', 'string'],
            'total_questions' => ['required', 'integer'],
            'score' => ['required', 'integer'],
            'percentage' => ['required', 'numeric'],
            'passed' => ['required', 'boolean'],
            'time_taken_sec' => ['required', 'integer'],
            'answers' => ['required', 'array'],
            'answers.*.question_id' => ['required', 'string'],
            'answers.*.user_answer' => ['nullable', 'string'],
            'answers.*.correct_answer' => ['required', 'string'],
            'answers.*.is_correct' => ['required', 'boolean'],
        ]);

        $sessionData = [
            'mode' => $validated['mode'],
            'categories' => $validated['categories'],
            'subcategory_keys' => $validated['subcategory_keys'] ?? [],
            'difficulty' => $validated['difficulty'],
            'total_questions' => $validated['total_questions'],
            'score' => $validated['score'],
            'percentage' => $validated['percentage'],
            'passed' => $validated['passed'],
            'time_taken_sec' => $validated['time_taken_sec'],
            'completed_at' => now(),
        ];

        if ($request->user()) {
            $sessionData['user_id'] = $request->user()->id;
        } else {
            $sessionData['guest_token'] = $request->input('guest_token', Str::uuid()->toString());
        }

        $session = ExamSession::create($sessionData);

        // Save individual answers
        foreach ($validated['answers'] as $answer) {
            SessionAnswer::create([
                'session_id' => $session->id,
                'question_id' => $answer['question_id'],
                'user_answer' => $answer['user_answer'] ?? null,
                'correct_answer' => $answer['correct_answer'],
                'is_correct' => $answer['is_correct'],
            ]);
        }

        // Update user stats if authenticated
        if ($request->user()) {
            $this->updateUserStats($request->user());
        }

        return response()->json([
            'data' => $session->load('answers'),
            'guest_token' => $sessionData['guest_token'] ?? null,
        ], 201);
    }

    public function show(string $id)
    {
        $session = ExamSession::with('answers.question')->findOrFail($id);

        if ($session->user_id && request()->user() && $session->user_id !== request()->user()->id) {
            abort(403);
        }

        return response()->json(['data' => $session]);
    }

    private function updateUserStats($user)
    {
        $sessions = ExamSession::where('user_id', $user->id)->get();

        $totalQuestions = 0;
        $totalCorrect = 0;
        $examsTaken = $sessions->count();
        $bestSimulator = 0;
        $totalStudySec = 0;

        foreach ($sessions as $session) {
            $totalQuestions += $session->total_questions;
            $totalCorrect += $session->score;
            $totalStudySec += $session->time_taken_sec;

            if ($session->mode === 'simulator' && $session->score > $bestSimulator) {
                $bestSimulator = $session->score;
            }
        }

        $accuracy = $totalQuestions > 0 ? ($totalCorrect / $totalQuestions) * 100 : 0;

        UserStat::updateOrCreate(
            ['user_id' => $user->id],
            [
                'username' => $user->username,
                'total_questions' => $totalQuestions,
                'total_correct' => $totalCorrect,
                'overall_accuracy' => round($accuracy, 2),
                'exams_taken' => $examsTaken,
                'best_simulator_score' => $bestSimulator,
                'total_study_sec' => $totalStudySec,
                'last_activity_at' => now(),
            ]
        );
    }
}