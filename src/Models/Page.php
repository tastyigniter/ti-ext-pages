<?php

namespace Igniter\Pages\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Database\Traits\Sortable;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Main\Template\Layout;
use Igniter\System\Models\Concerns\Switchable;
use Illuminate\Support\Collection;

/**
 * Pages Model Class
 *
 * @property int $page_id
 * @property int $language_id
 * @property string $title
 * @property string $content
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property bool $status
 * @property string|null $permalink_slug
 * @property string|null $layout
 * @property array|null $metadata
 * @property int|null $priority
 * @mixin \Igniter\Flame\Database\Model
 */
class Page extends Model
{
    use HasPermalink;
    use Sortable;
    use Switchable;

    public const SORT_ORDER = 'priority';

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
        'status' => 'boolean',
    ];

    public $relation = [
        'belongsTo' => [
            'language' => \Igniter\System\Models\Language::class,
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
        if (!is_null(self::$pagesCache)) {
            return self::$pagesCache;
        }

        return self::$pagesCache = static::whereIsEnabled()->orderBy('title')->get();
    }

    public function getLayoutOptions()
    {
        $result = [];
        $theme = resolve(ThemeManager::class)->getActiveTheme();
        $layouts = Layout::listInTheme($theme, true);
        foreach ($layouts as $layout) {
            $baseName = $layout->getBaseFileName();
            $result[$baseName] = strlen($layout->description) ? $layout->description : $baseName;
        }

        return $result;
    }

    public function getLayoutObject()
    {
        if (!$layoutId = $this->layout) {
            $layouts = $this->getLayoutOptions();
            $layoutId = count($layouts) ? array_keys($layouts)[0] : null;
        }

        if (!$layoutId) {
            return null;
        }

        if (!$layout = Layout::load($this->theme, $layoutId)) {
            return null;
        }

        return $layout;
    }

    public function getContentAttribute($value)
    {
        return html_entity_decode($value);
    }
}
