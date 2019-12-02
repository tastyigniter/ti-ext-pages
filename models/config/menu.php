<?php

return [
    'list' => [
        'filter' => [],
        'toolbar' => [
            'buttons' => [
                'back' => [
                    'label' => 'admin::lang.button_icon_back',
                    'class' => 'btn btn-default',
                    'href' => 'igniter/pages/pages',
                ],
                'create' => [
                    'label' => 'lang:admin::lang.button_new',
                    'class' => 'btn btn-primary',
                    'href' => 'igniter/pages/menus/create',
                ],
                'delete' => [
                    'label' => 'lang:admin::lang.button_delete',
                    'class' => 'btn btn-danger',
                    'data-attach-loading' => '',
                    'data-request-form' => '#list-form',
                    'data-request' => 'onDelete',
                    'data-request-data' => "_method:'DELETE'",
                    'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm',
                ],
            ],
        ],
        'columns' => [
            'edit' => [
                'type' => 'button',
                'iconCssClass' => 'fa fa-pencil',
                'attributes' => [
                    'class' => 'btn btn-edit',
                    'href' => 'igniter/pages/menus/edit/{id}',
                ],
            ],
            'name' => [
                'label' => 'admin::lang.label_name',
            ],
            'code' => [
                'label' => 'igniter.pages::default.menu.label_code',
            ],
        ],
    ],
    'form' => [
        'toolbar' => [
            'buttons' => [
                'back' => [
                    'label' => 'admin::lang.button_icon_back',
                    'class' => 'btn btn-default',
                    'href' => 'igniter/pages/menus',
                ],
                'save' => [
                    'label' => 'lang:admin::lang.button_save',
                    'class' => 'btn btn-primary',
                    'data-request' => 'onSave',
                    'data-progress-indicator' => 'lang:admin::lang.text_saving',
                ],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-data' => 'close:1',
                    'data-progress-indicator' => 'lang:admin::lang.text_saving',
                ],
                'delete' => [
                    'label' => 'lang:admin::lang.button_icon_delete', 'class' => 'btn btn-danger',
                    'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'",
                    'data-progress-indicator' => 'lang:admin::lang.text_deleting',
                    'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm', 'context' => ['edit'],
                ],
            ],
        ],
        'fields' => [
            'name' => [
                'label' => 'admin::lang.label_name',
                'type' => 'text',
                'span' => 'left',
            ],
            'code' => [
                'label' => 'igniter.pages::default.menu.label_code',
                'type' => 'text',
                'span' => 'right',
            ],
        ],
        'tabs' => [
            'fields' => [
                'items' => [
                    'tab' => 'igniter.pages::default.menu.text_menu_items',
                    'type' => 'connector',
                    'context' => 'edit',
                    'form' => 'menuitem',
                    'nameFrom' => 'title',
                    'sortable' => TRUE,
                    'partial' => 'form/type_info_summary',
                    'containerAttributes' => [
                        'data-control' => 'menu-item-editor',
                    ],
                ],
                '_new_item' => [
                    'tab' => 'igniter.pages::default.menu.text_menu_items',
                    'type' => 'partial',
                    'path' => 'form/new_item_btn',
                    'context' => 'edit',
                ],
            ],
        ],
    ],
];