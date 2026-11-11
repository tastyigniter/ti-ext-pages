<?php

declare(strict_types=1);

namespace Igniter\Pages\Classes;

use Igniter\Flame\Exception\ApplicationException;
use Igniter\Flame\Pagic\Model;
use Igniter\Flame\Support\RouterHelper;
use Igniter\Main\Classes\PageBuilder;
use Igniter\Main\Classes\Theme;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Main\Template\Page as PageTemplate;
use Igniter\Pages\Models\Page as PageModel;
use Illuminate\Support\Facades\Lang;
use Stevebauman\Purify\Facades\Purify;

class PageManager
{
    /**
     * @var Theme
     */
    protected $theme;

    public function initPage($url): ?Model
    {
        $staticPage = $this->findByUrl($url);

        if (!$staticPage) {
            return null;
        }

        $page = $this->makePage($staticPage);
        $page->permalink = $url;
        $page['staticPage'] = $staticPage;

        $this->applySettingsFromAttributes($page, $staticPage);

        return $page;
    }

    public function getPageContents(null|array|PageTemplate $page)
    {
        if (!isset($page['staticPage'])) {
            return null;
        }

        return $page['staticPage']->content;
    }

    /**
     * Returns the HTML to render for a static page: its page builder sections
     * when the page uses the builder and the theme supports it, otherwise its
     * cleaned content.
     */
    public function renderPageContents(?Theme $theme, null|array|PageTemplate $page): ?string
    {
        if (!isset($page['staticPage'])) {
            return null;
        }

        $staticPage = $page['staticPage'];

        if ($theme instanceof Theme && $staticPage->usesPageBuilder()) {
            $builder = resolve(PageBuilder::class);
            if (view()->exists($builder->getViewNamespace($theme).'::_builder.section')) {
                return $builder->renderBlade(
                    $theme,
                    $builder->normalize($theme, $staticPage->sections ?? [], PageModel::BUILDER_PAGE_CODE),
                    PageModel::BUILDER_PAGE_CODE,
                );
            }
        }

        return ($contents = $staticPage->content) ? Purify::clean($contents) : null;
    }

    public function listPageSlugs()
    {
        return PageModel::query()->whereIsEnabled()->dropdown('permalink_slug');
    }

    protected function findByUrl($url)
    {
        $url = ltrim(RouterHelper::normalizeUrl($url), '/');

        return PageModel::query()
            ->whereIsEnabled()
            ->where('permalink_slug', $url)
            ->first();
    }

    protected function makePage($staticPage): Page
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

    protected function applySettingsFromAttributes($page, $staticPage)
    {
        $settings = $page->settings;

        $settings['id'] = str_replace('/', '-', $staticPage->permalink_slug);
        $settings['title'] = $staticPage->title;
        $settings['layout'] = $staticPage->layout ?? 'static';
        $settings['description'] = $staticPage->meta_description;
        $settings['keywords'] = $staticPage->meta_keywords;
        $settings['is_hidden'] = !$staticPage->status;

        $page->settings = $settings;
    }
}
