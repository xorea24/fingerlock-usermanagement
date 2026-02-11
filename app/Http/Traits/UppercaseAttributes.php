<?php

namespace App\Http\Traits;

use Illuminate\Support\Str;

trait UppercaseAttributes
{
    /**
     * Register a saving model event to uppercase specified attributes.
     */
    protected static function bootUppercaseAttributes()
    {
        static::saving(function ($model) {
            foreach ($model->getUppercaseAttributes() as $attribute) {
                if (is_string($model->{$attribute})) {
                    $model->{$attribute} = Str::upper($model->{$attribute});
                }
            }
        });
    }

    /**
     * Get the attributes that should be uppercased.
     *
     * @return array
     */
    protected function getUppercaseAttributes()
    {
        // This array should be defined in the model using the trait
        return property_exists($this, 'uppercaseAttributes') ? $this->uppercaseAttributes : [];
    }
}