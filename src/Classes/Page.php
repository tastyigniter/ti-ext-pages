<?php

namespace Igniter\Pages\Classes;

use Igniter\Main\Classes\Theme;
use Igniter\Pages\Models\Page as PageModel;
use Illuminate\Support\Facades\URL;

class Page extends \Igniter\Main\Template\Page
{
    /**
     * Handler for the pages.menuitem.getTypeInfo event.
     */
    public static function getMenuTypeInfo(string $type): ?array
    {
        if ($type == 'static-page') {
            return [
                'references' => self::listStaticPageMenuOptions(),
            ];
        }

        return [];
    }

    /**
     * Handler for the pages.menuitem.resolveItem event.
     * @param \Igniter\Pages\Models\MenuItem $item
     * @return ?array
     */
    public static function resolveMenuItem($item, string $url, Theme $theme): ?array
    {
        $pages = PageModel::loadPages();

        if ($item->type == 'static-page') {
            $pages->where('page_id', $item->reference);
        }

        if ($pages->isEmpty()) {
            return null;
        }

        $result = [];

        if ($item->type == 'static-page') {
            $page = $pages->first();
            $result['url'] = URL::to($page->permalink_slug);
            $result['isActive'] = rawurldecode($result['url']) === rawurldecode($url);
        } else {
            $items = [];
            foreach ($pages as $page) {
                if (array_get($page->metadata, 'navigation_hidden', false)) {
                    continue;
                }

                $pageUrl = URL::to($page->permalink_slug);
                $items[] = [
                    'title' => $page->title,
                    'url' => $pageUrl,
                    'isActive' => rawurldecode($pageUrl) === rawurldecode($url),
                ];
            }
            $result['items'] = $items;
        }

        return $result;
    }

    protected static function listStaticPageMenuOptions()
    {
        $references = [];

        $pages = Page::whereIsEnabled()->orderBy('title')->get();
        foreach ($pages as $page) {
            $references[$page->page_id] = $page->title;
        }

        return $references;
    }
}
