<?php

namespace Igniter\Pages\Tests\Models;

use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Database\Traits\Sortable;
use Igniter\Main\Classes\Theme;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Main\Template\Layout;
use Igniter\Pages\Models\Page;
use Igniter\System\Models\Concerns\Switchable;
use Igniter\System\Models\Language;

it('returns dropdown options for enabled pages', function() {
    $options = Page::getDropdownOptions();

    expect($options->isNotEmpty())->toBeTrue()
        ->and($options)
        ->toContain('About Us')
        ->toContain('Policy');
});

it('loads enabled pages in alphabetical order', function() {
    Page::$pagesCache = null;
    $pages = Page::loadPages();

    expect($pages->isNotEmpty())->toBeTrue()
        ->and($pages->first()->title)->toBe('About Us')
        ->and($pages->last()->title)->toBe('Terms and Conditions');
});

it('returns layout options for active theme', function() {
    $themeManager = mock(ThemeManager::class);
    $themeManager->shouldReceive('getActiveTheme')->andReturn(new Theme('test-theme-path', ['code' => 'igniter-orange']));
    app()->instance(ThemeManager::class, $themeManager);

    $page = new Page;
    $options = $page->getLayoutOptions();

    expect($options)->toBeArray()
        ->and($options)->toHaveKeys(['static', 'default']);
});

it('returns layout object for the page', function() {
    $page = new Page(['layout' => 'default', 'theme' => 'igniter-orange']);

    $layout = $page->getLayoutObject();

    expect($layout)->toBeInstanceOf(Layout::class)
        ->and($layout->fileName)->toBe('default.blade.php');
});

it('returns null when layout is not found', function() {
    $page = new Page(['layout' => 'nonexistent', 'theme' => 'igniter-orange']);

    $layout = $page->getLayoutObject();

    expect($layout)->toBeNull();
});

it('returns null when no available layout in active theme', function() {
    $page = new Page(['theme' => 'test-theme']);
    $themeManager = mock(ThemeManager::class);
    $themeManager->shouldReceive('getActiveTheme')->andReturn(new Theme('test-theme-path', ['code' => 'test-theme']));
    app()->instance(ThemeManager::class, $themeManager);

    $layout = $page->getLayoutObject();

    expect($layout)->toBeNull();
});

it('decodes HTML entities in content attribute', function() {
    $page = new Page(['content' => 'Test &amp; Content']);

    $content = $page->getContentAttribute($page->content);

    expect($content)->toBe('Test & Content');
});

it('sets default language id before saving', function() {
    Page::$pagesCache = null;
    Language::clearDefaultModel();
    $language = Language::factory()->create(['status' => 1]);
    $language->makeDefault();
    $page = new Page();
    $page->save();
    $page = $page->fresh();

    expect($page->language_id)->toBe($language->getKey());
});

it('configures page model correctly', function() {
    $page = new Page;

    expect(class_uses_recursive($page))
        ->toContain(HasPermalink::class)
        ->toContain(Sortable::class)
        ->toContain(Switchable::class)
        ->and(Page::SORT_ORDER)->toEqual('priority')
        ->and($page->getTable())->toBe('pages')
        ->and($page->getKeyName())->toBe('page_id')
        ->and($page->getGuarded())->toEqual([])
        ->and($page->timestamps)->toBeTrue()
        ->and($page->getCasts())->toEqual([
            'page_id' => 'int',
            'language_id' => 'integer',
            'metadata' => 'json',
            'status' => 'boolean',
        ])
        ->and($page->relation)->toEqual([
            'belongsTo' => [
                'language' => \Igniter\System\Models\Language::class,
            ],
        ])
        ->and($page->permalinkable())->toEqual([
            'permalink_slug' => [
                'source' => 'title',
            ],
        ]);
});
