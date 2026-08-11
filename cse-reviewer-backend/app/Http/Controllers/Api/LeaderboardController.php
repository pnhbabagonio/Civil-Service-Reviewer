<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStat;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function accuracy(Request $request)
    {
        $leaderboard = UserStat::where('total_questions', '>=', 50)
            ->orderBy('overall_accuracy', 'desc')
            ->orderBy('total_questions', 'desc')
            ->limit(100)
            ->get(['username', 'overall_accuracy', 'total_questions']);

        return response()->json(['data' => $leaderboard]);
    }

    public function streak(Request $request)
    {
        $leaderboard = UserStat::orderBy('current_streak', 'desc')
            ->orderBy('longest_streak', 'desc')
            ->limit(100)
            ->get(['username', 'current_streak', 'longest_streak']);

        return response()->json(['data' => $leaderboard]);
    }

    public function simulator(Request $request)
    {
        $leaderboard = UserStat::where('best_simulator_score', '>', 0)
            ->orderBy('best_simulator_score', 'desc')
            ->limit(100)
            ->get(['username', 'best_simulator_score', 'exams_taken']);

        return response()->json(['data' => $leaderboard]);
    }

    public function weekly(Request $request)
    {
        $weekStart = now()->startOfWeek();

        $leaderboard = UserStat::where('last_activity_at', '>=', $weekStart)
            ->where('total_questions', '>=', 10)
            ->orderBy('overall_accuracy', 'desc')
            ->limit(50)
            ->get(['username', 'overall_accuracy', 'total_questions', 'last_activity_at']);

        return response()->json(['data' => $leaderboard]);
    }
}