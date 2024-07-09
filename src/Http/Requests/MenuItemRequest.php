<?php

namespace Igniter\Pages\Http\Requests;

use Igniter\System\Classes\FormRequest;

class MenuItemRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'title' => lang('igniter.pages::default.menu.label_title'),
            'type' => lang('igniter.pages::default.menu.label_type'),
            'url' => lang('igniter.pages::default.menu.label_url'),
            'reference' => lang('igniter.pages::default.menu.label_reference'),
            'parent_id' => lang('igniter.pages::default.menu.label_parent_id'),
            'description' => lang('admin::lang.label_description'),
            'code' => lang('igniter.pages::default.menu.label_code'),
            'config[extraAttributes]' => lang('igniter.pages::default.menu.label_attributes'),
        ];
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'type' => ['required', 'string'],
            'url' => ['required_if:type,url', 'string'],
            'reference' => ['alpha_dash'],
            'parent_id' => ['integer'],
            'description' => ['nullable', 'string', 'max:500'],
            'code' => ['alpha_dash'],
            'config[extraAttributes]' => ['string'],
        ];
    }
}
