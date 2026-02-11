<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interview extends Model
{
    use SoftDeletes;

    public const STATUS_WAITING = 0;
    public const STATUS_ONGOING = 1;
    public const STATUS_POSTPONED = 2;
    public const STATUS_CANCELLED = 3;
    public const STATUS_COMPLETED = 4;

    protected $fillable = [
        'interview_date',
        'position_id',
        'status',
    ];

    /**
     * Get statuses
     */
    public static function statuses()
    {
        return [
            self::STATUS_WAITING => 'WAITING',
            self::STATUS_ONGOING => 'ONGOING',
            self::STATUS_POSTPONED => 'POSTPONED',
            self::STATUS_CANCELLED => 'CANCELLED',
            self::STATUS_COMPLETED => 'COMPLETED',
        ];
    }

    /**
     * Get status to string
     */
    public function statusToString()
    {
        return self::statuses()[$this->status];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function interviewers(): HasMany
    {
        return $this->HasMany(Interviewer::class, 'interview_id');
    }
}