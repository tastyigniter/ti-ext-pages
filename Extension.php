<?php namespace SamPoyigi\Pages;

class Extension extends \System\Classes\BaseExtension
{
    public function initialize()
    {
	}

	public function registerComponents() {
		return array(
            'SamPoyigi\Pages\Components\Pages' => array(
                'code'        => 'pages',
                'name'        => 'lang:sampoyigi.pages::default.text_component_title',
                'description' => 'lang:sampoyigi.pages::default.text_component_desc',
			),
		);
	}
}
