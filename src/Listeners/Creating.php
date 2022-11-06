<?php

namespace Trisnm\Userstamps\Listeners;

class Creating
{
    /**
     * When the model is being created.
     *
     * @param  Illuminate\Database\Eloquent  $model
     * @return void
     */
    public function handle($model)
    {
        if (! $model->isUserstamping() || is_null($model->getCreatedByColumn())) {
            return;
        }

        if (is_null($model->{$model->getCreatedByColumn()})) {
            $model->{$model->getCreatedByColumn()} = $model->getTargetUser();
        }

        if (is_null($model->{$model->getUpdatedByColumn()}) && ! is_null($model->getUpdatedByColumn())) {
            $model->{$model->getUpdatedByColumn()} = $model->getTargetUser();
        }
    }
}
