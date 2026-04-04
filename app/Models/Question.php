<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id',
        'body',
        'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    // Choices for this question, ordered for display
    public function choices()
    {
        return $this->hasMany(Choice::class)->orderBy('order_index');
    }
}
