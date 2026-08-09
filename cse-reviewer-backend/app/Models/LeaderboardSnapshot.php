<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderboardSnapshot extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'type',
        'snapshot_data',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
        'created_at' => 'datetime',
    ];
}