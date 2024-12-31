<?php

namespace Igniter\Pages\Models\Observers;

use Igniter\Pages\Models\Menu;

class MenuObserver
{
    public function saved(Menu $model)
    {
        $model->restorePurgedValues();

        if (array_key_exists('items', $model->getAttributes())) {
            $model->addMenuItems((array)$model->getAttribute('items'));
        }
    }
}
