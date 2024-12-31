<?php

namespace Igniter\Pages\Tests\Classes;

use Igniter\Main\Classes\Theme;
use Igniter\Pages\Classes\Page;
use Igniter\Pages\Models\Page as PageModel;
use Igniter\System\Models\Language;

it('returns menu type info with static page references', function() {
    $typeInfo = Page::getMenuTypeInfo('static-page');

    expect($typeInfo)->toBeArray()
        ->and($typeInfo['references'])->toBeArray();
});

it('resolves static page menu item with valid reference', function() {
    $theme = new Theme('test-theme-path', ['code' => 'test-theme']);
    $item = (object)['type' => 'static-page', 'reference' => 1];
    $url = 'http://localhost/test-page';
    $language = Language::factory()->create(['status' => 1]);
    PageModel::create(['permalink_slug' => 'test-page', 'title' => 'AATest Page', 'status' => 1, 'language_id' => $language->getKey()]);

    $result = Page::resolveMenuItem($item, $url, $theme);

    expect($result)->toBeArray()
        ->and($result['url'])->toEndWith('/test-page')
        ->and($result['isActive'])->toBeTrue();
});

it('returns null for static page menu item with invalid reference', function() {
    $theme = new Theme('test-theme-path', ['code' => 'test-theme']);
    $item = (object)['type' => 'static-page', 'reference' => 999];
    $url = 'http://example.com/test-page';
    PageModel::where('status', 1)->update(['status' => 0]);

    PageModel::$pagesCache = null;
    $result = Page::resolveMenuItem($item, $url, $theme);

    expect($result)->toBeNull();
});

it('resolves menu item with multiple pages', function() {
    $theme = new Theme('test-theme-path', ['code' => 'test-theme']);
    $item = (object)['type' => 'all-pages'];
    $url = 'http://example.com/test-page';

    PageModel::$pagesCache = null;
    $result = Page::resolveMenuItem($item, $url, $theme);

    expect($result)->toBeArray()
        ->and($result['items'])->toBeArray()
        ->and($result['items'][0]['title'])->toBe('About Us')
        ->and($result['items'][1]['title'])->toBe('Policy');
});

it('excludes hidden pages from menu items', function() {
    $theme = new Theme('test-theme-path', ['code' => 'test-theme']);
    $item = (object)['type' => 'all-pages'];
    $url = 'http://example.com/test-page';
    $page = PageModel::firstWhere('permalink_slug', 'about-us');
    $page->metadata = ['navigation_hidden' => true];
    $page->save();

    PageModel::$pagesCache = null;
    $result = Page::resolveMenuItem($item, $url, $theme);

    expect($result)->toBeArray()
        ->and($result['items'])->toBeArray()
        ->and($result['items'])->toHaveCount(2)
        ->and($result['items'][0]['title'])->toBe('Policy');
});
