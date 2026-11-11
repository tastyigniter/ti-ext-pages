<?php

declare(strict_types=1);

namespace Igniter\Pages\Models;

use Igniter\Flame\Database\Builder;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Database\Traits\Sortable;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Main\Template\Layout;
use Igniter\System\Models\Concerns\Switchable;
use Igniter\System\Models\Language;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stevebauman\Purify\Facades\Purify;

/**
 * Pages Model Class
 *
 * @property int $page_id
 * @property null|int $language_id
 * @property string $title
 * @property string $content
 * @property string $editor
 * @property array|null $sections
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $status
 * @property string|null $permalink_slug
 * @property string|null $layout
 * @property array|null $metadata
 * @property int|null $priority
 * @method static Builder<static>|Page query()
 * @method static Builder<static>|Page whereIsEnabled()
 * @method static Builder<static>|Page whereSlug(?string $slug)
 * @mixin Model
 */
class Page extends Model
{
    public const string EDITOR_CONTENT = 'content';

    public const string EDITOR_BUILDER = 'builder';

    /** Page code used for static pages in the page builder. */
    public const string BUILDER_PAGE_CODE = 'static';

    use HasPermalink;
    use Sortable;
    use Switchable;

    public const string SORT_ORDER = 'priority';

    /**
     * @var string The database table name
     */
    protected $table = 'pages';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'page_id';

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'language_id' => 'integer',
        'metadata' => 'json',
        'sections' => 'array',
        'status' => 'boolean',
    ];

    public $relation = [
        'belongsTo' => [
            'language' => Language::class,
        ],
    ];

    protected $permalinkable = [
        'permalink_slug' => [
            'source' => 'title',
        ],
    ];

    public static ?Collection $pagesCache = null;

    public static function getDropdownOptions()
    {
        return static::whereIsEnabled()->dropdown('title');
    }

    public static function loadPages()
    {
        return self::$pagesCache ??= static::whereIsEnabled()->orderBy('title')->get();
    }

    public function getLayoutOptions()
    {
        $result = [];
        $theme = resolve(ThemeManager::class)->getActiveTheme();
        $layouts = Layout::listInTheme($theme, true);
        foreach ($layouts as $layout) {
            $baseName = $layout->getBaseFileName();
            $result[$baseName] = strlen((string)$layout->description) !== 0 ? $layout->description : $baseName;
        }

        return $result;
    }

    public function getLayoutObject()
    {
        if (!$layoutId = $this->layout) {
            $layouts = $this->getLayoutOptions();
            $layoutId = count($layouts) > 0 ? array_keys($layouts)[0] : null;
        }

        if (!$layoutId) {
            return null;
        }

        $themeCode = resolve(ThemeManager::class)->getActiveThemeCode();
        if (!$layout = Layout::load($themeCode, $layoutId)) {
            return null;
        }

        return $layout;
    }

    public function getEditorOptions(): array
    {
        return [
            self::EDITOR_CONTENT => 'lang:igniter.pages::default.text_editor_content',
            self::EDITOR_BUILDER => 'lang:igniter.pages::default.text_editor_builder',
        ];
    }

    /**
     * Whether the page is built with page builder sections instead of content.
     */
    public function usesPageBuilder(): bool
    {
        return $this->editor === self::EDITOR_BUILDER;
    }

    public function getContentAttribute($value): string
    {
        return html_entity_decode(Purify::clean((string)$value));
    }

    public function setContentAttribute($value): void
    {
        $this->attributes['content'] = Purify::clean((string)$value);
    }
}
