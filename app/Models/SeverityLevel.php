<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeverityLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id',
        'label',
        'min_score',
        'max_score',
        'description',
        'color_hex',
        'order_index',
    ];

    protected $casts = [
        'min_score'   => 'integer',
        'max_score'   => 'integer',
        'order_index' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
