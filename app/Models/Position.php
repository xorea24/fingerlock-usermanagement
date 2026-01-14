<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'present_position_id',
        'desired_position_id',
        'first_name',
        'middle_name',
        'last_name',
        'name_suffix',
        'gender',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'present_position_id');
    }

    public function applicants(): HasMany
    {
        return $this->HasMany(Applicant::class, 'desired_position_id');
    }
}