<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    use HasFactory;

    protected $table = 'exam_sessions';

    protected $fillable = [
        'user_id',
        'guest_token',
        'mode',
        'categories',
        'subcategory_keys',
        'difficulty',
        'total_questions',
        'score',
        'percentage',
        'passed',
        'time_taken_sec',
        'completed_at',
    ];

    protected $casts = [
        'categories' => 'array',
        'subcategory_keys' => 'array',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(SessionAnswer::class, 'session_id');
    }
}