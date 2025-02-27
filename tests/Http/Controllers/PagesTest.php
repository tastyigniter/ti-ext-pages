<?php

declare(strict_types=1);

namespace Igniter\Pages\Tests\Http\Controllers;

use Igniter\Pages\Models\Page;

it('loads static pages page', function(): void {
    actingAsSuperUser()
        ->get(route('igniter.pages.pages'))
        ->assertOk();
});

it('loads create static page', function(): void {
    actingAsSuperUser()
        ->get(route('igniter.pages.pages', ['slug' => 'create']))
        ->assertOk();
});

it('loads edit static page', function(): void {
    $page = Page::firstWhere('permalink_slug', 'about-us');

    actingAsSuperUser()
        ->get(route('igniter.pages.pages', ['slug' => 'edit/'.$page->getKey()]))
        ->assertOk();
});

it('creates static page', function(): void {
    actingAsSuperUser()
        ->post(route('igniter.pages.pages', ['slug' => 'create']), [
            'Page' => [
                'title' => 'Test Page',
                'permalink_slug' => 'test-page',
                'content' => 'Test page content',
                'status' => 1,
                'language_id' => 1,
                'metadata' => ['navigation_hidden' => 0],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ])
        ->assertOk();

    expect(Page::where('permalink_slug', 'test-page')->exists())->toBeTrue();
});

it('updates static page', function(): void {
    $page = Page::firstWhere('permalink_slug', 'about-us');

    actingAsSuperUser()
        ->post(route('igniter.pages.pages', ['slug' => 'edit/'.$page->getKey()]), [
            'Page' => [
                'title' => 'Test Page',
                'permalink_slug' => 'test-page',
                'content' => 'Test page content',
                'status' => 1,
                'language_id' => 1,
                'metadata' => ['navigation_hidden' => 0],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Page::where('permalink_slug', 'about-us')->exists())->toBeFalse()
        ->and(Page::where('permalink_slug', 'test-page')->exists())->toBeTrue();
});

it('deletes static page', function(): void {
    $page = Page::firstWhere('permalink_slug', 'about-us');

    actingAsSuperUser()
        ->post(route('igniter.pages.pages', ['slug' => 'edit/'.$page->getKey()]), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(Page::where('page_id', $page->getKey())->exists())->toBeFalse();
});
