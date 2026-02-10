<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date_scheduled',
        'position_id',
        'status',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function interviewers(): HasMany
    {
        return $this->HasMany(Interviewer::class, 'interview_id');
    }
}