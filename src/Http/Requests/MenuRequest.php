<?php

namespace Igniter\Pages\Http\Requests;

use Igniter\System\Classes\FormRequest;

class MenuRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'theme_code' => lang('igniter.pages::default.menu.label_theme'),
            'name' => lang('admin::lang.label_name'),
            'code' => lang('igniter.pages::default.menu.label_code'),
            'items' => lang('igniter.pages::default.menu.text_menu_items'),
        ];
    }

    public function rules()
    {
        return [
            'theme_code' => ['sometimes', 'required', 'alpha_dash'],
            'name' => ['required', 'string'],
            'code' => ['required', 'alpha_dash'],
            'items' => ['sometimes', 'required', 'array'],
        ];
    }
}
