<?php namespace SamPoyigi\Pages\Components;

use SamPoyigi\Pages\Models\Pages_model;

class SitePage extends \System\Classes\BaseComponent
{
    public $allPages;

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'rainlab.blog::lang.settings.post_slug',
                'description' => 'rainlab.blog::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
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
