<?php

namespace Igniter\Pages\Tests\Classes;

use Igniter\Main\Models\Theme;
use Igniter\Pages\Classes\PageManager;

beforeEach(function() {
    Theme::syncAll();
});

it('initializes page with valid URL', function() {
    $url = 'about-us';

    $page = (new PageManager)->initPage($url);

    expect($page)->not->toBeNull()
        ->and($page->permalink)->toEqual($url)
        ->and($page['staticPage']->content)->not->toBeEmpty();
});

it('returns null for uninitialized page with invalid URL', function() {
    $url = 'invalid-url';

    expect((new PageManager)->initPage($url))->toBeNull();
});

it('gets page contents for initialized page', function() {
    $url = 'about-us';

    $pageManager = new PageManager();

    $page = $pageManager->initPage($url);

    expect($pageManager->getPageContents($page))->not->toBeEmpty();
});

it('lists page slugs for enabled pages', function() {
    $slugs = (new PageManager)->listPageSlugs();

    expect($slugs)->toContain('about-us', 'policy', 'terms-and-conditions');
});
