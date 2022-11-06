<?php

namespace Trisnm\Userstamps\Listeners;

use Illuminate\Support\Facades\Auth;

class Saving
{
    /**
     * When the model is being created.
     *
     * @param  Illuminate\Database\Eloquent  $model
     * @return void
     */
    public function handle($model)
    {
        if (!$model->isUserstamping()) {
            return;
        }

        $col = $model->getCreatedByColumn();
        if($model->exists) {
            $col = $model->getUpdatedByColumn();
        }

        $model->{$col} = $model->getTargetUser();
    }
}
