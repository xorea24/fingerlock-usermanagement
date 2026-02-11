<?php

namespace App\Models;

use App\Http\Traits\UppercaseAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    use SoftDeletes, UppercaseAttributes;

    public const GENDER_MALE = "MALE";
    public const GENDER_FEMALE = "FEMALE";

    public const CIVIL_STATUS_SINGLE = "SINGLE";
    public const CIVIL_STATUS_MARRIED = "MARRIED";
    public const CIVIL_STATUS_WIDOWED = "WIDOWED";
    public const CIVIL_STATUS_DIVORCED = "DIVORCED";
    public const CIVIL_STATUS_SEPARATED = "SEPARATED";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'position_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'gender',
        'birthdate',
        'civil_status',
        'address',
    ];

    /**
     * The attributes to be uppercased.
     */
    protected $uppercaseAttributes = [
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'address',
    ];

    /**
     * Get genders
     */
    public static function genders()
    {
        return [
            self::GENDER_MALE,
            self::GENDER_FEMALE,
        ];
    }

    /**
     * Get civil statuses
     */
    public static function civilStatuses()
    {
        return [
            self::CIVIL_STATUS_SINGLE,
            self::CIVIL_STATUS_MARRIED,
            self::CIVIL_STATUS_WIDOWED,
            self::CIVIL_STATUS_DIVORCED,
            self::CIVIL_STATUS_SEPARATED,
        ];
    }

    /**
     * Get full name
     */
    public function getFullName()
    {
        $first_name = $this->first_name ?? null;
        $middle_name = $this->middle_name ?? null;
        $last_name = $this->last_name ?? null;
        $extension_name = $this->extension_name ?? null;
        $parts = [$first_name];
        if (!empty($middle_name)) $parts[] = $middle_name;
        if (!empty($last_name)) $parts[] = $last_name;
        if (!empty($extension_name)) $parts[] = $extension_name;
        return implode(' ', $parts);
    }
    
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}