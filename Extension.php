<?php namespace SamPoyigi\Pages;

class Extension extends \System\Classes\BaseExtension
{
    public function initialize()
    {
	}

	public function registerComponents() {
		return array(
            'SamPoyigi\Pages\Components\SitePage' => array(
                'code'        => 'sitePage',
                'name'        => 'lang:sampoyigi.pages::default.text_component_title',
                'description' => 'lang:sampoyigi.pages::default.text_component_desc',
			),
            'SamPoyigi\Pages\Components\PageNav' => array(
                'code'        => 'pageNav',
                'name'        => 'lang:sampoyigi.pages::default.text_component_title',
                'description' => 'lang:sampoyigi.pages::default.text_component_desc',
			),
		);
	}

	public function registerNavigation()
    {
        return [
            'design' => [
                'child' => [
                    'pages'          => [
                        'priority'   => 9,
                        'class'      => 'pages',
                        'href'       => admin_url('sampoyigi/pages/pages'),
                        'title'      => lang('admin::default.menu_page'),
                        'permission' => 'Site.Pages',
                    ],
                ]
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'Module.Messages' => [
                'action'      => ['manage'],
                'description' => 'Ability to manage local extension settings',
            ],
        ];
    }
}
