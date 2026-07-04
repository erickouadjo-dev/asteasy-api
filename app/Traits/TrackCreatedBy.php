<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait TrackCreatedBy
{
    /**
     * Boot the trait.
     */
    protected static function bootTrackCreatedBy()
    {
        // Automatically set CREATED_BY when creating a resource
        static::creating(function ($model) {
            if (Auth::check() && is_null($model->CREATED_BY)) {
                $model->CREATED_BY = Auth::user()->id;
            }
        });
    }
}
