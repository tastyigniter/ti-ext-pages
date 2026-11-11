<?php

declare(strict_types=1);

namespace Igniter\Pages\Tests\Http\Requests;

use Igniter\Pages\Http\Requests\PageRequest;

it('returns correct attribute labels', function(): void {
    $request = new PageRequest;

    $attributes = $request->attributes();

    expect($attributes)->toHaveKey('language_id', lang('igniter.pages::default.label_language'))
        ->and($attributes)->toHaveKey('title', lang('igniter.pages::default.label_title'))
        ->and($attributes)->toHaveKey('permalink_slug', lang('igniter.pages::default.label_permalink_slug'))
        ->and($attributes)->toHaveKey('content', lang('igniter.pages::default.label_content'))
        ->and($attributes)->toHaveKey('editor', lang('igniter.pages::default.label_editor'))
        ->and($attributes)->toHaveKey('meta_description', lang('igniter.pages::default.label_meta_description'))
        ->and($attributes)->toHaveKey('meta_keywords', lang('igniter.pages::default.label_meta_keywords'))
        ->and($attributes)->toHaveKey('metadata.navigation_hidden', lang('igniter.pages::default.label_navigation'))
        ->and($attributes)->toHaveKey('status', lang('admin::lang.label_status'))
        ->and($attributes)->toHaveKey('layout', lang('igniter.pages::default.label_layout'));
});

it('returns correct validation rules', function(): void {
    $request = new PageRequest;

    $rules = $request->rules();

    expect($rules)->toHaveKey('language_id', ['nullable', 'integer'])
        ->and($rules)->toHaveKey('title', ['required', 'min:2', 'max:255'])
        ->and($rules)->toHaveKey('permalink_slug', ['alpha_dash', 'max:255'])
        ->and($rules)->toHaveKey('editor', ['nullable', 'in:content,builder'])
        ->and($rules)->toHaveKey('content', ['required_unless:editor,builder', 'nullable', 'min:2'])
        ->and($rules)->toHaveKey('sections', ['nullable'])
        ->and($rules)->toHaveKey('meta_description', ['nullable'])
        ->and($rules)->toHaveKey('meta_keywords', ['nullable'])
        ->and($rules)->toHaveKey('metadata.navigation_hidden', ['required'])
        ->and($rules)->toHaveKey('status', ['required', 'integer'])
        ->and($rules)->toHaveKey('layout', ['nullable', 'alpha_dash']);
});

it('requires content only for rich text pages', function(): void {
    $rules = (new PageRequest)->rules();

    expect(validator(['editor' => 'builder', 'content' => ''], ['content' => $rules['content']])->passes())->toBeTrue()
        ->and(validator(['editor' => 'content', 'content' => ''], ['content' => $rules['content']])->fails())->toBeTrue()
        ->and(validator(['content' => ''], ['content' => $rules['content']])->fails())->toBeTrue()
        ->and(validator(['editor' => 'other'], ['editor' => $rules['editor']])->fails())->toBeTrue();
});
