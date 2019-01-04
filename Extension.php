<?php namespace Igniter\Pages;

use Illuminate\Database\Eloquent\Relations\Relation;

class Extension extends \System\Classes\BaseExtension
{
    public function boot()
    {
        Relation::morphMap([
            'pages' => 'Igniter\Pages\Models\Pages_model',
        ]);
    }

    public function registerComponents()
    {
        return [
            'Igniter\Pages\Components\SitePage' => [
                'code' => 'sitePage',
                'name' => 'lang:igniter.pages::default.text_component_title',
                'description' => 'lang:igniter.pages::default.text_component_desc',
            ],
            'Igniter\Pages\Components\PageNav' => [
                'code' => 'pageNav',
                'name' => 'lang:igniter.pages::default.nav.text_component_title',
                'description' => 'lang:igniter.pages::default.nav.text_component_desc',
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'design' => [
                'child' => [
                    'pages' => [
                        'priority' => 9,
                        'class' => 'pages',
                        'href' => admin_url('igniter/pages/pages'),
                        'title' => lang('admin::lang.side_menu.page'),
                        'permission' => 'Module.Pages',
                    ],
                ],
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'Module.Pages' => [
                'group' => 'module',
                'description' => 'Ability to manage local extension settings',
            ],
        ];
    }
}
