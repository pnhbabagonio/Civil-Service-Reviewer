<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStat extends Model
{
    use HasFactory;

    protected $table = 'user_stats';

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'username',
        'total_questions',
        'total_correct',
        'overall_accuracy',
        'exams_taken',
        'best_simulator_score',
        'current_streak',
        'longest_streak',
        'total_study_sec',
        'last_activity_at',
    ];

    protected $casts = [
        'overall_accuracy' => 'decimal:2',
        'last_activity_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}