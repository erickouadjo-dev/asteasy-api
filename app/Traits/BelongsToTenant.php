<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToTenant()
    {
        // Automatically set ENTREPRISE_ID when creating a resource
        static::creating(function ($model) {
            if (Auth::check() && is_null($model->ENTREPRISE_ID)) {
                $user = Auth::user();
                if ($user->ENTREPRISE_ID) {
                    $model->ENTREPRISE_ID = $user->ENTREPRISE_ID;
                }
            }
        });

        // Automatically filter queries by ENTREPRISE_ID
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                // Limit queries to the user's company if they have one
                if ($user->ENTREPRISE_ID) {
                    $builder->where($builder->getModel()->getTable() . '.ENTREPRISE_ID', $user->ENTREPRISE_ID);
                }
            }
        });
    }
}
