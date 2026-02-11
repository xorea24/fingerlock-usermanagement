<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes to be uppercased.
     */
    protected $uppercaseAttributes = [
        'name',
    ];

    public function applicants(): HasMany
    {
        return $this->HasMany(Applicant::class, 'position_id');
    }

    public function interviews(): HasMany
    {
        return $this->HasMany(Interview::class, 'position_id');
    }
}