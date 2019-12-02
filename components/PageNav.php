<?php namespace Igniter\Pages\Components;

use Igniter\Pages\Models\Pages_model;

class PageNav extends \System\Classes\BaseComponent
{
    public $allPages;

    public function onRun()
    {
        traceLog('Deprecated component. See staticMenu component.');
        $this->page['activePageId'] = null;
        if (method_exists($this->controller, 'getPage') AND $page = $this->controller->getPage())
            $this->page['activePageId'] = $page->getId();

        $this->page['headerPageList'] = $this->getPages('header');
        $this->page['footerPageList'] = $this->getPages('footer');
        $this->page['sidebarPageList'] = $this->getPages('side_bar');
    }

    public function getPages($navigation)
    {
        if (!$this->allPages)
            $this->loadPages();

        return $this->allPages->filter(function ($page) use ($navigation) {
            return in_array($navigation, (array)$page->navigation);
        });
    }

    protected function loadPages()
    {
        $result = Pages_model::select(
            'permalink_slug', 'name', 'navigation'
        )->isEnabled()->get();

        return $this->allPages = $result;
    }
}
