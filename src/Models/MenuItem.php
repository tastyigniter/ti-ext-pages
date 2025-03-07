<?php

declare(strict_types=1);

namespace Igniter\Pages\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\NestedTree;
use Igniter\Flame\Database\Traits\Sortable;
use Igniter\Flame\Database\Traits\Validation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Kalnoy\Nestedset\Collection;

/**
 * MenuItem Model
 *
 * @property int $id
 * @property int $menu_id
 * @property int|null $parent_id
 * @property string $title
 * @property string $code
 * @property string|null $description
 * @property string $type
 * @property string|null $url
 * @property string|null $reference
 * @property array|null $config
 * @property int|null $nest_left
 * @property int|null $nest_right
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, MenuItem> $children
 * @property-read int|null $children_count
 * @property-read mixed $summary
 * @property-read MenuItem|null $parent
 * @method static MenuItem sorted()
 * @method static Collection<int, MenuItem> get()
 * @mixin Model
 */
class MenuItem extends Model
{
    use NestedTree;
    use Sortable;
    use Validation;

    const SORT_ORDER = 'priority';

    public $table = 'igniter_pages_menu_items';

    protected $fillable = [
        'code',
        'title',
        'description',
        'menu_id',
        'parent_id',
        'priority',
        'type',
        'url',
        'reference',
        'config',
    ];

    public $timestamps = true;

    protected $casts = [
        'parent_id' => 'integer',
        'config' => 'array',
    ];

    public $relation = [
        'belongsTo' => [
            'menu' => [Menu::class],
            'parent' => [MenuItem::class, 'foreignKey' => 'parent_id', 'otherKey' => 'id'],
        ],
    ];

    public $rules = [
        ['type', 'igniter.pages::default.menu.label_type', 'required|string'],
        ['code', 'igniter.pages::default.menu.label_code', 'alpha_dash'],
        ['title', 'igniter.pages::default.menu.label_title', 'max:128'],
        ['description', 'admin::lang.label_description', 'max:255'],
        ['parent_id', 'igniter.pages::default.menu.label_parent_id', 'nullable|integer'],
        ['url', 'igniter.pages::default.menu.label_url', 'max:500'],
    ];

    public static function getTypeInfo($type)
    {
        $result = [];
        $response = Event::dispatch('pages.menuitem.getTypeInfo', [$type]);

        if (is_array($response)) {
            foreach ($response as $typeInfo) {
                if (!is_array($typeInfo)) {
                    continue;
                }

                foreach ($typeInfo as $name => $value) {
                    $result[$name] = $value;
                }
            }
        }

        return $result;
    }

    public function getTypeOptions()
    {
        $result = [
            'url' => 'URL',
            'header' => 'Header',
        ];

        $response = Event::dispatch('pages.menuitem.listTypes');

        if (is_array($response)) {
            foreach ($response as $typeList) {
                if (!is_array($typeList)) {
                    continue;
                }

                foreach ($typeList as $typeCode => $typeName) {
                    $result[$typeCode] = $typeName;
                }
            }
        }

        return $result;
    }

    public function getParentIdOptions()
    {
        return self::query()
            ->select('id', 'title')
            ->get()
            ->filter(fn(self $model): bool => $model->id !== $this->id)
            ->mapWithKeys(fn(self $model) => [$model->id => $model->title]);
    }

    public function getSummaryAttribute($value): string
    {
        $summary = '';
        if ($this->parent) {
            $summary .= 'Parent: '.$this->parent->title.' ';
        }

        return $summary.('Type: '.$this->type);
    }
}
