<?php

namespace Igniter\Pages\Tests\Models;

use Igniter\Flame\Database\Traits\NestedTree;
use Igniter\Flame\Database\Traits\Sortable;
use Igniter\Flame\Database\Traits\Validation;
use Igniter\Pages\Models\Menu;
use Igniter\Pages\Models\MenuItem;
use Illuminate\Support\Facades\Event;

it('returns type info for a valid type', function() {
    Event::listen('pages.menuitem.getTypeInfo', function($type) {
        return ['type' => $type];
    });

    $typeInfo = MenuItem::getTypeInfo('url');

    expect($typeInfo['type'])->toEqual('url');
});

it('returns type options including custom types', function() {
    Event::listen('pages.menuitem.listTypes', function() {
        return null;
    });

    Event::listen('pages.menuitem.listTypes', function() {
        return ['test-url' => 'URL', 'test-header' => 'Header'];
    });

    $typeOptions = (new MenuItem)->getTypeOptions();

    expect($typeOptions['test-url'])->toEqual('URL')
        ->and($typeOptions['test-header'])->toEqual('Header');
});

it('returns parent id options excluding current item', function() {
    $menu = Menu::create(['name' => 'Test Menu', 'code' => 'test-menu', 'theme_code' => 'test-theme']);
    $menuItem = MenuItem::create(['menu_id' => $menu->getKey(), 'code' => 'test-menu-item', 'title' => 'Parent Item', 'type' => 'url']);
    $childItem = MenuItem::create(['menu_id' => $menu->getKey(), 'code' => 'test-menu-item-2', 'title' => 'Child Item', 'type' => 'url', 'parent_id' => $menuItem->id]);

    $parentIdOptions = $childItem->getParentIdOptions();

    expect($parentIdOptions->isNotEmpty())->toBeTrue()
        ->and($parentIdOptions->all())->not->toHaveKey($childItem->id)
        ->and($parentIdOptions->all())->toHaveKey($menuItem->id);
});

it('returns summary attribute with parent and type', function() {
    $menu = Menu::create(['name' => 'Test Menu', 'code' => 'test-menu', 'theme_code' => 'test-theme']);
    $parentItem = MenuItem::create(['menu_id' => $menu->getKey(), 'code' => 'test-menu-item', 'title' => 'Parent Item', 'type' => 'url']);
    $menuItem = MenuItem::create(['menu_id' => $menu->getKey(), 'code' => 'test-menu-item-2', 'title' => 'Child Item', 'type' => 'url', 'parent_id' => $parentItem->id, 'type' => 'url']);

    $summary = $menuItem->getSummaryAttribute(null);

    expect($summary)->toBe('Parent: Parent Item Type: url');
})->only();

it('configures menu item model correctly', function() {
    $menuItem = new MenuItem;

    expect(class_uses_recursive($menuItem))
        ->toContain(NestedTree::class)
        ->toContain(Sortable::class)
        ->toContain(Validation::class)
        ->and(MenuItem::SORT_ORDER)->toEqual('priority')
        ->and($menuItem->getTable())->toBe('igniter_pages_menu_items')
        ->and($menuItem->getKeyName())->toBe('id')
        ->and($menuItem->getFillable())->toEqual([
            'code',
            'title',
            'description',
            'menu_id',
            'parent_id',
            'priority',
            'type',
            'url',
            'reference',
            'config',
        ])
        ->and($menuItem->timestamps)->toBeTrue()
        ->and($menuItem->relation)->toEqual([
            'belongsTo' => [
                'menu' => [Menu::class],
                'parent' => [MenuItem::class, 'foreignKey' => 'parent_id', 'otherKey' => 'id'],
            ],
        ])
        ->and($menuItem->rules)->toEqual([
            ['type', 'igniter.pages::default.menu.label_type', 'required|string'],
            ['code', 'igniter.pages::default.menu.label_code', 'alpha_dash'],
            ['title', 'igniter.pages::default.menu.label_title', 'max:128'],
            ['description', 'admin::lang.label_description', 'max:255'],
            ['parent_id', 'igniter.pages::default.menu.label_parent_id', 'nullable|integer'],
            ['url', 'igniter.pages::default.menu.label_url', 'max:500'],
        ]);
});
