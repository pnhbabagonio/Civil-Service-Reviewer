<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStat;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $stats = UserStat::where('user_id', $request->user()->id)->first();

        if (!$stats) {
            return response()->json([
                'data' => [
                    'total_questions' => 0,
                    'total_correct' => 0,
                    'overall_accuracy' => 0,
                    'exams_taken' => 0,
                    'best_simulator_score' => 0,
                    'current_streak' => 0,
                    'longest_streak' => 0,
                    'total_study_sec' => 0,
                ],
            ]);
        }

        return response()->json(['data' => $stats]);
    }

    public function categories(Request $request)
    {
        $sessions = $request->user()->sessions()->get();
        
        $catStats = [];
        
        foreach ($sessions as $session) {
            foreach ($session->answers as $answer) {
                $question = $answer->question;
                if (!$question) continue;
                
                $cat = $question->category;
                if (!isset($catStats[$cat])) {
                    $catStats[$cat] = ['correct' => 0, 'total' => 0];
                }
                $catStats[$cat]['total']++;
                if ($answer->is_correct) {
                    $catStats[$cat]['correct']++;
                }
            }
        }

        $result = [];
        foreach ($catStats as $cat => $data) {
            $result[$cat] = [
                'accuracy' => $data['total'] > 0 
                    ? round(($data['correct'] / $data['total']) * 100, 2) 
                    : 0,
                'total' => $data['total'],
            ];
        }

        return response()->json(['data' => $result]);
    }
}