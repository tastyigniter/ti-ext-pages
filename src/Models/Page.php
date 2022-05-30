<?php

namespace Igniter\Pages\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Database\Traits\Sortable;
use Igniter\Main\Classes\ThemeManager;
use Igniter\Main\Template\Layout;

/**
 * Pages Model Class
 */
class Page extends Model
{
    use HasPermalink;
    use Sortable;

    const SORT_ORDER = 'priority';

    /**
     * @var string The database table name
     */
    protected $table = 'pages';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'page_id';

    /**
     * @var array The model table column to convert to dates on insert/update
     */
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

    public static function getDropdownOptions()
    {
        return static::isEnabled()->dropdown('title');
    }

    //
    // Scopes
    //

    /**
     * Scope a query to only include enabled page
     *
     * @param $query
     *
     * @return $this
     */
    public function scopeIsEnabled($query)
    {
        return $query->where('status', 1);
    }

    public function getLayoutOptions()
    {
        $result = [];
        $theme = resolve(ThemeManager::class)->getActiveTheme();
        $layouts = Layout::listInTheme($theme, true);
        foreach ($layouts as $layout) {
            if (!$layout->hasComponent('staticPage')) continue;

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

        if (!$layoutId)
            return null;

        if (!$layout = Layout::load($this->theme, $layoutId))
            return null;

        return $layout;
    }

    public function getContentAttribute($value)
    {
        return html_entity_decode($value);
    }
}
