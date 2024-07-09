<?php

namespace Igniter\Pages\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;
use Igniter\Pages\Models\Menu;

class Pages extends \Igniter\Admin\Classes\AdminController
{
    public array $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
        \Igniter\Admin\Http\Actions\FormController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => \Igniter\Pages\Models\Page::class,
            'title' => 'lang:igniter.pages::default.text_title',
            'emptyMessage' => 'lang:igniter.pages::default.text_empty',
            'defaultSort' => ['page_id', 'DESC'],
            'configFile' => 'page',
        ],
    ];

    public array $formConfig = [
        'name' => 'lang:igniter.pages::default.text_form_name',
        'model' => \Igniter\Pages\Models\Page::class,
        'request' => \Igniter\Pages\Http\Requests\PageRequest::class,
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

    protected null|string|array $requiredPermissions = 'Igniter.Pages.*';

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
}
