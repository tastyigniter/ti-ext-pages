<?php

namespace Igniter\Pages\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Database\Traits\Sortable;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Main\Template\Layout;
use Igniter\System\Models\Concerns\Switchable;
use Igniter\System\Models\Language;
use Illuminate\Support\Collection;

/**
 * Pages Model Class
 */
class Page extends Model
{
    use HasPermalink;
    use Sortable;
    use Switchable;

    const SORT_ORDER = 'priority';

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

    protected static ?Collection $pagesCache = null;

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

    public function beforeSave()
    {
        if (is_null($this->language_id)) {
            $this->language_id = Language::getDefault()->getKey();
        }
    }
}
