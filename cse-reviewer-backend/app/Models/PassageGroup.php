<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassageGroup extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'category',
        'subcategory',
        'difficulty',
        'title',
        'passage_text',
        'source',
        'has_image',
    ];

    protected $casts = [
        'has_image' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'passage_group_id');
    }
}