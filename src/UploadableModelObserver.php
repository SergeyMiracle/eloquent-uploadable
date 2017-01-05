<?php

namespace SergeyMiracle\Uploadable;

use Illuminate\Database\Eloquent\Model;

class UploadableModelObserver
{
    /**
     * Trigger function when saving a model (creating).
     *
     * @param  Model  $model
     * @return void
     */
    public function saving(Model $model)
    {
        $model->performUploads();
    }

    /**
     * Trigger function when deleting a model.
     *
     * @param  Model  $model
     * @return void
     */
    public function deleting(Model $model)
    {
        $model->performDeletes();
    }
}
