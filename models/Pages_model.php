<?php namespace Igniter\Pages\Models;

use Igniter\Flame\ActivityLog\Traits\LogsActivity;

/**
 * Pages Model Class
 *
 * @package Admin
 */
class Pages_model extends \System\Models\Pages_model
{
    use LogsActivity;

    protected $fillable = ['language_id', 'name', 'title', 'heading', 'content', 'meta_description',
        'meta_keywords', 'layout_id', 'navigation', 'date_added', 'date_updated', 'status'];

    public function getContentAttribute($value)
    {
        return html_entity_decode($value);
    }

    public function getMessageForEvent($eventName)
    {
        return $eventName.' page <b>:subject.name</b>';
    }

    public function getMorphClass()
    {
        return 'pages';
    }
}