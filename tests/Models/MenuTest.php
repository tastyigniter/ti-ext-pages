<?php

namespace Igniter\Pages\Tests\Models;

use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Main\Models\Theme;
use Igniter\Pages\Models\Menu;
use Igniter\Pages\Models\MenuItem;

it('adds menu items after save', function() {
    $menu = new Menu([
        'name' => 'Test Menu',
        'code' => 'test-menu-page',
    ]);

    $menu->items = [
        [
            'title' => 'Test Page',
            'url' => '/test-page-menu',
            'type' => 'url',
        ],
    ];
    $menu->save();

    $this->assertDatabaseHas('igniter_pages_menu_items', [
        'menu_id' => $menu->getKey(),
        'title' => 'Test Page',
        'url' => '/test-page-menu',
    ]);
});

it('configures menu model correctly', function() {
    $menu = new Menu;

    expect(class_uses_recursive($menu))
        ->toContain(Purgeable::class)
        ->and($menu->getTable())->toBe('igniter_pages_menus')
        ->and($menu->getKeyName())->toBe('id')
        ->and($menu->getFillable())->toEqual([])
        ->and($menu->timestamps)->toBeTrue()
        ->and($menu->relation)->toEqual([
            'hasMany' => [
                'items' => [MenuItem::class],
            ],
            'belongsTo' => [
                'theme' => [Theme::class, 'foreignKey' => 'theme_code', 'otherKey' => 'code'],
            ],
        ])
        ->and($menu->getPurgeableAttributes())->toEqual(['items']);
});
