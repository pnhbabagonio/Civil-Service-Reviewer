<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar_url',
        'is_admin',
        'last_activity_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Question::class, 'bookmarks')
                    ->withTimestamps();
    }

    public function flags()
    {
        return $this->belongsToMany(Question::class, 'flags')
                    ->withPivot('reason')
                    ->withTimestamps();
    }

    public function stats()
    {
        return $this->hasOne(UserStat::class);
    }
}