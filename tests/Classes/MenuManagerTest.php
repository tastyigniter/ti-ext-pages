<?php

namespace Igniter\Pages\Tests\Classes;

use Igniter\Main\Models\Theme;
use Igniter\Pages\Classes\MenuManager;
use Igniter\Pages\Classes\Page;
use Igniter\Pages\Models\Menu;
use Illuminate\Support\Facades\Event;
use Mockery;

beforeEach(function() {
    Theme::syncAll();
    Menu::syncAll();
});

it('loads menus from config files across loaded themes', function() {
    $menuManager = new MenuManager;
    $menus = $menuManager->getMenusConfig();

    expect($menus)->not->toBeEmpty()
        ->and($menus)->toHaveCount(3);
});

it('generates menu references with active state', function() {
    Event::fake();

    $page = Mockery::mock(Page::class);
    $page->shouldReceive('getAttribute')->andReturn('view-menu');
    $menu = Menu::where('code', 'main-menu')->where('theme_code', 'igniter-orange')->first();
    $items = (new MenuManager)->generateReferences($menu, $page);

    expect($items)->not->toBeEmpty();

    Event::assertDispatched('pages.menu.referencesGenerated');
});

it('resolves custom menu item types')->todo();
