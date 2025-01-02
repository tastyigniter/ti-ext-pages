<?php

namespace Igniter\Pages\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Main\Models\Theme;
use Igniter\Pages\Classes\MenuManager;

/**
 * Menu Model
 *
 * @property int $id
 * @property string $theme_code
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $theme_name
 * @mixin \Igniter\Flame\Database\Model
 */
class Menu extends Model
{
    use Purgeable;

    public $table = 'igniter_pages_menus';

    protected $fillable = [];

    public $timestamps = true;

    /**
     * @var array Relations
     */
    public $relation = [
        'hasMany' => [
            'items' => [MenuItem::class],
        ],
        'belongsTo' => [
            'theme' => [Theme::class, 'foreignKey' => 'theme_code', 'otherKey' => 'code'],
        ],
    ];

    protected $purgeable = ['items'];

    public static function syncAll()
    {
        $dbMenus = self::select('id', 'theme_code', 'code')->get();

        $manager = resolve(MenuManager::class);
        foreach ($manager->getMenusConfig() as $config) {
            if ($dbMenus->where('code', $config['code'])->where('theme_code', $config['themeCode'])->count()) {
                continue;
            }

            $menu = new static;
            $menu->code = $config['code'];
            $menu->name = $config['name'];
            $menu->theme_code = $config['themeCode'];
            $menu->save();

            $menu->createMenuItemsFromConfig(array_get($config, 'items', []));
        }
    }

    public function getThemeCodeOptions()
    {
        return Theme::all()->pluck('name', 'code')->toArray();
    }

    public function addMenuItems($items)
    {
        $id = $this->getKey();
        $idsToKeep = [];
        foreach ($items as $item) {
            $item['menu_id'] = $id;
            $menuItem = $this->items()->firstOrNew([
                'id' => array_get($item, 'id'),
            ])->fill(array_except($item, ['id']));

            $menuItem->saveOrFail();
            $idsToKeep[] = $menuItem->getKey();
        }

        $this->items()->whereNotIn('id', $idsToKeep)->delete();

        return count($idsToKeep);
    }

    public function createMenuItemsFromConfig($items)
    {
        $iterator = function($items, $parentId = null) use (&$iterator) {
            foreach ($items as $item) {
                if ($item['type'] == 'static-page' && !is_numeric($item['reference'])) {
                    $item['reference'] = ($page = Page::whereSlug($item['reference'])->first())
                        ? $page->getKey() : $item['reference'];
                }

                $item['config'] = array_except($item, [
                    'code', 'title', 'description', 'type', 'url', 'reference', 'items',
                ]);

                $item['parent_id'] = $parentId;
                $menuItem = $this->items()->create($item);

                $iterator(array_get($item, 'items', []), $menuItem->id);
            }
        };

        $iterator($items);
    }

    public function getThemeNameAttribute($value)
    {
        return optional($this->theme)->name;
    }
}
