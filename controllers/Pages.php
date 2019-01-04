<?php namespace Igniter\Pages\Controllers;

use AdminMenu;

class Pages extends \Admin\Classes\AdminController
{
    public $implement = [
        'Admin\Actions\ListController',
        'Admin\Actions\FormController',
    ];

    public $listConfig = [
        'list' => [
            'model' => 'Igniter\Pages\Models\Pages_model',
            'title' => 'lang:igniter.pages::default.text_title',
            'emptyMessage' => 'lang:igniter.pages::default.text_empty',
            'defaultSort' => ['country_name', 'ASC'],
            'configFile' => 'pages_model',
        ],
    ];

    public $formConfig = [
        'name' => 'lang:igniter.pages::default.text_form_name',
        'model' => 'Igniter\Pages\Models\Pages_model',
        'create' => [
            'title' => 'lang:admin::lang.form.create_title',
            'redirect' => 'igniter/pages/pages/edit/{page_id}',
            'redirectClose' => 'pages',
        ],
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'igniter/pages/pages/edit/{page_id}',
            'redirectClose' => 'pages',
        ],
        'delete' => [
            'redirect' => 'pages',
        ],
        'configFile' => 'pages_model',
    ];

    protected $requiredPermissions = 'Site.Pages';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('pages', 'design');
    }

    public function formValidate($model, $form)
    {
        $rules[] = ['language_id', 'lang:igniter.pages::default.label_language', 'required|integer'];
        $rules[] = ['name', 'lang:igniter.pages::default.label_name', 'required|min:2|max:255'];
        $rules[] = ['title', 'lang:igniter.pages::default.label_title', 'required|min:2|max:255'];
        $rules[] = ['permalink_slug', 'lang:igniter.pages::default.label_permalink_slug', 'alpha_dash|max:255'];
        $rules[] = ['content', 'lang:igniter.pages::default.label_content', 'required|min:2'];
        $rules[] = ['meta_description', 'lang:igniter.pages::default.label_meta_description', 'min:2|max:255'];
        $rules[] = ['meta_keywords', 'lang:igniter.pages::default.label_meta_keywords', 'min:2|max:255'];
        $rules[] = ['layout_id', 'lang:igniter.pages::default.label_layout', 'integer'];
        $rules[] = ['navigation.*', 'lang:igniter.pages::default.label_navigation', 'required'];
        $rules[] = ['status', 'lang:admin::lang.label_status', 'required|integer'];

        return $this->validatePasses($form->getSaveData(), $rules);
    }
}