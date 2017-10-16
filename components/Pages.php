<?php namespace SamPoyigi\Pages_module\Components;

use Admin\Models\Pages_model;

class Pages extends \System\Classes\BaseComponent
{
    public function onRender()
    {
        $this->page['pagesTitle'] = $this->property('title', '');

        $this->page['activePageId'] = null;
        if (method_exists($this->controller, 'getPage') AND $page = $this->controller->getPage())
            $this->page['activePageId'] = $page->getKey();

        $this->page['pagesList'] = $this->loadPages();
    }

    protected function loadPages()
    {
        $result = Pages_model::with(['permalink'])
                             ->select('page_id', 'name', 'navigation')
                             ->isEnabled()->get();

        return $result->filter(function ($model) {
            if (empty($model->permalink_slug))
                $model->permalink_slug = 'pages?page_id='.$model->getKey();

            return in_array('side_bar', $model->navigation);
        });
    }
}
