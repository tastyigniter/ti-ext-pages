<?php

declare(strict_types=1);

namespace Igniter\Pages\Tests\Classes;

use Igniter\Main\Classes\PageBuilder;
use Igniter\Main\Classes\Theme;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Main\Models\Theme as ThemeModel;
use Igniter\Pages\Classes\PageManager;
use Igniter\Pages\Models\Page;
use Illuminate\Support\Facades\View;

beforeEach(function(): void {
    ThemeModel::syncAll();

    $this->theme = resolve(ThemeManager::class)->getActiveTheme();
    $this->sections = [['id' => 's_about', 'layout' => 'cols-1', 'columns' => [['blocks' => []]]]];

    View::addNamespace('pages-builder-test', __DIR__.'/../_fixtures/views');
});

it('returns null for pages that are not static pages', function(): void {
    expect((new PageManager)->renderPageContents($this->theme, []))->toBeNull();
});

it('renders cleaned content for rich text pages', function(): void {
    Page::where('permalink_slug', 'about-us')->update(['content' => '<p>About</p><script>alert(1)</script>']);
    $page = (new PageManager)->initPage('about-us');

    expect((new PageManager)->renderPageContents($this->theme, $page))
        ->toContain('<p>About</p>')
        ->not->toContain('<script>');
});

it('returns null for rich text pages without content', function(): void {
    Page::where('permalink_slug', 'about-us')->update(['content' => '']);
    $page = (new PageManager)->initPage('about-us');

    expect((new PageManager)->renderPageContents($this->theme, $page))->toBeNull();
});

it('renders page builder sections with the theme views', function(): void {
    Page::where('permalink_slug', 'about-us')->update(['editor' => Page::EDITOR_BUILDER, 'sections' => json_encode($this->sections)]);
    $page = (new PageManager)->initPage('about-us');

    $builder = mock(PageBuilder::class)->makePartial();
    $builder->shouldReceive('getViewNamespace')->andReturn('pages-builder-test');
    $builder->shouldReceive('normalize')->with($this->theme, $this->sections, Page::BUILDER_PAGE_CODE)->andReturn($this->sections);
    $builder->shouldReceive('renderBlade')->with($this->theme, $this->sections, Page::BUILDER_PAGE_CODE)->andReturn('<section id="s_about"></section>');
    app()->instance(PageBuilder::class, $builder);

    expect((new PageManager)->renderPageContents($this->theme, $page))->toBe('<section id="s_about"></section>');
});

it('falls back to content when the theme has no page builder views', function(): void {
    Page::where('permalink_slug', 'about-us')->update([
        'editor' => Page::EDITOR_BUILDER,
        'sections' => json_encode($this->sections),
        'content' => '<p>Fallback</p>',
    ]);
    $page = (new PageManager)->initPage('about-us');

    $builder = mock(PageBuilder::class)->makePartial();
    $builder->shouldReceive('getViewNamespace')->andReturn('missing-namespace');
    $builder->shouldNotReceive('renderBlade');
    app()->instance(PageBuilder::class, $builder);

    expect((new PageManager)->renderPageContents($this->theme, $page))->toContain('<p>Fallback</p>')
        ->and((new PageManager)->renderPageContents(null, $page))->toContain('<p>Fallback</p>');
});

it('knows when a page uses the page builder', function(): void {
    $page = new Page(['editor' => Page::EDITOR_BUILDER, 'sections' => $this->sections]);

    expect($page->usesPageBuilder())->toBeTrue()
        ->and($page->sections)->toBe($this->sections)
        ->and((new Page(['editor' => Page::EDITOR_CONTENT]))->usesPageBuilder())->toBeFalse()
        ->and(array_keys($page->getEditorOptions()))->toBe([Page::EDITOR_CONTENT, Page::EDITOR_BUILDER])
        ->and($this->theme)->toBeInstanceOf(Theme::class);
});

it('saves page builder sections from the admin form', function(): void {
    $slug = 'built-page-'.strtolower(str_random(6));

    actingAsSuperUser()
        ->post(route('igniter.pages.pages', ['slug' => 'create']), [
            'Page' => [
                'title' => 'Built Page',
                'permalink_slug' => $slug,
                'editor' => Page::EDITOR_BUILDER,
                'content' => '',
                'sections' => json_encode($this->sections),
                'status' => 1,
                'language_id' => 1,
                'metadata' => ['navigation_hidden' => 0],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ])
        ->assertOk();

    $page = Page::firstWhere('permalink_slug', $slug);

    expect($page->editor)->toBe(Page::EDITOR_BUILDER)
        ->and($page->sections[0]['id'])->toBe('s_about')
        ->and($page->sections[0]['layout'])->toBe('cols-1');
});

it('loads the page builder field on the edit form', function(): void {
    $page = Page::firstWhere('permalink_slug', 'about-us');

    actingAsSuperUser()
        ->get(route('igniter.pages.pages', ['slug' => 'edit/'.$page->getKey()]))
        ->assertOk()
        ->assertSee('data-control="page-builder"', false)
        ->assertSee('name="Page[editor]"', false);
});
