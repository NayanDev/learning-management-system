<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SilabusTopic extends Model
{
    protected $table = 'silabus_topics';
    protected $primaryKey = 'id';
    protected $fillable = ["activity", "skill", "metode", "duration", "silabus_id"];
}
