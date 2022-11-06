<?php

namespace Trisnm\Userstamps;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserstampsScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        return $builder;
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        // This callback will be invoked when Illuminate\Database\Eloquent\Builder::delete executed
        $builder->onDelete(function (Builder $builder) {
            $deleted_by = $builder->getModel()->getDeletedByColumn();

            if ($builder->getModel()->usingSoftDeletes()) {
                $deleted_at = $builder->getModel()->getDeletedAtColumn();
                return $builder->update([
                    $deleted_by => $builder->getModel()->getTargetUser(),
                    $deleted_at => $builder->getModel()->freshTimestampString()
                ]);
            }

            return $builder->update([
                $deleted_by => $builder->getModel()->getTargetUser()
            ]);
        });

        $builder->macro('updateWithUserstamps', function (Builder $builder, $values) {
            if (!$builder->getModel()->isUserstamping() || is_null(Auth::id())) {
                return $builder->update($values);
            }

            $values[$builder->getModel()->getUpdatedByColumn()] = $builder->getModel()->getTargetUser();

            return $builder->update($values);
        });
    }
}
