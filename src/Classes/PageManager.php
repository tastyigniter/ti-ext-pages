<?php

namespace Igniter\Pages\Classes;

use Igniter\Flame\Exception\ApplicationException;
use Igniter\Flame\Support\RouterHelper;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Pages\Models\Page as PageModel;
use Illuminate\Support\Facades\Lang;

class PageManager
{
    /**
     * @var \Igniter\Main\Classes\Theme
     */
    protected $theme;

    public function initPage($url)
    {
        $staticPage = $this->findByUrl($url);

        if (!$staticPage) {
            return null;
        }

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

    public function listPageSlugs()
    {
        return PageModel::whereIsEnabled()->dropdown('permalink_slug');
    }

    protected function findByUrl($url)
    {
        $url = ltrim(RouterHelper::normalizeUrl($url), '/');

        $query = PageModel::query()->isEnabled();

        return $query->where('permalink_slug', $url)->first();
    }

    protected function makePage($staticPage)
    {
        if (!$theme = resolve(ThemeManager::class)->getActiveTheme()) {
            throw new ApplicationException(Lang::get('igniter::main.not_found.active_theme'));
        }

        return Page::on($theme->getName())->newFromFinder([
            'fileName' => $staticPage->permalink_slug,
            'mTime' => $staticPage->updated_at->timestamp,
            'content' => $staticPage->content,
            'markup' => $staticPage->content,
            'code' => null,
        ]);
    }

    protected function fillSettingsFromAttributes($page, $staticPage)
    {
        $settings['id'] = str_replace('/', '-', $staticPage->permalink_slug);
        $settings['title'] = $staticPage->title;
        $settings['layout'] = $staticPage->layout ?? 'static';
        $settings['description'] = $staticPage->meta_description;
        $settings['keywords'] = $staticPage->meta_keywords;
        $settings['is_hidden'] = !(bool)$staticPage->status;

        $page->settings = $settings;
    }
}
