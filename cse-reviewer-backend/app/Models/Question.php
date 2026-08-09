<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'category',
        'subcategory',
        'difficulty',
        'question_text',
        'choices',
        'answer',
        'explanation',
        'source',
        'has_image',
        'passage_group_id',
        'version',
    ];

    protected $casts = [
        'choices' => 'array',
        'has_image' => 'boolean',
        'version' => 'integer',
    ];

    public function passageGroup()
    {
        return $this->belongsTo(PassageGroup::class, 'passage_group_id');
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'bookmarks')
                    ->withTimestamps();
    }

    public function flaggedBy()
    {
        return $this->belongsToMany(User::class, 'flags')
                    ->withPivot('reason')
                    ->withTimestamps();
    }

    public function sessionAnswers()
    {
        return $this->hasMany(SessionAnswer::class);
    }
}