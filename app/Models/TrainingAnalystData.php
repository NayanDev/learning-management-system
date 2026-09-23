<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAnalystData extends Model
{
    protected $table = 'training_analyst_datas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'training_analyst_id',
        'position',
        'personil',
        'qualification',
        'general',
        'technic',
    ];

    protected $casts = [
        'qualification' => 'array',
        'general'       => 'array',
        'technic'       => 'array',
    ];

    public function trainingAnalyst()
    {
        return $this->belongsTo(TrainingAnalyst::class, 'training_analyst_id');
    }
}
