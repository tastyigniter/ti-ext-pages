<?php

namespace Igniter\Pages\Classes;

use Igniter\Flame\Exception\ApplicationException;
use Igniter\Flame\Support\RouterHelper;
use Igniter\Flame\Traits\Singleton;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Pages\Models\Page;
use Illuminate\Support\Facades\Lang;

class PageManager
{
    use Singleton;

    /**
     * @var \Igniter\Main\Classes\Theme
     */
    protected $theme;

    protected function initialize()
    {
        $this->theme = resolve(ThemeManager::class)->getActiveTheme();
        if (!$this->theme)
            throw new ApplicationException(Lang::get('main::lang.not_found.active_theme'));
    }

    public function initPage($url)
    {
        $staticPage = $this->findByUrl($url);

        if (!$staticPage)
            return null;

        $page = $this->makePage($staticPage);
        $page->permalink = $url;
        $page['staticPage'] = $staticPage;

        $this->fillSettingsFromAttributes($page, $staticPage);

        return $page;
    }

    public function getPageContents($page)
    {
        if (!isset($page['staticPage'])) {
            return;
        }

        return $page['staticPage']->content;
    }

    protected function findByUrl($url)
    {
        $url = ltrim(RouterHelper::normalizeUrl($url), '/');

        $query = Page::isEnabled();

        return $query->where('permalink_slug', $url)->first();
    }

    protected function makePage($staticPage)
    {
        return Page::inTheme($this->theme)->newFromFinder([
            'fileName' => $staticPage->permalink_slug,
            'mTime' => $staticPage->updated_at->timestamp,
            'content' => $staticPage->content,
            'markup' => $staticPage->content,
            'code' => null,
        ]);
    }

    protected function fillSettingsFromAttributes($page, $staticPage)
    {
        $page->settings['id'] = str_replace('/', '-', $staticPage->permalink_slug);
        $page->settings['title'] = $staticPage->title;
        $page->settings['layout'] = $staticPage->layout ?? 'static';
        $page->settings['description'] = $staticPage->meta_description;
        $page->settings['keywords'] = $staticPage->meta_keywords;
        $page->settings['is_hidden'] = !(bool)$staticPage->status;
    }

    protected function getCacheKey($keyName)
    {
        return crc32($this->theme->getPath()).$keyName;
    }
}
