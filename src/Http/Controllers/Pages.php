<?php

namespace Igniter\Pages\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;
use Igniter\Pages\Models\Menu;

class Pages extends \Igniter\Admin\Classes\AdminController
{
    public $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
        \Igniter\Admin\Http\Actions\FormController::class,
    ];

    public $listConfig = [
        'list' => [
            'model' => \Igniter\Pages\Models\Page::class,
            'title' => 'lang:igniter.pages::default.text_title',
            'emptyMessage' => 'lang:igniter.pages::default.text_empty',
            'defaultSort' => ['page_id', 'DESC'],
            'configFile' => 'page',
        ],
    ];

    public $formConfig = [
        'name' => 'lang:igniter.pages::default.text_form_name',
        'model' => \Igniter\Pages\Models\Page::class,
        'create' => [
            'title' => 'lang:admin::lang.form.create_title',
            'redirect' => 'igniter/pages/pages/edit/{page_id}',
            'redirectClose' => 'igniter/pages/pages',
            'redirectNew' => 'igniter/pages/pages/create',
        ],
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'igniter/pages/pages/edit/{page_id}',
            'redirectClose' => 'igniter/pages/pages',
            'redirectNew' => 'igniter/pages/pages/create',
        ],
        'delete' => [
            'redirect' => 'igniter/pages/pages',
        ],
        'configFile' => 'page',
    ];

    protected $requiredPermissions = 'Igniter.Pages.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('pages', 'design');
    }

    public function index()
    {
        if ($this->getUser()->hasPermission('Igniter.PageMenus.Manage')) {
            Menu::syncAll();
        }

        $this->asExtension('ListController')->index();
    }

    public function formValidate($model, $form)
    {
        $rules[] = ['language_id', 'lang:igniter.pages::default.label_language', 'required|integer'];
        $rules[] = ['title', 'lang:igniter.pages::default.label_title', 'required|min:2|max:255'];
        $rules[] = ['permalink_slug', 'lang:igniter.pages::default.label_permalink_slug', 'alpha_dash|max:255'];
        $rules[] = ['content', 'lang:igniter.pages::default.label_content', 'required|min:2'];
        $rules[] = ['meta_description', 'lang:igniter.pages::default.label_meta_description', 'nullable'];
        $rules[] = ['meta_keywords', 'lang:igniter.pages::default.label_meta_keywords', 'nullable'];
        $rules[] = ['metadata.navigation_hidden', 'lang:igniter.pages::default.label_navigation', 'required'];
        $rules[] = ['status', 'lang:admin::lang.label_status', 'required|integer'];

        return $this->validatePasses($form->getSaveData(), $rules);
    }
}
