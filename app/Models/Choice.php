<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'body',
        'score_value',
        'order_index',
    ];

    protected $casts = [
        'score_value' => 'integer',
        'order_index' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
