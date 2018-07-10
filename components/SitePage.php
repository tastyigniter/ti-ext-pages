<?php namespace SamPoyigi\Pages\Components;

use SamPoyigi\Pages\Models\Pages_model;

class SitePage extends \System\Classes\BaseComponent
{
    public $allPages;

    public function defineProperties()
    {
        return [
            'slug' => [
                'label'   => 'sampoyigi.pages::default.label_permalink_slug',
                'comment' => 'sampoyigi.pages::default.help_permalink',
                'default' => '{{ :slug }}',
                'type'    => 'text',
            ],
        ];
    }

    public function onRun()
    {
        $this->page['sitePage'] = $this->loadPage();
    }

    protected function loadPage()
    {
        $slug = $this->param('slug', $this->property('slug'));

        $page = Pages_model::where('permalink_slug', $slug);

        return $page->isEnabled()->first();
    }
}
